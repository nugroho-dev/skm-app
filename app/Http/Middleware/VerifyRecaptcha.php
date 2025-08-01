<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $request->input('g-recaptcha-response');

        if (!$response) {
            return back()->withErrors(['captcha' => 'Harap selesaikan captcha.']);
        }

        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $response,
            'remoteip' => $request->ip(),
        ]);

        $success = $verify->json('success');

        if (!$success) {
            return back()->withErrors(['captcha' => 'Captcha tidak valid.']);
        }

        return $next($request);
    }
}
