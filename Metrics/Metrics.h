/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Arduino.h"
#include <ArduinoJson.h>
#include <string>

#define NUM_METRICS 12

class Metrics {
public:
  Metrics();
  ~Metrics();
  
  void StartRun(String date, String time, float altitude, float lat, char latDir, float lon, char lonDir);
  void FinishRun(String time, float altitude, float lat, char latDir, float lon, char lonDir);
  void AddSpeedSample(float speed);
  void ClearJson();
  String GetJsonStr();
  int GetNumSavedRuns();
  bool IsRunOngoing();
  
private:
  float DegToRad(float deg);
  float CalcDistance(float lat1, float lon1, float lat2, float lon2);
  void SaveData();
  
  static const int capacity = JSON_OBJECT_SIZE(NUM_METRICS);
  StaticJsonDocument<capacity> metricsDoc;
  
  int runCount;
  int numSavedRuns;
  String date;
  String startTime;
  float startAltitude;
  float sumSpeed;
  int numSamples;
  float startLat;
  float startLon;
  int startMillis;
  bool runOngoing;
};
