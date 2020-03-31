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

        if($userid != $id)
        {
            return Redirect::back()->withErrors(['msg', 'Access Denied']);
        }
        else
        {
            $run = Run::where('id',$runid)->get();
            if(count($run))
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

                $firstChart = new RunChart;
                $firstChart->labels($timeArray);
                $firstChart->dataset('Speed over Time', 'line', $speedArray);

                $secondChart = new RunChart;
                $secondChart->labels($timeArray);
                $secondChart->dataset('Altitude over Time', 'line', $altitudeArray);

                return view ('run', compact('run','firstChart', 'secondChart'));
            }
            else 
            {
                return Redirect::back()->withErrors(['msg', 'Run not in DB']); 
            }
        }       
    }
}
