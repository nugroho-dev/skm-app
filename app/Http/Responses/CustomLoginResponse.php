<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->intended('/dashboard');
        } elseif ($user->hasRole('admin_instansi')) {
            return redirect()->intended('/dashboard/instansi');
        }

        return redirect('/'); // default fallback
    }
}
