<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Event;
use App\Run;


class EventController extends Controller
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

    public function index($userid, $runid)
    {
        // MAKE SURE THE REQUESTED USER IS THE ONE LOGGED IN/IN SYSTEM
        // CHECK TO MAKE SURE THE EVENT REQUESTED IS VALID
        // IF EVERYTHING IS OK, CONTINUE
 
        // GIVEN EVENTID AND USERID, RETURN ALL RUNS
        // SEND RUNS BACK TO VIEW FOR PARSING/DISPLAYING

        return view('event');
    }
}
