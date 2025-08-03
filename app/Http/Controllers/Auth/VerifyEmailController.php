<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1')->with('message', 'Selamat Datang');
        }

        if ($request->user()->markEmailAsVerified()) {
            $user = $request->user();

            if (!empty($user->username) || !empty($user->name) || !empty($user->password)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                session(['message' => 'verify email']);
                return redirect('/login');
            }

            event(new Verified($user));
        }

        return redirect()->intended(route('settings/profile', absolute: false) . '?verified=1')->with('message', 'Selamat Datang');
    }
}
