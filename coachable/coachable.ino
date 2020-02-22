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
#include <WiFi.h>
#include <WiFiMulti.h>
#include <Metrics.h>

#define GPSSerial Serial2

const int MIN_ALT_DIFF = 1;
const float MIN_SPD_DIFF = 0.25f;

uint32_t timer = millis();
int stopCount = 0;

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
bool firstRun = true;

// Initialize
void setup() {
  Serial.begin(115200);

  // Init wifi
  wifiMulti.addAP(ssid, password);
  
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

      // TODO: remove below
      if (firstRun) {
        Serial.println("--RUN START--");
        metrics.StartRun(getDate(), getTime(), currentAltitude, GPS.latitude, GPS.lat, GPS.longitude, GPS.lon);
        firstRun = false;
      }

      if (!metrics.IsRunOngoing()) {
        if (wifiMulti.run() == WL_CONNECTED && metrics.GetNumSavedRuns() > 0) {
          // TODO: check if data should be sent
          Serial.println("Sending http request...");
          sendData();
          while(1);
        }
        
        if (checkRunStart()) {
          metrics.StartRun(getDate(), getTime(), currentAltitude, GPS.latitude, GPS.lat, GPS.longitude, GPS.lon);
          Serial.println("--RUN START--");
        }
      }
      else {
//        if (checkRunStop()) {
//          stopCount++;
//
//          if (stopCount >= 3) {
//            metrics.FinishRun(getTime(), currentAltitude, GPS.latitude, GPS.lat, GPS.longitude, GPS.lon);
//            stopCount = 0;
//            Serial.println("--RUN STOP--");
//          }
//        }
//        else {
//          stopCount = 0;
//        }

        trackCount += 1;
        metrics.AddSpeedSample(currentSpeed);

        if (trackCount >= 20) {
          Serial.println("--RUN STOP--");
          metrics.FinishRun(getTime(), currentAltitude, GPS.latitude, GPS.lat, GPS.longitude, GPS.lon);
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


// Check if start of run conditions met
bool checkRunStart() {
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
bool checkRunStop() {
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


// Sends the saved JSON data to the API
void sendData() {
  HTTPClient http;

  http.begin("https://webhook.site/000d8382-1952-49f9-a79e-bf4de40e88ac");

  // TODO: get json from file once saving works
  String jsonStr = metrics.GetJsonStr();
  
  http.addHeader("Content-Type", "application/json");
  int response = http.POST(jsonStr);

  if (response > 0) {
    if (response == HTTP_CODE_OK) {
      metrics.ClearJson();
    }
  }
  else {
    Serial.println("HTTP error: " + http.errorToString(response));
  }

  http.end();
}
