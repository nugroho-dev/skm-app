<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('admin_instansi') && !$user->approved) {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda belum disetujui oleh admin.',
            ]);
        }
        
        return $next($request);
        
    }
}
