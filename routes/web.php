<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Session\Middleware\AuthenticateSession;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth' ])->name('dashboard'); /*, 'verified' <- bila ingin wajib verifikasi email */
// Auth::routes(['verify' => true]); // untuk verifikasi email
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::firstOrCreate([
        'email' => $googleUser->getEmail(),
    ], [
        'username' => $googleUser->getName(),
        'password' => bcrypt(Str::random(24)),
        'avatar' => $googleUser->getAvatar(),
        'email_verified_at' => now(),
        
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
