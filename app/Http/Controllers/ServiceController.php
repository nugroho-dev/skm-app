<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        // Tentukan instansi berdasarkan role
        if ($user->hasRole('super_admin')) {
            $institution = Institution::find($request->institution);
            if (!$institution) {
            return redirect()->route('institutions.index')->with('error', 'Instansi tidak ditemukan.');
            }
        } else {
            $institution = $user->institution;
            // Cegah admin_instansi melihat layanan instansi lain
            if ($user->hasRole('admin_instansi') && $request->institution && $user->institution_id != $request->institution) {
            abort(403, 'Tidak diizinkan melihat layanan instansi lain.');
            }
        }

        $title = 'Layanan untuk ' . $institution->name;
        $institution_id = $institution->id;

        $services = Service::with('institution')
            ->where('institution_id', $institution->id)
            ->when($request->search, fn($query) => $query->where('name', 'like', "%{$request->search}%"))
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.services.index', compact('services', 'title', 'institution_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         
        $user = Auth::user();
        $institution = $user->hasRole('super_admin')
            ? Institution::find($request->institution)
            : $user->institution;

        if (!$institution->exists()) {
            return redirect()->route('services.index')->with('error', 'Instansi tidak ditemukan.');
        }

        $title = 'Layanan untuk ' . $institution->name;
        $institution_id = $institution->id;

        return view('dashboard.services.create', compact('title', 'institution_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $user = Auth::user();

        // Cegah admin instansi menambah layanan di instansi lain
        if ($user->hasRole('admin_instansi') && $validated['institution_id'] != $user->institution_id) {
            abort(403, 'Tidak diizinkan menambah layanan untuk instansi lain.');
        }

        Service::create($validated);

        // Redirect sesuai role
        return redirect()->route(
            $user->hasRole('super_admin') ? 'service.index' : 'instansi.services.index',
            $user->hasRole('super_admin') ? ['institution' => $validated['institution_id']] : []
        )->with('success', 'Layanan berhasil ditambahkan.');
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
        
    // Tidak perlu menulis $services di sini, karena parameter $services sudah otomatis berisi data dari route model binding.
    // Jika Anda menulis hanya "$services", itu tidak melakukan apa-apa dan tidak mengubah nilai apapun.
    // Pastikan route Anda menggunakan {service} dan bukan {services} agar binding sesuai dengan model Service.
    // Contoh: Route::get('services/{service}/edit', [ServiceController::class, 'edit']);
    $user = Auth::user();
    $title = 'Edit Layanan Instansi';
    // Ganti pengecekan role sesuai implementasi Anda, misal menggunakan atribut 'role'
    if ($user->role === 'admin_instansi' &&
        $service->institution_id != $user->institution_id) {
        abort(403, 'Tidak diizinkan mengedit layanan instansi lain.');
    }

    $institutions = $user->role === 'super_admin' ? Institution::all() : Institution::where('id', $user->institution_id)->get();
    $institution_id = $service->institution_id;
        return view('dashboard.services.edit', compact('service', 'institutions', 'title','institution_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();

        // Cegah admin_instansi mengedit layanan instansi lain atau memindahkan layanan ke instansi lain
        if ($user->hasRole('admin_instansi')) {
            if ($service->institution_id != $user->institution_id || $request->institution_id != $user->institution_id) {
                abort(403, 'Tidak diizinkan mengubah layanan instansi lain.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $service->update($validated);

        return redirect()->route(
            $user->hasRole('super_admin') ? 'service.index' : 'instansi.services.index',
            $user->hasRole('super_admin') ? ['institution' => $validated['institution_id']] : []
        )->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();

        // Cegah admin_instansi menghapus layanan instansi lain
        if ($user->hasRole('admin_instansi') && $service->institution_id != $user->institution_id) {
            abort(403, 'Tidak diizinkan menghapus layanan instansi lain.');
        }

        $institution_id = $service->institution_id;
        $service->delete();

        return redirect()->route(
            $user->hasRole('super_admin') ? 'service.index' : 'instansi.services.index',
            $user->hasRole('super_admin') ? ['institution' => $institution_id] : []
        )->with('success', 'Layanan berhasil dihapus.');
    
    }
}
