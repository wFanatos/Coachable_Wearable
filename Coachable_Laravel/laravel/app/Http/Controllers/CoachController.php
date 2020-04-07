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

class CoachController extends Controller
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
        
        // Ensure the current user is actually a coach
        if($typeID != 3)
        {
            return redirect()->back();
        }


        //Get list of teams that i am on
        //For each team, get a list of events
        // For each event, get each members runs for that event

        $userTeams = UserTeam::where('user_id', $id)->get('team_id');

        $teamArray = array();
        $eventArray = array();
        

        foreach($userTeams as $team)
        {
            $teamMembers = UserTeam::where([
                ['team_id', '=', $team->team_id],
                ['user_id', '<>', $id],
            ])->get('user_id');

            $teamInfo = Team::Select('name')->where('id', $team->team_id)->first();

            $events = Event::where('team_id', $team->team_id)->get();

            foreach($events as $event)
            {
                $memberArray = array();

                foreach($teamMembers as $member)
                {               
                    $tempMemberArray = array();

                    $name = User::Select('id', 'name')->where('id', $member->user_id)->first();

                    $runs = Run::Select('distance')->where('user_id', $member->user_id)->where('event_id', $event->id)->get();
                    $runCount = $runs->count();

                    array_push($tempMemberArray, $name, $runs,$runCount);
                    array_push($memberArray,$tempMemberArray);
                }

                $tempEventArray = array();

                array_push($tempEventArray, $event, $memberArray);
                array_push($eventArray, $tempEventArray);                
            }

            $a = array();

            array_push($a, $teamInfo, $eventArray);

            array_push($teamArray, $a);
        }

        $collection = collect($teamArray);

        return view('coach', compact('collection'));
    }
}
