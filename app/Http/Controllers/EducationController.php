<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Manajemen Pendidikan';
        if (Auth::user()->hasRole('super_admin')) {
            $educations = Education::paginate(15);
        } else {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        return view('dashboard.education.index', compact('educations','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.education.create', [
            'title' => 'Pendidikan'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate(['level' => 'required|string|max:100']);

        Education::create([
            'level' => $request->level
        ]);
        return redirect()->route('pendidikan.index')->with('success', 'Pendidikan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Education $education)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Education $pendidikan)
    {
        $title = 'Edit Pendidikan';
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        return view('dashboard.education.edit', compact('pendidikan', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Education $pendidikan)
    {
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        $request->validate(['level' => 'required|string|max:100']);
        $pendidikan->update(['level' => $request->level]);

        return redirect()->route('pendidikan.index')->with('success', 'Pendidikan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $pendidikan)
    {
        if (Auth::user()->hasRole('admin_instansi')) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        $pendidikan->delete();
        return redirect()->route('pendidikan.index')->with('success', 'Pendidikan berhasil dihapus');
    }
}
