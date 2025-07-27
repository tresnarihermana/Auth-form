<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cegah loop: jika sedang di halaman profile, lewati middleware ini

        if ($request->is(['logout', 'settings/profile','settings/password' ,'settings/appearance'])) {
            return $next($request);
        }
        if (!auth()->check()) {
            return $next($request);
        }
        if (!$request->user()->hasVerifiedEmail()) {
            return $next($request);
        }
        if (auth()->check()) {
            if (
                !$request->user()?->username ||
                !$request->user()?->name
            ) {
                return redirect('/settings/profile')
                    ->with('message', 'Silakan lengkapi profil Anda terlebih dahulu.');
            } elseif (!$request->user()?->password) {
                return redirect('/settings/password')->with('message', 'Silakan lengkapi password Anda terlebih dahulu.');
            }
            // if (
            //     !$request->user()?->password){
            //         return redirect('/settings/password')
            //         ->with('message', 'Silakan lengkapi password Anda terlebih dahulu.');
            //     }
        }
        return $next($request);
    }
}
