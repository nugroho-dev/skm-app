<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
    $query = User::role('admin_instansi');
    // Fitur pencarian
    if ($request->has('search') && $request->search !== null) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereHas('institution', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $users = $query->orderBy('created_at', 'desc')->paginate(20);
    $title = 'Manajemen User';
    return view('dashboard.users.index', compact('users', 'title'));
    }

    public function approve(User $user)
    {
        // Tambahkan ini untuk memastikan hanya super_admin
    if (!auth()->user()->hasRole('super_admin')) {
        abort(403, 'Unauthorized');
    }

    User::where('id', $user->id)->update(['is_approved' => true]);
    return back()->with('success', 'User telah disetujui.');
    }
    public function reject(User $user)
    {
        // Tambahkan ini untuk memastikan hanya super_admin
    if (!auth()->user()->hasRole('super_admin')) {
        abort(403, 'Unauthorized');
    }

    User::where('id', $user->id)->update(['is_approved' => false]);
    return back()->with('success', 'User telah ditolak.');
    }
}
