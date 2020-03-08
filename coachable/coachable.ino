/*
 * PROGRAMMER:    William Bicknell
 * FIRST VERSION: Feb 9, 2020
 * DESCRIPTION:   Tracks skiing metrics
 */

#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include "SPIFFS.h"
#include <Adafruit_GPS.h>
#include <Adafruit_MPL3115A2.h>
#include <string>
#include <HTTPClient.h>
#include <WiFi.h>
#include <WiFiMulti.h>
#include <Metrics.h>
#include <Test.h>

#define GPSSerial Serial2

const String DEVICE_NAME = "ABC123";
const int MIN_ALT_DIFF = 1;
const float MIN_SPD = 1.0f;
const int LED_PIN = 5;
const int CS_PIN = 33;

uint32_t timer = millis();
int stopCount = 0;
bool addDataSample = false;
bool waitWifi = false;
bool useSD = false;

float lastAltitude = 0.0f;
float currentAltitude = 0.0f;
float lastSpeed = 0.0f;
float currentSpeed = 0.0f;

Metrics metrics = Metrics();
Adafruit_GPS GPS(&GPSSerial);
Adafruit_MPL3115A2 baro = Adafruit_MPL3115A2();

const char* ssid = "";
const char* password = "";
WiFiMulti wifiMulti;

// TODO: remove below
int trackCount = 0;
int testRuns = 0;
bool firstRun = true;


// Initialize
void setup() {
  pinMode(LED_PIN, OUTPUT);
  Serial.begin(115200);

  // Init wifi
  wifiMulti.addAP(ssid, password);

  // Init SD
  useSD = SD.begin(CS_PIN, SPI, 1000000, "/sd");
  if (!useSD) {
    Serial.println("Initial SD mount failed!");
  }

  // Init SPIFFS
  if(!SPIFFS.begin()){
    Serial.println("SPIFFS Mount Failed");
  }

  metrics.Init(SD, useSD, SPIFFS);

  // Init GPS
  GPS.begin(9600);
  GPS.sendCommand(PMTK_SET_NMEA_OUTPUT_RMCGGA);
  GPS.sendCommand(PMTK_SET_NMEA_UPDATE_10HZ);
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

  // Runs every ~0.5 secs
  if (millis() - timer >= 500) {
    timer = millis();
    readBaro();

    if (!metrics.IsRunOngoing()) {
      uploadData();
    }

    if (GPS.fix) {
      // Convert speed from knots to m/s
      currentSpeed = GPS.speed / 1.944f;

      // TODO: remove below used for testing without moving
//      if (firstRun && testRuns < 1) {
//        startRun();
//        firstRun = false;
//      }

      if (!metrics.IsRunOngoing()) {
        if (checkRunStart()) {
          startRun();
        }
      }
      else {
        addDataSamples();
        
        if (checkRunStop()) {
          stopCount++;

          if (stopCount >= 4) {
            finishRun();
          }
        }
        else {
          stopCount = 0;
        }
        
        // TODO: remove below used for testing without moving
//        trackCount += 1;
//        if (trackCount >= 120) {
//          finishRun();
//          trackCount = 0;
//          testRuns++;
//          firstRun = true;
//        }
      }

      lastSpeed = currentSpeed;
    }
    // If run is ongoing and GPS connection is lost, end run
    else if (metrics.IsRunOngoing()) {
      finishRun();
    }
    
    lastAltitude = currentAltitude;
    printMetrics(GPS.fix);
  }
}


// Get altitude from barometer
void readBaro() {
  if (baro.begin()) {
    currentAltitude = baro.getAltitude();
  }
}


// Send data if there are saved runs
void uploadData() {
  if (!waitWifi && metrics.GetNumSavedRuns() > 0 && wifiMulti.run() == WL_CONNECTED) {
    Serial.println("Sending http request...");
    sendData();
    // TODO: remove while
    //while(1);
    waitWifi = true;
  }
  else {
    waitWifi = false;
  }
}


// Prints the current metrics
void printMetrics(bool gpsFix) {
  Serial.print("Time: "); Serial.println(getTime());
  Serial.print("Date: "); Serial.println(getDate());
  Serial.print("Altitude(m): "); Serial.println(currentAltitude);

  if (gpsFix) {
    Serial.print("Num Satellites: "); Serial.println((int)GPS.satellites);
    Serial.print("Speed (m/s): "); Serial.println(currentSpeed);
    Serial.print("Latitude: "); Serial.println(String(GPS.latitudeDegrees, 7));
    Serial.print("Longitude: "); Serial.println(String(GPS.longitudeDegrees, 7));
  }
  else {
    Serial.println("Waiting for GPS satellites...");
  }

  Serial.println();
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


// Starts the run
void startRun() {
  metrics.StartRun(getDate(), getTime(), currentAltitude, GPS.latitudeDegrees, GPS.longitudeDegrees);
  
  digitalWrite(LED_PIN, HIGH);
  Serial.println("--RUN START--");
}


// Ends the run
void finishRun() {
  stopCount = 0;
  
  if (!useSD) {
    metrics.FinishRun(DEVICE_NAME, getTime(), currentAltitude, GPS.latitudeDegrees, GPS.longitudeDegrees, SPIFFS, useSD);
  }
  else {
    metrics.FinishRun(DEVICE_NAME, getTime(), currentAltitude, GPS.latitudeDegrees, GPS.longitudeDegrees, SD, useSD);
  }

  digitalWrite(LED_PIN, LOW);
  Serial.println("--RUN STOP--");
}


// Check if start of run conditions met
bool checkRunStart() {
  // Using altitude
  if (lastAltitude - currentAltitude >= MIN_ALT_DIFF) {
    return true;
  }

  // Using speed
  if (currentSpeed >= MIN_SPD) {
    return true;
  }

  return false;
}


// Check if start of run conditions met
bool checkRunStop() {
  // Using speed
  if (currentSpeed < MIN_SPD) {
    return true;
  }

  return false;
}


// Adds metrics data samples
void addDataSamples() {
  metrics.AddSpeedSample(currentSpeed);
  // Add data sample every other run
  if (addDataSample) {
    metrics.AddDataSample(GPS.latitudeDegrees, GPS.longitudeDegrees, currentSpeed, currentAltitude, getTime());
    addDataSample = false;
  }
  else {
    addDataSample = true;
  }
}


// Sends the saved JSON data to the API
void sendData() {
  HTTPClient http;
  http.begin("https://webhook.site/000d8382-1952-49f9-a79e-bf4de40e88ac");
  http.setUserAgent("Wearable");
  http.addHeader("Content-Type", "application/json");

  String jsonStr = metrics.GetJsonStr(SD, useSD, SPIFFS);
  int response = http.POST(jsonStr);
  if (response > 0) {
    if (response == HTTP_CODE_OK) {
      metrics.ClearJson(SD, useSD, SPIFFS);
    }
  }
  else {
    Serial.println("HTTP error: " + http.errorToString(response));
  }

  http.end();
}
