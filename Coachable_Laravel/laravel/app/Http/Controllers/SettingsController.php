<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Auth;
use App\Device;


class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    { 
        $id = Auth::id();

        // Get device name attached to users id
        $device = Device::where('user_id', $id)->first('device_name');
        
        return view('settings', compact('device'));
    }

    public function manageDevice(Request $request)
    {
        if($request->submit == "Remove")
        {
            $this->removeDevice($request);
        }
        else
        {
            $this->addDevice($request);
        }
        
        return redirect()->route('home');
    }

    public function removeDevice(Request $request)
    {
        $id = Auth::id();

        // Get device name attached to users id
        $device = Device::where('user_id', $id)->first();

        $device->user_id = null;

        $device->save();
    }

    public function addDevice(Request $request)
    {
        $id = Auth::id();

        $deviceName = $request->input('devName');

        $device = Device::where('device_name', $deviceName)->first();

        $device->user_id = $id;

        $device->save();
    }
}
