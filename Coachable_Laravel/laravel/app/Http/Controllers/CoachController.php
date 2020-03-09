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
        $user = User::Select(
            'name', 'email', 'user_type_id')->where('id', $id)->first();
        
        // Get the users type
        $typeID = $user->user_type_id;
        
        // Ensure the current user is actually a coach
        if($typeID != 3)
        {
            return redirect()->back();
        }

        $teamArray = array();

        //Get team that coach is apart of - only one team rn
        $userTeam = UserTeam::where('user_id', $id)->first('team_id');
        $teamid = $userTeam->team_id;
        $team = Team::where('id', $teamid)->first();
        $season = Season::where('id', $team->season_id)->first();
        $events = Event::where('team_id', $teamid)->get();
        $teamMembers = UserTeam::where([
            ['team_id', '=', $teamid],
            ['user_id', '<>', $id],
        ])->get('user_id');

        // Loop through the list of parents and grab their information
        foreach($teamMembers as $member)
        {
            $temp = array();

            // Array for storing run information
            $runArray = array();

            $device = Device::where('user_id', $member->user_id)->first('device_name');
            $userInfo = User::Select('name', 'email')->where('id', $member->user_id)->first();

            // Loop through each event and grab a list of runs attached to user
            foreach($events as $event)
            {
                $temp2 = array();

                $run = Run::where('user_id', $member->user_id)->where('event_id', $event->id)->get();
                array_push($temp2, $event, $run);
                array_push($runArray, $temp2);    
            }

            array_push($temp, $device, $userInfo, $runArray);
            array_push($teamArray, $temp);
        }            

        $collection = collect([$user,$team, $season, $teamArray]);

        //dd($collection);

        return view('coach', compact('collection'));
    }
}
