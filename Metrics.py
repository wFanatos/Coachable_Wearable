import requests
import json
import datetime
import time
from datetime import date

MIN_RUN_LENGTH = 3


class Metrics:
    def __init__(self):
        self.url = "https://webhook.site/6c07f778-07f7-45b8-9a4b-35bd2ca04e3b"
        self.numRuns = 0
        self.runStart = 0
        self.pressureReadings = []
        self.tempReadings = []
        self.pressureAvgs = []
        self.tempAvgs = []
        self.altitudeStart = []
        self.altitudeEnd = []
        self.date = []
        self.startTime = []
        self.endTime = []


    def sendReq(self):
        if (self.numRuns == 0):
            return
        
        rawJson = '{ "Runs": ['
        
        for i in range(0, self.numRuns):
            length = self.endTime[i] - self.startTime[i]
            rawJson += '{ "RunNumber":%d, "Length":"%s", "AvgTemp":%.2f, ' % (i+1, length, self.tempAvgs[i])
            rawJson += '"AvgPressure":%.2f, "StartAltitude":%.2f, "EndAltitude":%.2f, ' % (self.pressureAvgs[i], self.altitudeStart[i], self.altitudeEnd[i])
            rawJson += '"Date":"%s", "StartTime":"%s", "EndTime":"%s" }' % (self.date[i], self.startTime[i].strftime("%H:%M:%S"), self.endTime[i].strftime("%H:%M:%S"))
            if (i != self.numRuns - 1):
                rawJson += ', '
        
        rawJson += ']}'

        metrics = json.loads(rawJson)
        requests.post(self.url, json=metrics)
        self.numRuns = 0
        self.pressureAvgs = []
        self.tempAvgs = []
        self.altitudeStart = []
        self.altitudeEnd = []
        self.startTime = []
        self.endTime = []
        self.date = []
    
    
    def startRun(self, altitude):
        # Calculate run end altitude
        self.altitudeStart.append(altitude)
        self.runStart = time.time()
        self.date.append(date.today().strftime("%m/%d/%Y"))
        self.startTime.append(datetime.datetime.now())
        print("Start run\n")
        
        
    def endRun(self, altitude):
        sumPressure = 0
        sumTemp = 0
        runLength = time.time() - self.runStart
        
        if (len(self.tempReadings) == 0 or len(self.pressureReadings) == 0):
            return
        
        if (runLength >= MIN_RUN_LENGTH):
            # Sum readings
            for temp in self.tempReadings:
                sumTemp += temp
            for pressure in self.pressureReadings:
                sumPressure += pressure
            
            # Calculate avgs
            tempAvg = sumTemp / len(self.tempReadings)
            pressureAvg = sumPressure / len(self.pressureReadings)
            
            # Calculate run end altitude
            self.altitudeEnd.append(altitude)
        
            self.endTime.append(datetime.datetime.now())
            self.tempAvgs.append(tempAvg)
            self.pressureAvgs.append(pressureAvg)
            self.numRuns += 1
            print("End run\n")
        else:
            print("Invalid run")
            
        self.tempReadings = []
        self.pressureReadings = []
