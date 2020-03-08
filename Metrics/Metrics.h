/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 19, 2020
 * DESCRIPTION:   Keeps track of skiing metrics
 */

#include "Arduino.h"
#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include "SPIFFS.h"
#include <string>
#include <vector>
#include <cmath>

#define NUM_RUNS 100
#define MIN_DURATION 5
#define JSON_PATH "/runs.json"
#define INFO_PATH "/info.dat"

struct IncrementalData {
  float lat;
  float lon;
  float spd;
  float alt;
  String time;
  
  IncrementalData(float lat, float lon, float spd, float alt, String time) {
    this->lat = lat;
    this->lon = lon;
    this->spd = spd;
    this->alt = alt;
    this->time = time;
  }
};

class Metrics {
public:
  Metrics();
  ~Metrics();
  
  void Init(fs::FS &sd, bool useSD, fs::FS &spiffs);
  
  void StartRun(String date, String time, float altitude, float lat, float lon);
  void FinishRun(String deviceName, String time, float altitude, float lat, float lon, fs::FS &fs, bool isSD);
  void AddSpeedSample(float speed);
  void AddDataSample(float lat, float lon, float spd, float alt, String time);
  void ClearJson(fs::FS &sd, bool useSD, fs::FS &spiffs);
  String GetJsonStr(fs::FS &sd, bool useSD, fs::FS &spiffs);
  int GetNumSavedRuns();
  bool IsRunOngoing();
  
private:
  float DegToRad(float deg);
  float CalcDistance(float lat1, float lon1, float lat2, float lon2);
  void SaveData(fs::FS &fs, bool isSD);
  int GetInfo(fs::FS &fs);
  void UpdateInfo(fs::FS &fs, int numSavedRuns);
  void ReadInfo(fs::FS &fs, bool isSD);
  String ReadFile(fs::FS &fs, const char* path);
  void WriteFile(fs::FS &fs, const char* path, const char* method, const char* data);
  String GetIncrementalDataJson(float duration);
  
  String jsonData;
  std::vector<IncrementalData> data;
  
  int num;
  
  bool sdInfoRead;
  bool spiffsInfoRead;
  int numSavedRunsSD;
  int numSavedRunsSpiffs;
  
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
