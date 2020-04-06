<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\UserTeam;
use App\Team;
use App\Season;
use App\Organization;
use App\Event;
use App\ParentAthlete;
use App\Run;
use App\UserOrg;
use App\Device;

class AthleteController extends Controller
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

        // Ensure the current user is actually an athlete
        if($typeID != 1)
        {
            return redirect()->back();
        }

        $usersTeams = UserTeam::where('user_id', $id)->get('team_id');

        $rosterArray = array();

        $eventArray = array();

        // Loop through all teams I am on
        foreach($usersTeams as $team)
        {
            $teamRoster = UserTeam::where('team_id', $team->team_id)->get('user_id');

            $memberArray = array();

            foreach($teamRoster as $member)
            {
                $user = User::Select('name', 'user_type_id')->where('id', $member->user_id)->first();
                array_push($memberArray,$user);
            }

            $teamArray = array();

            $teamInfo = Team::where('id', $team->team_id)->first();

            array_push($teamArray, $teamInfo, $memberArray);

            array_push($rosterArray,$teamArray);        

            $events = Event::where('team_id', $team->team_id)->get();

            $tempEventArray = array();

            foreach($events as $event)
            {
                $tempevent2 = array();

                $run = Run::Select('duration', 'avg_speed', 'distance')->where('user_id', $id)->where('event_id', $event->id)->get();
                              
                array_push($tempevent2, $event, $run);
                array_push($tempEventArray, $tempevent2);
            }

            array_push($eventArray,$tempEventArray);
        }
   
        $collection = collect([$eventArray, $rosterArray]);

        return view('athlete', compact('collection'));
    }
}
