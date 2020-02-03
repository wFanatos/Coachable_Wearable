"""
#    FILE:          Track_Accel.py
#    PROGRAMMER:    William Bicknell
#    FIRST VERSION: January 25, 2020
#    DESCRIPTION:   This file runs a test skiing metrics tracker that uses
#                   acceleration to determine when a run starts and ends.
"""


from sense_hat import SenseHat
from Metrics import Metrics
from Logger import *
import socket
import time

sense = SenseHat()
sense.get_pressure()
sense.get_temperature()
sense.get_accelerometer_raw()
runOngoing = False;

MIN_ACCEL_DIFF = 0.05;
SEA_LVL_PRESSURE = 1013.25

metrics = Metrics()

accelCount = 128
accelNoiseX = 0
accelNoiseY = 0
currentAccelX = 0
currentAccelY = 0

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
    LogInfo(__name__, "Checking if internet connection is available")
    try:
        socket.setdefaulttimeout(3)
        socket.socket(socket.AF_INET, socket.SOCK_STREAM).connect(("8.8.8.8", 53))
        LogInfo(__name__, "Internet connection found")
        return True
    except:
        LogInfo(__name__, "Could not detect internet connection")
        return False


LogInfo(__name__, "Starting metrics tracking (acceleration version)")

# Calibrate acceleration
numSamples = 256
for i in range(0, numSamples):
    rawAccel = sense.get_accelerometer_raw()
    accelNoiseX += rawAccel['x']
    accelNoiseY += rawAccel['y']

accelNoiseX /= numSamples
accelNoiseY /= numSamples
LogInfo(__name__, "Initialized accelerometer, X Noise: %f | Y Noise: %f" % (accelNoiseX, accelNoiseY))

while True:
    currentPressure = sense.get_pressure()
    currentTemp = sense.get_temperature()
    currentAltitude = calcAltitude(currentPressure, currentTemp)
    
    sumAccelX = 0
    sumAccelY = 0
    
    for i in range(0, accelCount):
        rawAccel = sense.get_accelerometer_raw()
        sumAccelX += rawAccel['x']
        sumAccelY += rawAccel['y']
    
    # Get average of last accels
    currentAccelX = (sumAccelX / accelCount) - accelNoiseX
    currentAccelY = (sumAccelY / accelCount) - accelNoiseY
    LogDebug(__name__, "Current Acceleration, X: %.2f | Y: %.2f" % (currentAccelX, currentAccelY))

    if (runOngoing):
        # Track pressure and temperature during run
        metrics.inputReadings(currentTemp, currentPressure)

        # Check for run end conditions
        if (currentAccelX > -MIN_ACCEL_DIFF and currentAccelX < MIN_ACCEL_DIFF and currentAccelY > -MIN_ACCEL_DIFF and currentAccelY < MIN_ACCEL_DIFF):
            endCount += 1
            LogInfo(__name__, "Run end conditions detected")
            
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
        if (currentAccelX > MIN_ACCEL_DIFF or currentAccelX < -MIN_ACCEL_DIFF or currentAccelY > MIN_ACCEL_DIFF or currentAccelY < -MIN_ACCEL_DIFF):
            startCount += 1
            LogInfo(__name__, "Run start conditions detected")
            
            if (startCount >= startAt):
                metrics.startRun(currentAltitude)
                runOngoing = True
                startCount = 0
        else:
            startCount = 0
        
    time.sleep(0.1)
