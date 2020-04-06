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
        $user = User::Select('user_type_id')->where('id', $id)->first();
    
        // Get the users type
        $typeID = $user->user_type_id;
    
        // Ensure the current user is actually a parent/guardian
        if($typeID != 2)
        {
            return redirect()->back();
        }

        // Array for storing child information
        $childArray = array();


        // Get all children attached to parent
        $children = ParentAthlete::where('parent_id', $id)->get('athlete_id');

        // Loop through each child and get their information
        foreach($children as $child)
        {
            $runArray = array();

            $childInfo = User::Select('name')->where('id', $child->athlete_id)->first();
            $userTeam = UserTeam::where('user_id', $child->athlete_id)->first('team_id');
            $teamid = $userTeam->team_id;
            $team = Team::where('id', $teamid)->first();           
            $events = Event::where('team_id', $teamid)->get();

            // Loop through each event and grab a list of runs attached to user
            foreach($events as $event)
            {
                $temp = array();
                $runs = Run::Select('distance')->where('user_id', $child->athlete_id)->where('event_id', $event->id)->get();
                $runCount = $runs->count();
                array_push($temp, $event, $runs, $runCount);
                array_push($runArray, $temp);                
            }

            
            $temp2 = array();

            array_push($temp2, $childInfo, $team, $runArray);
            array_push($childArray, $temp2);
        }

        //dd($runArray);

        $collection = collect($childArray);  

        return view('parent', compact('collection'));
    }
}
