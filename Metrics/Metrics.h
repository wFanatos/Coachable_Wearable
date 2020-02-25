/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Arduino.h"
#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include <ArduinoJson.h>
#include <string>

#define NUM_METRICS 12
#define MIN_DURATION 5
#define JSON_PATH "/data.json"

class Metrics {
public:
  Metrics();
  ~Metrics();
  
  void StartRun(String date, String time, float altitude, float lat, char latDir, float lon, char lonDir);
  void FinishRun(String time, float altitude, float lat, char latDir, float lon, char lonDir, fs::FS &fs);
  void AddSpeedSample(float speed);
  void ClearJson(fs::FS &fs);
  String GetJsonStr(fs::FS &fs);
  int GetNumSavedRuns();
  bool IsRunOngoing();
  
  float sumSpeed;
  
private:
  float DegToRad(float deg);
  float CalcDistance(float lat1, float lon1, float lat2, float lon2);
  void SaveData(fs::FS &fs);
  
  static const int capacity = JSON_OBJECT_SIZE(NUM_METRICS);
  StaticJsonDocument<capacity> metricsDoc;
  
  int runCount;
  int numSavedRuns;
  String date;
  String startTime;
  float startAltitude;
  
  int numSamples;
  float startLat;
  float startLon;
  int startMillis;
  bool runOngoing;
};
