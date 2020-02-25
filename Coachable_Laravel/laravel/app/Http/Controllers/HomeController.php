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
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        $typeID = $user->user_type_id;

        if($typeID == 1)
        {
            $userTeam = UserTeam::where('user_id', $id)->first('team_id');
            $teamid = $userTeam->team_id;
            $team = Team::where('id', $teamid)->first();
            $season = Season::where('id', $team->season_id)->first();
            $org = Organization::where('id', $season->org_id)->first();
            $events = Event::where('team_id', $teamid)->get();
            $parents = ParentAthlete::where('athlete_id', $id)->first('parent_id');
            $parentUser = User::where('id', $parents->parent_id)->first();
            $runs = Run::where('user_id', $id)->get();

            return view('home', compact('user', 'team', 'season', 'org', 'events', 'parentUser', 'runs'));
        }

        else if($typeID == 2)
        {

            $childArray = array();

            $children = ParentAthlete::where('parent_id', $id)->get('athlete_id');

            foreach($children as $child)
            {
                $temp = array();

                $userTeam = UserTeam::where('user_id', $child->athlete_id)->first('team_id');
                $teamid = $userTeam->team_id;
                $team = Team::where('id', $teamid)->first();
                $season = Season::where('id', $team->season_id)->first();
                $events = Event::where('team_id', $teamid)->get();
                $runs = Run::where('user_id', $child->athlete_id)->get();
                array_push($temp, $team, $season, $events, $runs);
                array_push($childArray, $temp);
            }

            return view('home', compact('user', 'childArray'));
        }

        else if($typeID == 3)
        {
            $teamArray = array();

            $userTeam = UserTeam::where('user_id', $id)->first('team_id');
            $teamid = $userTeam->team_id;
            $team = Team::where('id', $teamid)->first();
            $season = Season::where('id', $team->season_id)->first(); // FIX
            $events = Event::where('team_id', $teamid)->get();
            $teamMembers = UserTeam::where('team_id', $teamid)->get('user_id');

            foreach($teamMembers as $member)
            {
                $temp = array();

                $userInfo = User::where('id', $member->user_id)->first();
                $runs = Run::where('user_id', $member->user_id)->get(); //NEED TO CHANGE - COACH SHOULDNT BE ABLE TO SEE MEMBERS RUNS FROM WHEN THEY WERENT ON THE TEAM

                array_push($temp, $userInfo, $runs);
                array_push($teamArray, $temp);
            }            

            return view('home', compact('user', 'team', 'season', 'events', 'teamArray'));
        }

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

            return view('home', compact('user', 'org', 'seasons', 'teamArray'));
        }
    }
}
