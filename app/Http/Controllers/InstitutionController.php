<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;

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
      public function create()
    {
        $title = 'Tambah Instansi';
        $groups = InstitutionGroup::all();
        $mpps = Mpp::all();
        return view('dashboard.institutions.create',compact('title', 'groups', 'mpps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
        ]);

        Institution::create($request->only('name'));

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function edit(Institution $instansi)
    {
        return view('dashboard.institutions.edit', ['institution' => $instansi]);
    }

    public function update(Request $request, Institution $instansi)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name,' . $instansi->id,
        ]);

        $instansi->update($request->only('name'));

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Institution $instansi)
    {
        $instansi->delete();

        return redirect()->route('instansi.index')->with('success', 'Instansi dihapus.');
    }
}
