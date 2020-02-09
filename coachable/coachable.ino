/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 9, 2020
 * DESCRIPTION:   Tracks skiing metrics
 */

#include <ArduinoJson.h>
#include <Adafruit_GPS.h>
#include <string>

#define NUM_RUNS 50
#define NUM_METRICS 9
#define GPSSerial Serial2

struct RunMetrics {
  String date;
  String startTime;
  float startAltitude;
  float sumSpeed;
  int numSamples;
  float startLat;
  float startLon;
};

uint32_t timer = millis();
bool runOngoing = false;
int runCount = 0;
int startMillis = 0;

const int capacity = JSON_ARRAY_SIZE(NUM_RUNS) + NUM_RUNS * JSON_OBJECT_SIZE(NUM_METRICS);
DynamicJsonDocument metricsDoc(capacity);

struct RunMetrics metrics;
Adafruit_GPS GPS(&GPSSerial);

// Initialize
void setup() {
  Serial.begin(115200);

  // Init GPS
  GPS.begin(9600);
  GPS.sendCommand(PMTK_SET_NMEA_OUTPUT_RMCGGA);
  GPS.sendCommand(PMTK_SET_NMEA_UPDATE_1HZ);
}

// Main loop, runs repeatedly
void loop() {
  // Catch when millis wraps and reset timer
  if (millis() < timer) {
    timer = millis();
  }

  // If GPS is working, runs every ~2 secs
  if (readGPS() && GPS.fix && millis() - timer >= 2000) {
    timer = millis();
    
    Serial.print("Time: "); Serial.println(getTime());
    Serial.print("Date: "); Serial.println(getDate());
    Serial.print("Speed (m/s): "); Serial.println(GPS.speed);
    Serial.print("Altitude: "); Serial.println(GPS.altitude);

    if (!runOngoing) {
      // TODO: detect run start
    }
    else {
      // TODO: detect run stop
      
      metrics.sumSpeed += GPS.speed;
      metrics.numSamples++;
    }
  }
}


// Check GPS for data
bool readGPS() {
  char c = GPS.read();

  if (GPS.newNMEAreceived()) {
    if (!GPS.parse(GPS.lastNMEA())) {
      return false;
    }
  }

  return true;
}


// Gets date string from GPS in ISO format yyyy-mm-dd
String getDate() {
  String date = GPS.year + "-";
  if (GPS.month < 10) { date += "0"; }
  date += GPS.month + "-";
  if (GPS.day < 10) { date += "0"; }
  date += GPS.day;

  return date;
}


// Gets time string from GPS in 24h format UTC time
String getTime() {
  String time = "";
  if (GPS.hour < 10) { time += "0"; }
  time += GPS.hour + ":";
  if (GPS.minute < 10) { time += "0"; }
  time += GPS.minute + ":";
  if (GPS.seconds < 10) { time += "0"; }
  time += GPS.seconds;
  time += " UTC";

  return time;
}


// Converts degrees to radians
float degToRad(float deg) {
  return deg * PI / 180;
}


// Calculates the distance between two sets of latitude and longitude
float calcDistance(float lat1, float lon1, float lat2, float lon2) {
  const int earthRadiusKm = 6371;

  float dLat = degToRad(lat2 - lat1);
  float dLon = degToRad(lon2 - lon1);

  lat1 = degToRad(lat1);
  lat2 = degToRad(lat2);

  float a = pow(sin(dLat / 2), 2) + pow(sin(dLon / 2), 2) * cos(lat1) * cos(lat2);
  float c = 2 * atan2(sqrt(a), sqrt(1 - a));

  return earthRadiusKm * c;
}


// Stores start of run info
void startRun() {
  metrics.date = getDate();
  metrics.startTime = getTime();
  metrics.startAltitude = GPS.altitude;
  metrics.sumSpeed = 0.0f;
  metrics.numSamples = 0;
  
  metrics.startLat = GPS.latitude;
  if (GPS.lat == 'S') {
    metrics.startLat *= -1.0f;
  }
  
  metrics.startLon = GPS.longitude;
  if (GPS.lon == 'W') {
    metrics.startLon *= -1.0f;
  }

  startMillis = millis();
}


// Store data in json document
void finishRun() {
  float duration = (millis() - startMillis) / 1000.0f;

  float lat = GPS.latitude;
  if (GPS.lat == 'S') {
    lat *= -1.0f;
  }
  
  float lon = GPS.longitude;
  if (GPS.lon == 'W') {
    lon *= -1.0f;
  }
  
  JsonObject obj = metricsDoc.createNestedObject();
  obj["RunNumber"] = runCount + 1;
  obj["Duration"] = duration;
  obj["Date"] = metrics.date;
  obj["StartTime"] = metrics.startTime;
  obj["EndTime"] = getTime();
  obj["StartAltitude"] = metrics.startAltitude;
  obj["EndAltitude"] = GPS.altitude;
  obj["AvgSpeed"] = metrics.sumSpeed / metrics.numSamples;
  obj["Distance"] = calcDistance(metrics.startLat, metrics.startLon, lat, lon);

  runCount++;
}
