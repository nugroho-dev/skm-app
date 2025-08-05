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
            'institution_group' => 'required|string|max:255',
            'mpp' => 'required|string|max:255'
        ]);

        $institutionGroup = InstitutionGroup::where('slug', $request->institution_group)->firstOrFail();
        $mpp = Mpp::where('slug', $request->mpp)->firstOrFail();

        Institution::create([
            'name' => $request->name,
            'institution_group_id' => $institutionGroup->id,
            'mpp_id' => $mpp->id
        ]);

        return redirect()->route('institutions.index')->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function edit(string $slug)
    {
        $instansi = Institution::where('slug', $slug)->first();
        

        // Pastikan instansi yang diminta ada
        if (!$instansi) {
            return redirect()->route('institutions.index')->with('error', 'Instansi tidak ditemukan.');
        }

        // Ambil data grup dan MPP untuk dropdown
        $title = 'Edit Instansi';
        $groups = InstitutionGroup::all();
        $mpps = Mpp::all();

        return view('dashboard.institutions.edit', [
            'institution' => $instansi,
            'title' => $title,
            'groups' => $groups,
            'mpps' => $mpps,
        ]);
    }

    public function update(Request $request, Institution $institution)
    {
        
        $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name,' . $institution->id,
            'institution_group' => 'required|string|max:255,' . $institution->slug,
            'mpp' => 'required|string|max:255,'. $institution->slug
        ]);
        $institutionGroup = InstitutionGroup::where('slug', $request->institution_group)->firstOrFail();
        $mpp = Mpp::where('slug', $request->mpp)->firstOrFail();
        $institution->update([
            'name' => $request->name,
            'institution_group_id' => $institutionGroup->id,
            'mpp_id' => $mpp->id
        ]);

        return redirect()->route('institutions.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return redirect()->route('institutions.index')->with('success', 'Instansi dihapus.');
    }
}
