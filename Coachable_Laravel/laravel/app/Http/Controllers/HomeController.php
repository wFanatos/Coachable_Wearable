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
            return redirect()->route('athlete');
        }

        // If the logged in user is a parent
        else if($typeID == 2)
        {
            return redirect()->route('parent');
        }

        // If the logged in user is a coach
        else if($typeID == 3)
        {
            return redirect()->route('coach');
        }

        // If the logged in user is a head coach
        else if($typeID == 4)
        {
            return redirect()->route('head');          
        }
    }
}
