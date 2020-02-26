/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Arduino.h"
#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include <string>

#define NUM_RUNS 100
#define MIN_DURATION 5
#define JSON_PATH "/runs.json"
#define INFO_PATH "/info.dat"

class Metrics {
public:
  Metrics();
  ~Metrics();
  
  void StartRun(String date, String time, float altitude, float lat, char latDir, float lon, char lonDir);
  void FinishRun(String time, float altitude, float lat, char latDir, float lon, char lonDir, fs::FS &fs, bool useSD);
  void AddSpeedSample(float speed);
  void AddDataSample(float lat, char latDir, float lon, float lonDir, float spd, float alt, String time);
  void ClearJson(fs::FS &fs, bool useSD);
  String GetJsonStr(fs::FS &fs, bool useSD);
  int GetNumSavedRuns();
  bool IsRunOngoing();
  
  float sumSpeed;
  
private:
  float DegToRad(float deg);
  float CalcDistance(float lat1, float lon1, float lat2, float lon2);
  void SaveData(fs::FS &fs);
  void GetSDInfo(fs::FS &fs);
  void UpdateSDInfo(fs::FS &fs);
  
  String jsonData[NUM_RUNS];
  String incrementalData;
  
  bool sdInfoRead;
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
