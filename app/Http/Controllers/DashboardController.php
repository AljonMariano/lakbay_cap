<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (auth()->user()->isAdmin()) {
            return view('admin.dashboard');
        } else {
            return view('users.dashboard');
        }
    }
}
