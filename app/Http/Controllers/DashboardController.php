<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $title = 'Dashboard';
        return view('dashboard.index', compact('user', 'title'));
    }
}
