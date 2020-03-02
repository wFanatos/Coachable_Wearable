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

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get the id of current user logged in
        $id = Auth::id();

        // Get the logged in user's basic information
        $user = User::Select(
            'user_type_id', 
            'name', 
            'email')->where('id', $id)->first();

        // Get the users type
        $typeID = $user->user_type_id;

        // If the logged in user is an athlete
        if($typeID == 1)
        {
            // Array for storing parent information
            $parentArray = array();

            // Array for storing run information
            $runArray = array();

            // Get device name attached to users id
            $device = Device::where('user_id', $id)->first('device_name');
            
            // Get the team id that the user is apart of
            $userTeam = UserTeam::where('user_id', $id)->first('team_id');

            // Assign teamid for further use
            $teamid = $userTeam->team_id;

            // Get the users team information
            $team = Team::where('id', $teamid)->first();

            // Get the current season the team is in
            $season = Season::where('id', $team->season_id)->first();

            // Get the organization that the user is apart of
            $org = Organization::where('id', $season->org_id)->first();

            // Grab the list of events that the team is participating in
            $events = Event::where('team_id', $teamid)->get();

            // Loop through each event and grab a list of runs attached to user
            foreach($events as $event)
            {
                $temp = array();
                $run = Run::where('user_id', $id)->where('event_id', $event->id)->get();
                array_push($temp, $run);
                array_push($runArray, $temp);
            }
            //dd($runArray);
            // Get a list of parents associated with user
            $parents = ParentAthlete::where('athlete_id', $id)->get('parent_id');

            // Loop through the list of parents and grab their information
            foreach($parents as $parent)
            {
                $temp = array();
                $parentUser = User::select('name')->where('id', $parent->parent_id)->first();
                array_push($temp, $parentUser);
                array_push($parentArray, $temp);
            }

            // Get the users device
            //$collection = collect([$user, $device, $team, $season, $org, $events, $parentArray, $runArray]);

            return view('home', compact('user', 'device', 'team', 'season', 'org', 'events', 'parentArray', 'runArray'));
        }

        // If the logged in user is a parent
        else if($typeID == 2)
        {
            // Array for storing child information
            $childArray = array();

            // Array for storing run information
            $runArray = array();

            // Get all children attached to parent
            $children = ParentAthlete::where('parent_id', $id)->get('athlete_id');         

            // Loop through each child and get their information
            foreach($children as $child)
            {
                $temp = array();

                $childInfo = User::Select('name')->where('id', $child->athlete_id)->first();
                $userTeam = UserTeam::where('user_id', $child->athlete_id)->first('team_id');
                $teamid = $userTeam->team_id;
                $team = Team::where('id', $teamid)->first();
                $season = Season::where('id', $team->season_id)->first();              
                $events = Event::where('team_id', $teamid)->get();

                // Loop through each event and grab a list of runs attached to user
                foreach($events as $event)
                {
                    $run = Run::where('user_id', $child->athlete_id)->where('event_id', $event->id)->get();
                    array_push($runArray, $run);
                }
               
                array_push($temp, $team, $season, $runArray);
                array_push($childArray, $temp);
            }
            
            //dd($childArray);

            $collection = collect([$childArray]);    
            //dd($collection);

            return view('home', compact('user', 'childArray'));
        }

        // If the logged in user is a coach
        else if($typeID == 3)
        {
            $teamArray = array();

            //Get team that coach is apart of - only one team rn
            $userTeam = UserTeam::where('user_id', $id)->first('team_id');
            $teamid = $userTeam->team_id;
            $team = Team::where('id', $teamid)->first();
            $season = Season::where('id', $team->season_id)->first(); // could add ability for past seasons, not just current
            $events = Event::where('team_id', $teamid)->get();
            $teamMembers = UserTeam::where('team_id', $teamid)->get('user_id');

            foreach($teamMembers as $member)
            {
                $temp = array();

                $device = Device::where('user_id', $member->user_id)->first('device_name');
                $userInfo = User::where('id', $member->user_id)->first();
                $runs = Run::where('user_id', $member->user_id)->get(); //NEED TO CHANGE - COACH SHOULDNT BE ABLE TO SEE MEMBERS RUNS FROM WHEN THEY WERENT ON THE TEAM

                array_push($temp, $device, $userInfo, $runs);
                array_push($teamArray, $temp);
            }            

            $collection = collect([$teamArray]);
            //dd($collection);

            return view('home', compact('user', 'device', 'team', 'season', 'events', 'teamArray'));
        }

        // If the logged in user is a head coach
        else if($typeID == 4)
        {

            $teamArray = array();

            $currOrg = UserOrg::where('user_id', $id)->first('org_id');

            $orgID = $currOrg->org_id;
            
            $org = Organization::where('id', $orgID)->first();
            $seasons = Season::where('org_id', $orgID)->get();

            foreach($seasons as $season)
            {
                $temp = array();

                $team = Team::where('season_id', $season->id)->first();
                $events = Event::where('id', $team->id)->get();

                array_push($temp, $team, $events);
                array_push($teamArray, $temp);
            }

            $collection = collect([$teamArray]);
            //dd($collection);

            return view('home', compact('user', 'org', 'seasons', 'teamArray'));
        }
    }
}
