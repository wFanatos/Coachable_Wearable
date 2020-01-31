"""
#    FILE:          Track_Alt.py
#    PROGRAMMER:    William Bicknell
#    FIRST VERSION: January 22, 2020
#    DESCRIPTION:   This file runs a test skiing metrics tracker that uses
#                   altitude to determine when a run starts and ends.
"""


from sense_hat import SenseHat
from Metrics import Metrics
import socket
import time

sense = SenseHat()
sense.get_pressure()
sense.get_temperature()
runOngoing = False;

MIN_ALTITUDE_DIFF = 0.5;
SEA_LVL_PRESSURE = 1013.25

metrics = Metrics()

lastAltitude = 0
currentAltitude = 0
altCount = 0
altitudes = []

checkConnCount = 0
checkConnAt = 10 # approx every 5 sec

startCount = 0
startAt = 2 # approx 1 sec of start conditions
endCount = 0
endAt = 2 # approx 1 sec of end conditions


def calcAltitude(pressure, temp):
    """
    Calculates altitude using the Hypsometric formula.

    Args:
    pressure -- The pressure (in hPa/millibars) to use
    temp -- The temperature (in Celsius) to use
    """
    h = (((pow((SEA_LVL_PRESSURE / pressure), (1 / 5.257)) - 1) * (temp + 273.15)) / 0.0065) * 3.281
    return h
    

def checkConnection():
    """
    Checks if a connection is available by trying to connect
    to Google's DNS servers.
    """
    try:
        socket.setdefaulttimeout(3)
        socket.socket(socket.AF_INET, socket.SOCK_STREAM).connect(("8.8.8.8", 53))
        print("Attempt connect success")
        return True
    except:
        print("Attempt connect failed")
        return False


while True:
    currentPressure = sense.get_pressure()
    currentTemp = sense.get_temperature()

    altCount += 1
    altitudes.append(calcAltitude(currentPressure, currentTemp))
    
    # Happens every ~0.5 sec
    if (altCount >= 5):
        sumAlt = 0
        lastAltitude = currentAltitude
        for alt in altitudes:
            sumAlt += alt
        
        # Get average of last 10 altitudes
        currentAltitude = sumAlt / altCount
        print("Alt: %.2f ft" % currentAltitude)
        altCount = 0
        altitudes = []
    
        if (runOngoing):
            # Track pressure and temperature during run
            metrics.inputReadings(currentTemp, currentPressure)
            
            # Check for run end conditions
            if (lastAltitude != 0 and currentAltitude != 0):
                diff = currentAltitude - lastAltitude
                
                if (diff > -MIN_ALTITUDE_DIFF and diff < MIN_ALTITUDE_DIFF):
                    endCount += 1
                    
                    if (endCount >= endAt):
                        metrics.endRun(currentAltitude)
                        runOngoing = False
                        endCount = 0
                else:
                    endCount = 0
        else:
            # Check if connection is available
            if (checkConnCount >= checkConnAt):
                checkConnCount = 0
                if (checkConnection()):
                    metrics.sendReq()
            else:
                checkConnCount += 1
            
            # Check for run start conditions
            if (lastAltitude != 0 and currentAltitude != 0 and currentAltitude < lastAltitude):
                diff = currentAltitude - lastAltitude
                
                if (diff < -MIN_ALTITUDE_DIFF):
                    startCount += 1
                    
                    if (startCount >= startAt):
                        metrics.startRun(currentAltitude)
                        runOngoing = True
                        startCount = 0
                else:
                    startCount = 0
        
    time.sleep(0.1)
