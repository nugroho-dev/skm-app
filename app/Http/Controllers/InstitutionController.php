<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'name' => 'required|string|max:255',
            'institution_group' => 'required|string|max:255',
            'mpp' => 'required|string|max:255'
        ]);

        $institutionGroup = InstitutionGroup::where('slug', $request->institution_group)->firstOrFail();
        $mpp = Mpp::where('slug', $request->mpp)->firstOrFail();

        // biarkan model yang membuat slug via HasSlug trait
        Institution::create([
            'name' => $request->name,
            'institution_group_id' => $institutionGroup->id,
            'mpp_id' => $mpp->id,
        ]);

        return redirect()->route('institutions.index')->with('success', 'Instansi berhasil ditambahkan.');
    }

    // gunakan route-model-binding berdasarkan slug
    public function edit(Institution $institution)
    {
        $title = 'Edit Instansi';
        $groups = InstitutionGroup::all();
        $mpps = Mpp::all();

        return view('dashboard.institutions.edit', [
            'institution' => $institution,
            'title' => $title,
            'groups' => $groups,
            'mpps' => $mpps,
        ]);
    }

    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'name' => ['required','string','max:255', \Illuminate\Validation\Rule::unique('institutions','name')->ignore($institution->id)],
            'institution_group' => 'required|string|max:255',
            'mpp' => 'required|string|max:255'
        ]);

        $institutionGroup = InstitutionGroup::where('slug', $request->institution_group)->firstOrFail();
        $mpp = Mpp::where('slug', $request->mpp)->firstOrFail();

        // biarkan model menangani slug; cukup update field lain
        $institution->update([
            'name' => $request->name,
            'institution_group_id' => $institutionGroup->id,
            'mpp_id' => $mpp->id,
        ]);

        return redirect()->route('institutions.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return redirect()->route('institutions.index')->with('success', 'Instansi dihapus.');
    }
}
