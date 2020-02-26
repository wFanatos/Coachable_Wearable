/*
 * PROGRAMMER:  William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Metrics.h"

Metrics::Metrics() {
  runCount = 0;
  numSavedRuns = 0;
  date = "";
  startTime = "";
  startAltitude = 0.0f;
  sumSpeed = 0.0f;
  numSamples = 0;
  startLat = 0.0f;
  startLon = 0.0f;
  startMillis = 0;
  runOngoing = false;
  sdInfoRead = false;
}

Metrics::~Metrics() {}


// Stores start of run info
void Metrics::StartRun(String date, String time, float altitude, float lat, char latDir, float lon, char lonDir) {
  if (runOngoing) {
    return;
  }
  
  this->date = date;
  startTime = time;
  startAltitude = altitude;
  sumSpeed = 0.0f;
  numSamples = 0;
  incrementalData = "";
  
  startLat = lat;
  if (latDir == 'S') {
    startLat *= -1.0f;
  }
  
  startLon = lon;
  if (lonDir == 'W') {
    startLon *= -1.0f;
  }

  startMillis = millis();
  runOngoing = true;
}


// Store end of run data
void Metrics::FinishRun(String time, float altitude, float lat, char latDir, float lon, char lonDir, fs::FS &fs, bool useSD) {
  if (!runOngoing) {
    return;
  }
  
  float duration = (millis() - startMillis) / 1000.0f;
  
  if (duration < MIN_DURATION) {
    runOngoing = false;
    return;
  }
  
  if (latDir == 'S') {
    lat *= -1.0f;
  }
  
  if (lonDir == 'W') {
    lon *= -1.0f;
  }
  
  // jsonData[runCount] = "{\"RunNumber\": " + String(runCount + 1) + ",";
  jsonData[runCount] += "{\"Duration\": " + String(duration) + ",";
  jsonData[runCount] += "\"Date\": " + date + ",";
  jsonData[runCount] += "\"StartTime\": " + startTime + ",";
  jsonData[runCount] += "\"EndTime\": " + time + ",";
  jsonData[runCount] += "\"StartAltitude\": " + String(startAltitude) + ",";
  jsonData[runCount] += "\"EndAltitude\": " + String(altitude) + ",";
  jsonData[runCount] += "\"AvgSpeed\": " + String(sumSpeed / numSamples) + ",";
  jsonData[runCount] += "\"Distance\": " + String(CalcDistance(startLat, startLon, lat, lon)) + ",";
  jsonData[runCount] += "\"Data\": [" + incrementalData + "]}";

  runCount++;
  runOngoing = false;
  
  if (useSD) {
    SaveData(fs);
  }
}


// Adds a speed to the speed sum
void Metrics::AddSpeedSample(float speed) {
  sumSpeed += speed;
  numSamples++;
}


// Adds a data sample
void Metrics::AddDataSample(float lat, char latDir, float lon, float lonDir, float spd, float alt, String time) {
  if (incrementalData != "") {
    incrementalData += ",";
  }
  
  if (latDir == 'S') {
    lat *= -1.0f;
  }
  
  if (lonDir == 'W') {
    lon *= -1.0f;
  }
  
  incrementalData += "{\"Latitude\": " + String(lat) + ",";
  incrementalData += "\"Longitude\": " + String(lon) + ",";
  incrementalData += "\"Speed\": " + String(spd) + ",";
  incrementalData += "\"Altitude\": " + String(alt) + ",";
  incrementalData += "\"Time\": " + time + "}";
}


// Clears all json data
void Metrics::ClearJson(fs::FS &fs, bool useSD) {
  // Clear SD
  if (useSD) {
    fs.remove(JSON_PATH);
    numSavedRuns = 0;
    UpdateSDInfo(fs);
  }
  
  // Clear memory
  for (int i = 0; i < runCount; i++) {
    jsonData[i] = "";  
  }
  
  runCount = 0;
}


// Returns the JSON string
String Metrics::GetJsonStr(fs::FS &fs, bool useSD) {
  String jsonStr = "{ \"Runs\": [";
  
  if (useSD) {
    if (!sdInfoRead) {
      GetSDInfo(fs);
      sdInfoRead = true;
    }
    
    if (numSavedRuns > 0) {
      File file = fs.open(JSON_PATH);
      while (file.available()) {
        jsonStr += file.read();
      }
      file.close();
    }
  }
  
  for (int i = 0; i < runCount; i++) {
    if (i != 0 || numSavedRuns > 0) {
      jsonStr += ",";
    }
    jsonStr += jsonData[i];
  }
  
  jsonStr += "]}";
  
  return jsonStr;
}


// Returns number of saved runs
int Metrics::GetNumSavedRuns() {
  return numSavedRuns + runCount;
}


// Returns whether a run is ongoing
bool Metrics::IsRunOngoing() {
  return runOngoing;
}


// Converts degrees to radians
float Metrics::DegToRad(float deg) {
  return deg * PI / 180;
}


// Calculates the distance between two sets of latitude and longitude
float Metrics::CalcDistance(float lat1, float lon1, float lat2, float lon2) {
  const float earthRadiusKm = 6371.0f;

  float dLat = DegToRad(lat2 - lat1);
  float dLon = DegToRad(lon2 - lon1);

  lat1 = DegToRad(lat1);
  lat2 = DegToRad(lat2);

  float a = pow(sin(dLat / 2), 2) + pow(sin(dLon / 2), 2) * cos(lat1) * cos(lat2);
  float c = 2 * atan2(sqrt(a), sqrt(1 - a));

  return earthRadiusKm * c;
}


// Saves the current JSON data to the json data file
void Metrics::SaveData(fs::FS &fs) {
  if (!sdInfoRead) {
    GetSDInfo(fs);
    sdInfoRead = true;
  }
  
  for (int i = 0; i < runCount; i++) {
    File file = fs.open(JSON_PATH, FILE_APPEND);
    
    if (numSavedRuns != 0) {
      file.print(",\n");
    }
    
    file.print(jsonData[i]);
    file.close();
    
    jsonData[i] = "";
    numSavedRuns++;
  }
  
  UpdateSDInfo(fs);
  
  runCount = 0;
}


// Gets the number of runs saved on the SD
void Metrics::GetSDInfo(fs::FS &fs) {
  if (fs.exists(INFO_PATH)) {
    String s = "";
    File file = fs.open(INFO_PATH);
    while (file.available()) {
      s += file.read();
    }
    file.close();
    numSavedRuns = s.toInt();
  }
}


// Updates the number of runs saved on the SD
void Metrics::UpdateSDInfo(fs::FS &fs) {
  File file = fs.open(INFO_PATH, FILE_WRITE);
  file.print(String(numSavedRuns));
  file.close();
}
