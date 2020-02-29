#include "Arduino.h"
#include "FS.h"
#include "SD.h"
#include "SPI.h"
#include "SPIFFS.h"
#include <string>

class Test {
public:
  static String TestMem(fs::FS &sd, fs::FS &spiffs, bool useSD) {
    String s = "";
    if (useSD) {
      s += "Beginning SD tests\n";
      if (sd.exists(JSON_PATH)) {
        s += "JSON: ";
        File file = sd.open(JSON_PATH, FILE_READ);
        while (file.available()) {
          s += file.readStringUntil('\n');
        }
        file.close();
        s += "\n";
      }
      else {
        s += "JSON file doesnt exist!\n";
      }

      if (sd.exists(INFO_PATH)) {
        s += "INFO: ";
        File file = sd.open(INFO_PATH, FILE_READ);
        while (file.available()) {
          s += file.readStringUntil('\n');
        }
        file.close();
        s += "\n";
      }
      else {
        s += "INFO file doesnt exist!\n";
      }
    }
    else {
      s += "SD not connected!\n";
    }
  
    s += "Beginning SPIFFS tests\n";
    if (spiffs.exists(JSON_PATH)) {
      s += "JSON: ";
      File file = spiffs.open(JSON_PATH, FILE_READ);
      while (file.available()) {
        s += file.readStringUntil('\n');
      }
      file.close();
      s += "\n";
    }
    else {
      s += "JSON file doesnt exist!\n";
    }

    if (spiffs.exists(INFO_PATH)) {
      s += "INFO: ";
      File file = spiffs.open(INFO_PATH, FILE_READ);
      while (file.available()) {
        s += file.readStringUntil('\n');
      }
      file.close();
      s += "\n";
    }
    else {
      s += "INFO file doesnt exist!\n";
    }
  
    return s;
  }
  
private:
  Test() {}
};