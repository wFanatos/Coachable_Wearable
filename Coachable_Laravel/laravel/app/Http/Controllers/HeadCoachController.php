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

class HeadCoachController extends Controller
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
        
                if($typeID != 4)
                {
                    return redirect()->back();
                }

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
