<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occupation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OccupationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Manajemen Pekerjaan';
        if (Auth::user()->hasRole('super_admin')) {
            $occupations= Occupation::paginate(15);
        } else {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        return view('dashboard.occupation.index', compact('occupations','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.occupation.create', [
            'title' => 'Pekerjaan'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['type' => 'required|string|max:100|unique:occupations,type']);

        Occupation::create([
            'type' => $request->type
        ]);
        return redirect()->route('pekerjaan.index')->with('success', 'Pekerjaan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Occupation $pekerjaan)
    {
        $title = 'Edit Pekerjaan';
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Mengakses halaman ini.');
        }

        return view('dashboard.occupation.edit', compact('pekerjaan', 'title'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Occupation $pekerjaan)
    {
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:100',
                Rule::unique('occupations')->ignore($pekerjaan->id),
            ],
        ]);
       
        $pekerjaan->update($validated);

        return redirect()->route('pekerjaan.index')->with('success', 'Pekerjaan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Occupation $pekerjaan)
    {
        if (Auth::user()->hasRole('admin_instansi')) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        $pekerjaan->delete();
        return redirect()->route('pekerjaan.index')->with('success', 'pekerjaan berhasil dihapus');

    }
}
