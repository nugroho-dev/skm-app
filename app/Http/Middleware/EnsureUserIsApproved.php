<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin_instansi') && ! $user->is_approved) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => __('auth.failed'),
            ]);
        }
        
        return $next($request);
        
    }
}
