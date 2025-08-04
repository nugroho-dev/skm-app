<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Manajemen Instansi';
        $institutions = Institution::when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.institutions.index', compact('institutions','title'));
    }
}
