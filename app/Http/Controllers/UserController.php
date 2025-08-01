<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    
    public function index()
    {
    $users = User::role('admin_instansi')->get();
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
}
