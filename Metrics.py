"""
#    FILE:          Metrics.py
#    PROGRAMMER:    William Bicknell
#    FIRST VERSION: January 22, 2020
#    DESCRIPTION:   This file contains the Metrics class.
"""


import requests
import json
import datetime
import os.path
from datetime import date

MIN_RUN_LENGTH = 5
METRICS_FILE_NAME = "./metrics.json"

# TODO: Store run number so when shutdown can remember
# TODO: Change Track_Accel to use velocity

class Metrics:
    """
    Stores skiing metrics and handles starting/ending runs and sending
    the skiing metrics through an http request.
    """
    def __init__(self):
        """
        Init all variables and data file
        """
        self.url = "https://webhook.site/6c07f778-07f7-45b8-9a4b-35bd2ca04e3b"
        self.numRuns = 0
        self.startTime = ""
        self.pressureSum = 0
        self.temperatureSum = 0
        self.readingsCount = 0
        self.runJson = ""
        
        # Init metrics json file
        self.initFile()
    
    
    def initFile(self, reset=False):
        """
        Inits the metrics json file if it does not exist
        
        Args:
        reset -- Should the file be reset
        """
        if (reset or not os.path.exists(METRICS_FILE_NAME)):
            file = open(METRICS_FILE_NAME, "w")
            file.write('{ "Runs": [')
            file.close()
    
    
    def inputReadings(self, temperature, pressure):
        """
        Adds temperature and pressure readings.
        
        Args:
        temperature -- The temperature reading
        pressure -- The pressure reading
        """
        self.temperatureSum += temperature
        self.pressureSum += pressure
        self.readingsCount += 1
        

    def sendReq(self):
        """
        Sends an http request to the specified URL containing the metrics for each run recorded.
        After sending the request, the data is deleted.
        """
        if (self.numRuns == 0):
            return
        
        file = open(METRICS_FILE_NAME, "r")
        rawJson = file.read()
        file.close()
        rawJson = rawJson[:-1]
        
        rawJson += ']}'

        metrics = json.loads(rawJson)
        response = requests.post(self.url, json=metrics)
        
        if (response):
            self.numRuns = 0
            self.initFile(True)
    
    
    def startRun(self, altitude):
        """
        Starts a run.
        
        Args:
        altitude -- The starting altitude
        """
        self.startTime = datetime.datetime.now()
        
        self.initFile()
        self.runJson = '{"RunNumber":%d, "Date":"%s", "StartAltitude":%.2f, "StartTime":"%s",'
        self.runJson = self.runJson % (self.numRuns + 1, date.today().strftime("%m/%d/%Y"), altitude, self.startTime.strftime("%H:%M:%S"))
        print("Start run\n")
        
        
    def endRun(self, altitude):
        """
        Ends a run.
        
        Args:
        altitude -- The starting altitude
        """
        endTime = datetime.datetime.now()
        runLength = endTime - self.startTime
        
        if (runLength.total_seconds() >= MIN_RUN_LENGTH):  
            # Calculate avgs
            temperatureAvg = self.temperatureSum / self.readingsCount
            pressureAvg = self.pressureSum / self.readingsCount
            
            self.runJson += '"EndTime":"%s", "EndAltitude":%.2f, "Length":"%s", "AvgTemperature":%.2f, "AvgPressure":%.2f},'
            self.runJson = self.runJson % (endTime.strftime("%H:%M:%S"), altitude, runLength, temperatureAvg, pressureAvg)
            self.numRuns += 1
            
            file = open(METRICS_FILE_NAME, "a")
            file.write(self.runJson)
            file.close()
            
            print("End run\n")
        else:
            print("Invalid run")
        
        self.sumTemp = 0
        self.sumPressure = 0
        self.readingsCount = 0
