from sense_hat import SenseHat
from Metrics import Metrics
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
    # Using Hypsometric formula
    h = (((pow((SEA_LVL_PRESSURE / pressure), (1 / 5.257)) - 1) * (temp + 273.15)) / 0.0065) * 3.281
    return h
    

def checkConnection():
    # Check by trying to connect to Google DNS
    try:
        socket.setdefaulttimeout(3)
        socket.socket(socket.AF_INET, socket.SOCK_STREAM).connect(("8.8.8.8", 53))
        print("Attempt connect success")
        return True
    except:
        print("Attempt connect failed")
        return False


# Calibrate acceleration
numSamples = 1024
for i in range(0, numSamples):
    rawAccel = sense.get_accelerometer_raw()
    accelNoiseX += rawAccel['x']
    accelNoiseY += rawAccel['y']
    
accelNoiseX /= numSamples
accelNoiseY /= numSamples
print(accelNoiseX)
print(accelNoiseY)

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
    print("AccelX: %.2f" % currentAccelX)
    print("AccelY: %.2f" % currentAccelY)

    if (runOngoing):
        # Track pressure and temperature during run
        metrics.pressureReadings.append(currentPressure)
        metrics.tempReadings.append(currentTemp)

        # Check for run end conditions
        if (currentAccelX > -MIN_ACCEL_DIFF and currentAccelX < MIN_ACCEL_DIFF and currentAccelY > -MIN_ACCEL_DIFF and currentAccelY < MIN_ACCEL_DIFF):
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
        if (currentAccelX > MIN_ACCEL_DIFF or currentAccelX < -MIN_ACCEL_DIFF or currentAccelY > MIN_ACCEL_DIFF or currentAccelY < -MIN_ACCEL_DIFF):
            startCount += 1
            
            if (startCount >= startAt):
                metrics.startRun(currentAltitude)
                runOngoing = True
                startCount = 0
        else:
            startCount = 0
        
    time.sleep(0.1)
