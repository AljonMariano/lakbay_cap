<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPageController  extends Controller
{
    public function compute()
    {
        return view('users.compute');
    }
    public function dashboard()
    {
        return view('users.dashboard');
    }
    public function drivers_schedule()
    {
        return view('users.drivers_schedule');
    }
    public function drivers()
    {
        return view('users.drivers');
    }
    public function event_calendar()
    {
        return view('users.event_calendar');
    }
    public function events()
    {
        return view('users.events');
    }
    public function index()
    {
        return view('users.index');
    }
    public function navigation_menu()
    {
        return view('ususerser.navigation-menu');
    }
    public function offices()
    {
        return view('users.offices');
    }
    public function policy()
    {
        return view('users.policy');
    }
    public function reservations()
    {
        return view('users.reservations');
    }
    public function statistics()
    {
        return view('users.statistics');
    }
    public function terms()
    {
        return view('users.terms');
    }
    public function test_select()
    {
        return view('users.test_select');
    }
    public function test_word()
    {
        return view('users.test_word');
    }
    public function vehicles()
    {
        return view('users.vehicles');
    }
    public function welcome()
    {
        return view('users.welcome');
    }
    public function worker()
    {
        return view('users.worker');
    }

    public function requestors()
    {
        return view('requestor.requestors');
    }

    public function profile()
    {
        return view('requestor.requestors');
    }

}
