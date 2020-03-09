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
        $user = User::Select(
            'name', 'email', 'user_type_id')->where('id', $id)->first();

        // Get the users type
        $typeID = $user->user_type_id;

        // Ensure the current user is actually an athlete
        if($typeID != 1)
        {
            return redirect()->back();
        }

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
            array_push($temp, $event, $run);
            array_push($runArray, $temp);
        }

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
        $collection = collect([$user, $device, $team, $season, $org, $parentArray, $runArray]);

        //dd($collection);

        return view('athlete', compact('collection'));
    }
}
