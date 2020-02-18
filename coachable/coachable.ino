/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 9, 2020
 * DESCRIPTION:   Tracks skiing metrics
 */

#include <ArduinoJson.h>
#include <Adafruit_GPS.h>
#include <Adafruit_MPL3115A2.h>
#include <string>
#include <HTTPClient.h>

#define NUM_METRICS 12
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
int stopCount = 0;

float lastAltitude = 0.0f;
float currentAltitude = 0.0f;
float lastSpeed = 0.0f;
float currentSpeed = 0.0f;

const int MIN_ALT_DIFF = 1;
const float MIN_SPD_DIFF = 0.25f;

const int capacity = JSON_OBJECT_SIZE(NUM_METRICS);
StaticJsonDocument<capacity> metricsDoc;

struct RunMetrics metrics;
Adafruit_GPS GPS(&GPSSerial);
Adafruit_MPL3115A2 baro = Adafruit_MPL3115A2();

// TODO: remove below
int trackCount = 0;
bool firstRun = true;

// Initialize
void setup() {
  Serial.begin(115200);
  
  // Init GPS
  GPS.begin(9600);
  GPS.sendCommand(PMTK_SET_NMEA_OUTPUT_RMCGGA);
  GPS.sendCommand(PMTK_SET_NMEA_UPDATE_1HZ);
  GPS.sendCommand(PGCMD_ANTENNA);
  delay(1000);
  GPSSerial.println(PMTK_Q_RELEASE);
}

// Main loop, runs repeatedly
void loop() {
  // Get new GPS data
  char c = GPS.read();
  if (GPS.newNMEAreceived()) {
    if (!GPS.parse(GPS.lastNMEA())) {
      return;
    }
  }
  
  // Catch when millis wraps and reset timer
  if (millis() < timer) {
    timer = millis();
  }

  // If GPS is working, runs every ~2 secs
  if (millis() - timer >= 2000) {
    timer = millis();
    Serial.print("Time: "); Serial.println(getTime());
    Serial.print("Date: "); Serial.println(getDate());

    // Get altitude from barometer
    if (baro.begin()) {
      currentAltitude = baro.getAltitude();
      Serial.print("Altitude(m): "); Serial.println(currentAltitude);
    }

    if (GPS.fix) {
      // Convert speed from knots to m/s
      currentSpeed = GPS.speed / 1.944f;
      
      Serial.print("Num Satellites: "); Serial.println((int)GPS.satellites);
      Serial.print("Speed (m/s): "); Serial.println(currentSpeed);
      Serial.print("Latitude: "); Serial.print(GPS.latitude); Serial.println(GPS.lat);
      Serial.print("Longitude: "); Serial.print(GPS.longitude); Serial.println(GPS.lon);

      // TODO: remove below if
      if (firstRun) {
        Serial.println("--RUN START--");
        startRun();
        runOngoing = true;
        firstRun = false;
      }

      if (!runOngoing) {
        if (shouldRunStart()) {
          startRun();
          runOngoing = true;
          Serial.println("--RUN START--");
        }
      }
      else {
//        if (shouldRunStop()) {
//          stopCount++;
//
//          if (stopCount >= 3) {
//            finishRun();
//            runOngoing = false;
//            stopCount = 0;
//            Serial.println("--RUN STOP--");
//          }
//        }
//        else {
//          stopCount = 0;
//        }

        trackCount += 1;
        metrics.sumSpeed += currentSpeed;
        metrics.numSamples++;

        if (trackCount >= 20) {
          Serial.println("--RUN STOP--");
          finishRun();
          runOngoing = false;
          while(1);
        }
      }

      lastSpeed = currentSpeed;
    }
    else {
      Serial.println("Waiting for GPS satellites...");
    }
    
    lastAltitude = currentAltitude;
    Serial.println("");
  }
}


// Gets date string from GPS in ISO format yyyy-mm-dd
String getDate() {
  String date = "20" + String(GPS.year) + "-";
  if (GPS.month < 10) { date += "0"; }
  date += String(GPS.month) + "-";
  if (GPS.day < 10) { date += "0"; }
  date += String(GPS.day);

  return date;
}


