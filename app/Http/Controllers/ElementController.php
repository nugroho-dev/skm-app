<?php

namespace App\Http\Controllers;

use App\Models\Unsur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Manajemen Unsur';
        if (Auth::user()->hasRole('super_admin')) {
            $elements= Unsur::orderBy('label_order', 'asc')->paginate(15);
        } else {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        return view('dashboard.element.index', compact('elements','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('dashboard.element.create', [
            'title' => 'Unsur'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:unsurs,name',
                            'label_order' => 'required|number|max:10|unique:unsurs,label_order']);

        Unsur::create([
            'name' => $request->name,
            'label_order' => $request->label_order,
        ]);
        return redirect()->route('unsur.index')->with('success', 'Unsur berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unsur $unsur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unsur $unsur)
    {
        $title = 'Edit Unsur';
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Mengakses halaman ini.');
        }

        return view('dashboard.element.edit', compact('unsur', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unsur $unsur)
    {
        if (Auth::user()->hasRole('admin_instansi') ) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        // Validasi input
       $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:100',
            Rule::unique('unsurs')->ignore($unsur->id),
        ],
        'label_order' => [
            'required',
            'string',
            'max:10',
            Rule::unique('unsurs')->ignore($unsur->id),
        ],
    ]);
        
        // Update data
        $unsur->update($validated);

        return redirect()->route('unsur.index')->with('success', 'Unsur berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unsur $unsur)
    {
        if (Auth::user()->hasRole('admin_instansi')) {
            abort(403, 'Tidak diizinkan Menagkses halaman ini.');
        }

        $unsur->delete();
        return redirect()->route('unsur.index')->with('success', 'Unsur berhasil dihapus');
    }
}
