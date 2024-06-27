<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPageController  extends Controller
{
    public function compute()
    {
        return view('user.compute');
    }
    public function dashboard()
    {
        return view('user.dashboard');
    }
    public function drivers_schedule()
    {
        return view('user.drivers_schedule');
    }
    public function drivers()
    {
        return view('user.drivers');
    }
    public function event_calendar()
    {
        return view('user.event_calendar');
    }
    public function events()
    {
        return view('user.events');
    }
    public function index()
    {
        return view('user.index');
    }
    public function navigation_menu()
    {
        return view('user.navigation-menu');
    }
    public function offices()
    {
        return view('user.offices');
    }
    public function policy()
    {
        return view('user.policy');
    }
    public function reservations()
    {
        return view('user.reservations');
    }
    public function statistics()
    {
        return view('user.statistics');
    }
    public function terms()
    {
        return view('user.terms');
    }
    public function test_select()
    {
        return view('user.test_select');
    }
    public function test_word()
    {
        return view('user.test_word');
    }
    public function vehicles()
    {
        return view('user.vehicles');
    }
    public function welcome()
    {
        return view('user.welcome');
    }
    public function worker()
    {
        return view('user.worker');
    }
}
