/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Metrics.h"

Metrics::Metrics() {
  numSavedRunsSD = 0;
  numSavedRunsSpiffs = 0;
  date = "";
  startTime = "";
  jsonData = "";
  startAltitude = 0.0f;
  sumSpeed = 0.0f;
  numSamples = 0;
  startLat = 0.0f;
  startLon = 0.0f;
  startMillis = 0;
  runOngoing = false;
  sdInfoRead = false;
  spiffsInfoRead = false;
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
void Metrics::FinishRun(String deviceID, String time, float altitude, float lat, char latDir, float lon, char lonDir, fs::FS &fs, bool isSD) {
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
  
  jsonData = "{\"DeviceID\": \"" + deviceID + "\",";
  jsonData += "\"UserID\": 0,";
  jsonData += "\"EventID\": 0,";
  jsonData += "\"Duration\": " + String(duration) + ",";
  jsonData += "\"Date\": \"" + date + "\",";
  jsonData += "\"StartTime\": \"" + startTime + "\",";
  jsonData += "\"EndTime\": \"" + time + "\",";
  jsonData += "\"StartAltitude\": " + String(startAltitude) + ",";
  jsonData += "\"EndAltitude\": " + String(altitude) + ",";
  jsonData += "\"AvgSpeed\": " + String(sumSpeed / numSamples) + ",";
  jsonData += "\"Distance\": " + String(CalcDistance(startLat, startLon, lat, lon)) + ",";
  jsonData += "\"Data\": [" + incrementalData + "]}";

  runOngoing = false;
  
  SaveData(fs, isSD);
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
  incrementalData += "\"Time\": \"" + time + "\"}";
}


// Clears all json data
void Metrics::ClearJson(fs::FS &sd, bool useSD, fs::FS &spiffs) {
  // Clear SD
  if (useSD) {
    sd.remove(JSON_PATH);
    numSavedRunsSD = 0;
    UpdateInfo(sd, numSavedRunsSD);
  }

  spiffs.remove(JSON_PATH);
  numSavedRunsSpiffs = 0;
  UpdateInfo(spiffs, numSavedRunsSpiffs);
}


// Returns the JSON string
String Metrics::GetJsonStr(fs::FS &sd, bool useSD, fs::FS &spiffs) {
  String jsonStr = "{ \"Runs\": [";
  int numRuns = 0;

  // Get data from SD
  if (useSD) {
    ReadInfo(sd, true);
    numRuns += numSavedRunsSD;
    
    if (numSavedRunsSD > 0) {
      jsonStr += ReadFile(sd, JSON_PATH);
    }
  }
  
  // Get data from SPIFFS
  ReadInfo(spiffs, false);

  if (numSavedRunsSpiffs > 0) {
    if (numRuns > 0) {
      jsonStr += ",";
    }
    jsonStr += ReadFile(spiffs, JSON_PATH);
  }
 
  numRuns += numSavedRunsSpiffs;
  
  // Get string data if not saved
  if (jsonData != "") {
    if (numRuns > 0) {
      jsonStr += ",";
    }
    jsonStr += jsonData;
  }
 
  jsonStr += "]}";
  
  return jsonStr;
}


// Returns number of saved runs
int Metrics::GetNumSavedRuns() {
  return numSavedRunsSD + numSavedRunsSpiffs;
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
void Metrics::SaveData(fs::FS &fs, bool isSD) {
  int numRuns = 0;
  
  ReadInfo(fs, isSD);
  if (isSD) {
    numRuns = numSavedRunsSD;
  }
  else {
    numRuns = numSavedRunsSpiffs;
  }
  
  String str = "";
  if (numRuns != 0) {
    str += ",\n";
  }
  str += jsonData;
  WriteFile(fs, JSON_PATH, FILE_APPEND, str.c_str());
  
  numRuns++;
  if (isSD) {
    numSavedRunsSD++;
  }
  else {
    numSavedRunsSpiffs++;
  }
  
  jsonData = "";
  UpdateInfo(fs, numRuns);
}


// Gets the number of runs saved on the SD
int Metrics::GetInfo(fs::FS &fs) {
  int numSavedRuns = 0;
  String s = ReadFile(fs, INFO_PATH);
  numSavedRuns = s.toInt();

  return numSavedRuns;
}


// Updates the number of runs saved on the SD
void Metrics::UpdateInfo(fs::FS &fs, int numSavedRuns) {
  WriteFile(fs, INFO_PATH, FILE_WRITE, String(numSavedRuns).c_str());
}


// Gets info for spiffs and sd
void Metrics::ReadInfo(fs::FS &fs, bool isSD) {
  if (isSD) {
    if (!sdInfoRead) {
      numSavedRunsSD = GetInfo(fs);
      sdInfoRead = true;
    }
  }
  else {
    if (!spiffsInfoRead) {
      numSavedRunsSpiffs = GetInfo(fs);
      spiffsInfoRead = true;
    }
  }
}


// Reads all data from a file
String Metrics::ReadFile(fs::FS &fs, const char* path) {
  String str = "";
  if (fs.exists(path)) {
    File file = fs.open(path);
    while (file.available()) {
      str += String(file.readStringUntil('\n'));
    }
    file.close();
  }

  return str;
}


// Writes a string to a file
void Metrics::WriteFile(fs::FS &fs, const char* path, const char* method, const char* data) {
  File file = fs.open(path, method);
  file.println(data);
  file.close();
}
