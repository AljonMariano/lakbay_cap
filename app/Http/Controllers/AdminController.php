<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function driversSchedule()
    {
        return view('admin.drivers_schedule');
    }

    public function drivers()
    {
        return view('admin.drivers');
    }

    public function eventCalendar()
    {
        return view('admin.event_calendar');
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

    public function testSelect()
    {
        return view('admin.test_select');
    }

    public function testWord()
    {
        return view('admin.test_word');
    }

    public function vehicles()
    {
        return view('admin.vehicles');
    }

    public function worker()
    {
        return view('admin.worker');
    }
    

 
}
