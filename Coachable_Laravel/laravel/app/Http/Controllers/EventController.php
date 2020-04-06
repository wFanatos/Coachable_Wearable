<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Event;
use App\Run;
use App\ParentAthlete;

use App\Charts;
use App\Charts\RunChart;

use Illuminate\Support\Facades\Redirect;


class EventController extends Controller
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

    public function index($eventid, $userid)
    {
        $id = Auth::id();
        $parent = ParentAthlete::where('athlete_id',$userid)->get();
        
        if($userid != $id && (count($parent) && $userid != $parent[0]->parent_id))
        {
            return Redirect::back()->withErrors(['msg', 'Access Denied']);
        }
        else
        {
            $runs = Run::where(['user_id' => $userid, 'event_id' => $eventid])->get();
            
            if (count($runs))
            {
                $runData = array();
                
                foreach($runs as $run)
                {
                    $jsonObj = json_decode($run->other_data);
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
                    $firstChart->dataset('Speed(km/h) over Time (seconds)', 'line', $speedArray);
                
                    $secondChart = new RunChart;
                    $secondChart->labels($timeArray);
                    $secondChart->dataset('Altitude over Time (seconds)', 'line', $altitudeArray);
                    
                    $temp = array();
                    array_push($temp, $run, $firstChart, $secondChart);
                    array_push($runData, $temp);
                }
                
                return view('event', compact('runData'));
            }
            else
            {
                return Redirect::back()->withErrors(['msg', 'Runs not in DB']);
            }
        }
    }
}
