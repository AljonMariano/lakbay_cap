<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class AdminPageController extends Controller
{
    public function compute()
    {
        return view('admin.compute');
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function drivers_schedule()
    {
        return view('admin.drivers_schedule');
    }
    public function drivers()
    {
        return view('admin.drivers');
    }
    public function event_calendar()
    {
        return view('admin.event_calendar');
    }
    public function events()
    {
        return view('admin.events');
    }
    public function index()
    {
        return view('admin.index');
    }
    public function navigation_menu()
    {
        return view('admin.navigation-menu');
    }
    public function offices()
    {
        return view('admin.offices');
    }
    public function policy()
    {
        return view('admin.policy');
    }
    public function reservations()
    {
        return view('admin.reservations');
    }
    public function statistics()
    {
        return view('admin.statistics');
    }
    public function terms()
    {
        return view('admin.terms');
    }
    public function test_select()
    {
        return view('admin.test_select');
    }
    public function test_word()
    {
        return view('admin.test_word');
    }
    public function vehicles()
    {
        return view('admin.vehicles');
    }
    public function welcome()
    {
        return view('admin.welcome');
    }
    public function worker()
    {
        return view('admin.worker');
    }
}