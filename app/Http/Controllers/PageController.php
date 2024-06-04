<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function compute()
    {
        return view('compute');
    }
    public function dashboard()
    {
        return view('dashboard');
    }
    public function drivers_schedule()
    {
        return view('drivers_schedule');
    }
    public function drivers()
    {
        return view('drivers');
    }
    public function event_calendar()
    {
        return view('event_calendar');
    }
    public function events()
    {
        return view('events');
    }
    public function index()
    {
        return view('index');
    }
    public function navigation_menu()
    {
        return view('navigation-menu');
    }
    public function offices()
    {
        return view('offices');
    }
    public function policy()
    {
        return view('policy');
    }
    public function reservations()
    {
        return view('reservations');
    }
    public function statistics()
    {
        return view('statistics');
    }
    public function terms()
    {
        return view('terms');
    }
    public function test_select()
    {
        return view('test_select');
    }
    public function test_word()
    {
        return view('test_word');
    }
    public function vehicles()
    {
        return view('vehicles');
    }
    public function welcome()
    {
        return view('welcome');
    }
    public function worker()
    {
        return view('worker');
    }
}
