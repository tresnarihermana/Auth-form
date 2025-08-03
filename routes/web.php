<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Session\Middleware\AuthenticateSession;
use App\Http\Middleware\EnsureProfileComplete;
Route::get('/', function () {
    return redirect('login');
    
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard')->with('message', 'Selamat datang');
})->middleware(['auth', 'verified'])->name('dashboard'); /*, 'verified' <- bila ingin wajib verifikasi email */
Auth::routes(['verify' => true]); // untuk verifikasi email
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');
Route::get('/clear-message', function () {
    session()->forget('message');
    return redirect()->back();
});
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user = User::firstOrCreate(
        [
            'email' => $googleUser->getEmail(),
        ]

    );


    Auth::login($user);
    return redirect('/dashboard')->with("message", "Selamat datang");
});


Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
