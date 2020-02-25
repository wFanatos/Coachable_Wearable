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


// Store data in json document
void Metrics::FinishRun(String time, float altitude, float lat, char latDir, float lon, char lonDir, fs::FS &fs) {
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
  
  // TODO: remove RunNumber
  metricsDoc["RunNumber"] = runCount + 1;
  metricsDoc["Duration"] = duration;
  metricsDoc["Date"] = date;
  metricsDoc["StartTime"] = startTime;
  metricsDoc["EndTime"] = time;
  metricsDoc["StartAltitude"] = startAltitude;
  metricsDoc["EndAltitude"] = altitude;
  metricsDoc["AvgSpeed"] = sumSpeed / numSamples;
  metricsDoc["Distance"] = CalcDistance(startLat, startLon, lat, lon);

  runCount++;
  runOngoing = false;

  SaveData(fs);
  metricsDoc.clear();
}


// Adds a speed to the speed sum
void Metrics::AddSpeedSample(float speed) {
  sumSpeed += speed;
  numSamples++;
}


// Clears all json data
void Metrics::ClearJson(fs::FS &fs) {
  fs.remove(JSON_PATH);
  numSavedRuns = 0;
}


// Returns the JSON string
String Metrics::GetJsonStr(fs::FS &fs) {
  String jsonStr = "";
  
  File file = fs.open(JSON_PATH);
  while (file.available()) {
    jsonStr += file.read();
  }
  file.close();
  
  jsonStr += "]}";
  
  return jsonStr;
}


// Returns number of saved runs
int Metrics::GetNumSavedRuns() {
  return numSavedRuns;
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
  String jsonStr = "";
  serializeJson(metricsDoc, jsonStr);
  
  if (!fs.exists(JSON_PATH)) {
    File file = fs.open(JSON_PATH, FILE_WRITE);
	file.print("{ \"Runs\": [");
	file.close();
  }
  else {
    File file = fs.open(JSON_PATH, FILE_APPEND);
    file.print(",\n");
    file.close();
  }
  
  File file = fs.open(JSON_PATH, FILE_APPEND);
  file.print(jsonStr);
  file.close();

  numSavedRuns++;
}
