<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionerController extends Controller
{
   public function index(Request $request)
    {
        $title = 'Manajemen Survey';
        return view('dashboard.questioner.index', compact('title'));
    }
}
