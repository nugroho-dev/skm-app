<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Layanan Instansi';
        
        //dd($request->institution);
        // Jika user adalah super_admin, tampilkan semua layanan
        // Jika bukan, tampilkan layanan berdasarkan instansi user
        if (auth()->user()->hasRole('super_admin')) {
            
            $institution = Institution::where('slug', $request->institution)->first();
            if ($institution) {
                $title = 'Layanan untuk ' . $institution->name;
                $institution_slug = $institution->slug;
            } else {
                return redirect()->route('institutions.index')->with('error', 'Instansi tidak ditemukan.');
            }
            
            $services = Service::with('institution')->where('institution_id', $institution->id)->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })->paginate(10)->withQueryString();
        }else {
            $title = 'Layanan untuk ' . auth()->user()->institution->name;
            $institution_slug = '';
            $services = Service::with('institution')
                ->where('institution_id', auth()->user()->institution_id)
                ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
                })->paginate(10)->withQueryString();
             
        }

        return view('dashboard.services.index', compact('services', 'title', 'institution_slug'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         
         if (auth()->user()->hasRole('super_admin')) {
            $institution = Institution::where('slug', $request->institution)->first();
            if ($institution) {
                $title = 'Layanan untuk ' . $institution->name;
                $institution_slug = $institution->slug;
            } else {
                return redirect()->route('services.index')->with('error', 'Instansi tidak ditemukan.');
            }
        }else {
            $title = 'Layanan untuk ' . auth()->user()->institution->name;
            $institution_slug = '';
        }
        return view('dashboard.services.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->institution);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
