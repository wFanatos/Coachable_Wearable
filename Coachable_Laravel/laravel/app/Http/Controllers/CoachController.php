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
                    'user_type_id')->where('id', $id)->first();
        
                // Get the users type
                $typeID = $user->user_type_id;
        
                if($typeID != 3)
                {
                    return redirect()->back();
                }

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
}
