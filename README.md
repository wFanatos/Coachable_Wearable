<h1>The Coachable Wearable</h1>
<p>The Coachable Wearable is a wearable device that allows skiers to track metrics such as speed, altitude, run length, and number of runs. The metrics tracked by the device can be uploaded and viewed on a website.</p>
<h2>Components</h2>
<h3>Wearable</h3>
<p>The wearable tracks the skiing metrics. To setup the device some hardware will be required:</p>
<ul>
  <li>ESP32 Thing: https://www.sparkfun.com/products/13907</li>
  <li>Adafruit Ultimate GPS Breakout: https://www.adafruit.com/product/746</li>
  <li>MPL3115A2 Altitude Sensor: https://www.sparkfun.com/products/11084</li>
</ul>
<p>The Arduino code in Coachable_Wearable/wearable/coachable/ can be uploaded to the board using Arduino studio. But first:</p>
<ul>
  <li>Move Coachable_Wearable/wearable/Metrics to the Arduino libraries folder (ex. C:\Users\<user>\Documents\Arduino\libraries\)</li>
  <li>In Arduino studio set the partition scheme to Minimal SPIFFS</li>
  <li>Move the esp32-hal-bt.c file in Coachable_Wearable/wearable/esp32/ to the esp32 core code library (ex. C:\Users\<user>\AppData\Local\Arduino15\packages\esp32\hardware\esp32\1.0.4\cores\esp32)</li>
  <li>Move boards.txt to the Arduino esp32 folder (ex. C:\Users\<your username>\AppData\Local\Arduino15\packages\esp32\hardware\esp32\1.0.4\)</li>
  <li>Move Coachable_Wearable/wearable/HTTPClient to the esp32 libraries folder (ex. C:\Users\<your username>\AppData\Local\Arduino15\packages\esp32\hardware\esp32\1.0.4\libraries\)</li>
</ul>

<h3>Database</h3>
<p>A MySQL database that stores the run data. In order to receive data from the API the database will need to be hosted. The schema for the database can be created using the db creation script.</p>

<h3>API</h3>
<p>An ASP.net API that receives the run data from the wearable. In order to receive data from the wearable the API needs to be hosted, and the HTTP request address within the Arduino code will need to be changed to the hosted API. Also, the database connection string within the web.config will need to be changed to the hosted database.</p>

<h3>WiFi App</h3>
<p>An Android app that uses BlueTooth to configure the wifi for the wearable. The app can be installed using Android Studio.</p>

<h3>Website</h3>
<p>A Laravel website that displays the run data stored in the database. This can be run locally using <code>php artisan serve</code>. Before running a .env file will need to be created and give an APP_KEY and the database info.</p>
