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
from Logger import *
from datetime import date

MIN_RUN_LENGTH = 5
METRICS_FILE_NAME = "./metrics.json"
PROG_DATA_FILE_NAME = "./prog.dat"


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
        self.numTotalRuns = 0
        self.numRuns = 0
        self.startTime = ""
        self.pressureSum = 0
        self.temperatureSum = 0
        self.readingsCount = 0
        self.runData = {}
        
        # Init metrics json file
        self.initFile()
        
        # Load prog data
        self.loadProgData()
    
    
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
            
            if (not reset):
                LogInfo(__name__, "Initialized run data json file")
            else:
                LogInfo(__name__, "Reset run data json file")
    
    
    def saveProgData(self):
        """
        Saves the current program data for use between program uses
        """
        file = open(PROG_DATA_FILE_NAME, "w")
        file.write(str(self.numTotalRuns))
        file.close()
        LogInfo(__name__, "Saved total run count. Total runs = %d" % self.numTotalRuns)
        
        
    def loadProgData(self):
        """
        Loads the saved program data if it exists
        """
        if (os.path.exists(PROG_DATA_FILE_NAME)):
            file = open(PROG_DATA_FILE_NAME, "r")
            self.numTotalRuns = int(file.read())
            file.close()
            LogInfo(__name__, "Loaded saved program data. Total runs = %d" % self.numTotalRuns)
    
    
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
        LogInfo(__name__, "Received temperature: %.2f and pressure: %.2f" % (temperature, pressure))
        

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

        LogInfo(__name__, "Sending metrics to: %s" % self.url)

        metrics = json.loads(rawJson)
        response = requests.post(self.url, json=metrics)
        
        LogInfo(__name__, "Received %s response" % response.status_code)
        
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
        self.runData = {
            "RunNumber": self.numRuns + 1,
            "Date": date.today().strftime("%m/%d/%Y"),
            "StartAltitude": float("%.2f" % altitude),
            "StartTime": self.startTime.strftime("%H:%M:%S")
        }
        
        LogInfo(__name__, "Run started")
        
        
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
            
            self.runData["EndTime"] = endTime.strftime("%H:%M:%S")
            self.runData["EndAltitude"] = float("%.2f" % altitude)
            self.runData["Length"] = str(runLength)
            self.runData["AvgTemperature"] = float("%.2f" % temperatureAvg)
            self.runData["AvgPressure"] = float("%.2f" % pressureAvg)
            self.numRuns += 1
            self.numTotalRuns += 1
            self.saveProgData()
            
            file = open(METRICS_FILE_NAME, "a")
            file.write(json.dumps(self.runData) + ",")
            file.close()
            
            LogInfo(__name__, "Run ended, Length: %f s" % runLength.total_seconds())
        else:
            LogInfo(__name__, "Run ended but was invalid, Length: %f s" % runLength.total_seconds())
        
        self.sumTemp = 0
        self.sumPressure = 0
        self.readingsCount = 0
