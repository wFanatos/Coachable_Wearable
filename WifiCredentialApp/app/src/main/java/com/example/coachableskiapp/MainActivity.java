/*
*   Programmer: Firas
*   Description: Update wifi credentials to the board, to allow wifi connection.
*   Date:       3/15/2020
 */

package com.example.coachableskiapp;

import android.app.Activity;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.bluetooth.BluetoothSocket;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.view.MotionEvent;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RelativeLayout;
import android.widget.TextView;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Set;
import java.util.UUID;


public class MainActivity extends Activity {
    private final String DeviceName ="ESP32";
    private final UUID D_UUID = UUID.fromString("00001101-0000-1000-8000-00805f9b34fb");
    private BluetoothDevice device;
    private BluetoothSocket socket;
    private OutputStream outputStream;
    private InputStream inputStream;
    private String recievedInfo;
    boolean deviceConnected=false;
    boolean stopThread;
    byte buffer[];
    Button startBtn, sendBtn, stopBtn;
    EditText ssidTxt, ssidPassTxt;
    InputMethodManager keyBoardH;
    RelativeLayout relLayout;
    TextView statusView, ssidLbl, ssidPassLbl;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        startBtn = (Button) findViewById(R.id.buttonStart);
        sendBtn = (Button) findViewById(R.id.buttonSend);
        stopBtn = (Button) findViewById(R.id.buttonStop);
        ssidTxt = (EditText) findViewById(R.id.ssid);
        ssidLbl = (TextView) findViewById(R.id.ssidLbl);
        ssidPassLbl = (TextView) findViewById(R.id.ssidPassLbl);
        ssidPassTxt = (EditText) findViewById(R.id.ssidPassword);
        statusView = (TextView) findViewById(R.id.status);
        relLayout = (RelativeLayout) findViewById(R.id.relLayout);

        ConfigureUI(false);


        relLayout.setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                keyBoardH = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                keyBoardH.hideSoftInputFromWindow(getCurrentFocus().getWindowToken(), 0);
                return false;
            }
        });


    }


    public boolean BluetoothInit()
    {
        boolean found=false;
        BluetoothAdapter bluetoothAdapter=BluetoothAdapter.getDefaultAdapter();
        if (bluetoothAdapter == null) {
            statusView.setText("Device doesnt Support Bluetooth");
        }
        if(!bluetoothAdapter.isEnabled())
        {
            Intent enableAdapter = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
            startActivityForResult(enableAdapter, 0);
            try {
                Thread.sleep(1000);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
        Set<BluetoothDevice> bondedDevices = bluetoothAdapter.getBondedDevices();

        if(bondedDevices.isEmpty())
        {
            statusView.setText("Pair the device first\n");
            startBtn.setTextColor(Color.parseColor("#DE512E"));
        }
        else
        {
            for (BluetoothDevice iterator : bondedDevices)
            {

                if(iterator.getName().contains(DeviceName))
                {


                    device=iterator;
                    found=true;
                    break;
                }
            }
        }
        return found;
    }

    public void onClickSend(View view) {
        String clearMsg = "CLEAR_WIFI\0";
        String ssid = "SSID|" + ssidTxt.getText().toString() + "\0";
        String pass = "PASS|"+ ssidPassTxt.getText().toString() + "\0";
        try {
            outputStream.write(clearMsg.getBytes());
            outputStream.write(ssid.getBytes());
            outputStream.write(pass.getBytes());
        } catch (IOException e) {
            e.printStackTrace();
        }
        statusView.append("Verifying Data\n");
        ssidTxt.setText("");
        ssidPassTxt.setText("");

    }

    public void ConfigureUI(boolean bool)
    {
        startBtn.setEnabled(!bool);
        sendBtn.setEnabled(bool);
        stopBtn.setEnabled(bool);
        statusView.setEnabled(bool);
    }

    public boolean BluetoothConn()
    {
        boolean connected=true;
        try {

            socket = device.createRfcommSocketToServiceRecord(D_UUID);
            socket.connect();
        } catch (IOException e) {
            e.printStackTrace();
            statusView.setText("Connection could not be made\n");
            startBtn.setTextColor(Color.parseColor("#DE512E"));
            connected=false;
        }
        if(connected)
        {
            try {
                outputStream=socket.getOutputStream();
            } catch (IOException e) {
                e.printStackTrace();
            }
            try {
                inputStream=socket.getInputStream();
            } catch (IOException e) {
                e.printStackTrace();
            }

        }


        return connected;
    }

    public void onClickStart(View view) {
        stopBtn.setTextColor(Color.parseColor("#FFFFFF"));

        if(BluetoothInit())
        {
            if(BluetoothConn())
            {
                ConfigureUI(true);
                deviceConnected=true;
                beginListenForData();
                startBtn.setTextColor(Color.parseColor("#4BF73D"));
                statusView.append("Connection Opened\n");

            }

        }
    }

    void beginListenForData()
    {
        final Handler handler = new Handler();
        stopThread = false;
        buffer = new byte[1024];
        Thread thread  = new Thread(new Runnable()
        {
            public void run()
            {
                while(!Thread.currentThread().isInterrupted() && !stopThread)
                {
                    try
                    {
                        int byteCount = inputStream.available();
                        if(byteCount > 0)
                        {
                            byte[] rawBytes = new byte[byteCount];
                            inputStream.read(rawBytes);
                            final String string=new String(rawBytes,"UTF-8");
                            recievedInfo += string;
                            if(string.contains("@"))
                            {
                                recievedInfo = recievedInfo.replace("\n","").replace("null","").replace("@","");
                                handler.post(new Runnable() {
                                    public void run()
                                    {
                                        statusView.append(recievedInfo +"\n");
                                        recievedInfo = "";
                                    }
                                });

                            }


                        }
                    }
                    catch (IOException ex)
                    {
                        stopThread = true;
                    }
                }
            }
        });

        thread.start();
    }



    public void onClickStop(View view) throws IOException {
        stopThread = true;
        outputStream.close();
        inputStream.close();
        socket.close();
        ConfigureUI(false);
        deviceConnected=false;
        stopBtn.setTextColor(Color.parseColor("#DE512E"));
        startBtn.setTextColor(Color.parseColor("#FFFFFF"));//FFFFFF
        //statusView.append("\nConnection Closed!\n");
        statusView.setText("");
    }


}