<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Auth;
use App\ParentAthlete;
use App\UserTeam;
use App\Team;
use App\User;
use App\Event;
use App\Run;

class CompareController extends Controller
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
        if ($userid <= 0)
        {
            return Redirect::back()->withErrors(['msg', 'Invalid user id']);
        }

        $id = Auth::id();

        $parents = ParentAthlete::where('athlete_id', $userid)->get();
        $isParent = false;
        foreach($parents as $parent)
        {
            if ($id == $parent->parent_id)
            {
                $isParent = true;
                break;
            }
        }

        $userTeam = UserTeam::where('user_id', $userid)->first();
        $team = Team::where('id', $userTeam->team_id)->first();
        $coaches = User::where('user_type_id', 3)->get();
        $isCoach = false;
        foreach($coaches as $coach)
        {
            $coachTeam = UserTeam::where('user_id', $coach->id)->first();
            if ($team->id == $coachTeam->team_id && $id == $coach->id)
            {
                $isCoach = true;
                break;
            }
        }
        
        if($userid != $id && !$isParent && !$isCoach)
        {
            return Redirect::back()->withErrors(['msg', 'Access Denied']);
        }
        else
        {
            $selectedEvent = -1;
            $selectedRun = -1;
            
            $events = Event::where('team_id', $team->id)->get();
            $eventData = array();

            if (count($events))
            {
                foreach ($events as $event)
                {
                    $eventRuns = Run::where(['user_id' => $userid, 'event_id' => $event->id])->get();
                    $runData = array();

                    foreach ($eventRuns as $run)
                    {
                        $jsonObj = json_decode($run->other_data);
                        $timeArray = array();
                        $speedArray = array();
                        $altitudeArray = array();

                        if ($run->id == $runid)
                        {
                            $selectedRun = count($runData);
                            $selectedEvent = count($eventData);
                        }
                        
                        foreach($jsonObj as $dataEntry)
                        {
                            array_push($timeArray, $dataEntry->Time);
                            array_push($speedArray, $dataEntry->Speed);
                            array_push($altitudeArray, $dataEntry->Altitude);
                        }

                        $tempRunData = array();
                        array_push($tempRunData, $run, $timeArray, $speedArray, $altitudeArray);
                        array_push($runData, $tempRunData);
                    }

                    $temp = array();
                    array_push($temp, $event, $runData);
                    array_push($eventData, $temp);
                }

                return view('compare', compact('eventData', 'selectedRun', 'selectedEvent'));
            }
            else
            {
                return Redirect::back()->withErrors(['msg', 'Runs/Events not in DB']);
            }
        }
    }
}