// Gets time string from GPS in 24h format UTC time
String getTime() {
  String time = "";
  if (GPS.hour < 10) { time += "0"; }
  time += String(GPS.hour) + ":";
  if (GPS.minute < 10) { time += "0"; }
  time += String(GPS.minute) + ":";
  if (GPS.seconds < 10) { time += "0"; }
  time += String(GPS.seconds);
  time += " UTC";

  return time;
}


// Converts degrees to radians
float degToRad(float deg) {
  return deg * PI / 180;
}


// Calculates the distance between two sets of latitude and longitude
float calcDistance(float lat1, float lon1, float lat2, float lon2) {
  const float earthRadiusKm = 6371.0f;

  float dLat = degToRad(lat2 - lat1);
  float dLon = degToRad(lon2 - lon1);

  lat1 = degToRad(lat1);
  lat2 = degToRad(lat2);

  float a = pow(sin(dLat / 2), 2) + pow(sin(dLon / 2), 2) * cos(lat1) * cos(lat2);
  float c = 2 * atan2(sqrt(a), sqrt(1 - a));

  return earthRadiusKm * c;
}


// Check if start of run conditions met
bool shouldRunStart() {
  // Using altitude
  if (lastAltitude - currentAltitude >= MIN_ALT_DIFF) {
    return true;
  }

  // Using speed
  if (lastSpeed - currentSpeed >= MIN_SPD_DIFF) {
    return true;
  }

  return false;
}


// Check if start of run conditions met
bool shouldRunStop() {
  // Using altitude
  if (lastAltitude - currentAltitude < MIN_ALT_DIFF) {
    return true;
  }

  // Using speed
  if (lastSpeed - currentSpeed < MIN_SPD_DIFF) {
    return true;
  }

  return false;
}


// Stores start of run info
void startRun() {
  metrics.date = getDate();
  metrics.startTime = getTime();
  metrics.startAltitude = currentAltitude;
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
  
  metricsDoc["RunNumber"] = runCount + 1;
  metricsDoc["Duration"] = duration;
  metricsDoc["Date"] = metrics.date;
  metricsDoc["StartTime"] = metrics.startTime;
  metricsDoc["EndTime"] = getTime();
  metricsDoc["StartAltitude"] = metrics.startAltitude;
  metricsDoc["EndAltitude"] = currentAltitude;
  metricsDoc["AvgSpeed"] = metrics.sumSpeed / metrics.numSamples;
  metricsDoc["Distance"] = calcDistance(metrics.startLat, metrics.startLon, lat, lon);

  runCount++;

  saveData();
  metricsDoc.clear();
}


// Saves the current JSON data to the json data file
void saveData() {
  // TODO: save data to SD

  String jsonStr = "";
  serializeJson(metricsDoc, jsonStr);

  Serial.println();
  Serial.println("---RUN METRICS---");
  Serial.print("Run #: "); Serial.println(metricsDoc["RunNumber"].as<int>());
  Serial.print("Duration (s): "); Serial.println(metricsDoc["Duration"].as<float>());
  Serial.print("Date: "); Serial.println(metricsDoc["Date"].as<String>());
  Serial.print("Start Time: "); Serial.println(metricsDoc["StartTime"].as<String>());
  Serial.print("End Time: "); Serial.println(metricsDoc["EndTime"].as<String>());
  Serial.print("Start Altitude (m): "); Serial.println(metricsDoc["StartAltitude"].as<float>());
  Serial.print("End Altitude (m): "); Serial.println(metricsDoc["EndAltitude"].as<float>());
  Serial.print("Avg Speed (m/s): "); Serial.println(metricsDoc["AvgSpeed"].as<float>());
  Serial.print("Distance (km): "); Serial.println(metricsDoc["Distance"].as<float>());
}


// Sends the saved JSON data to the API
void sendData() {
  HTTPClient http;

  http.begin(""); // TODO: add address

  // get json from document
  
  int response = http.POST(""); // TODO: add json string
  if (response == HTTP_CODE_OK) {
    // clear saved json in doc
  }

  http.end();
}
