<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Run;
use App\User;

use App\Charts;
use App\Charts\RunChart;

use Illuminate\Support\Facades\Redirect;

class RunController extends Controller
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

    public function index($userid, $runid)
    {
         // MAKE SURE THE REQUESTED USER IS THE ONE LOGGED IN/IN SYSTEM
        // CHECK TO MAKE SURE THE RUN REQUESTED IS VALID
        // IF EVERYTHING IS OK, CONTINUE
 
        // GIVEN RUNID AND USERID, RETURN THAT RUN
        // SEND RUN BACK TO VIEW FOR PARSING/DISPLAYING

        $id = Auth::id();
        $run = Run::where('id',$runid)->get();

        if(count($run))
        { 
            if($userid != $id || $run[0]->user_id != $id)
            {
                return Redirect::back()->withErrors(['msg', 'Access Denied']);
            }
            else
            {
                
                $jsonObj = json_decode($run[0]->other_data);

                $timeArray = array();
                $speedArray = array();
                $altitudeArray = array();

                foreach($jsonObj as $dataEntry)
                {
                    array_push($timeArray, $dataEntry->Time);
                    array_push($speedArray, $dataEntry->Speed);
                    array_push($altitudeArray, $dataEntry->Altitude);
                }

                return view ('run', compact('run', 'timeArray', 'speedArray', 'altitudeArray'));
            }
        }
        else 
        {
            return Redirect::back()->withErrors(['msg', 'Run not in DB']); 
        }       
    }
}
