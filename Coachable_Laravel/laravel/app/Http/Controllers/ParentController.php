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

class ParentController extends Controller
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
                'user_type_id')->where('id', $id)->first();
    
            // Get the users type
            $typeID = $user->user_type_id;
    
            if($typeID != 2)
            {
                return redirect()->back();
            }

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
}
