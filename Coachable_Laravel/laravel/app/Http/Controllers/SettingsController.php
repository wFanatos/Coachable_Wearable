<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Auth;
use App\Device;
use App\User;


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
        // Get the id of current user logged in
        $id = Auth::id();

        // Get the logged in user's basic information
        $user = User::Select('user_type_id')->where('id', $id)->first();

        // Get the users type
        $typeID = $user->user_type_id;

        $flag = 0;

        // Ensure the current user is actually an athlete
        if($typeID == 1)
        {
           // Get device name attached to users id
            $device = Device::where('user_id', $id)->first('device_name');
            $flag = 1;
        }
        else
        {
            $device = null;
        }       

        // Get device name attached to users id
        $device = Device::where('user_id', $id)->first('device_name');
        
        return view('settings', compact('device', 'flag'));
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
        
        return redirect()->route('settings');
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
