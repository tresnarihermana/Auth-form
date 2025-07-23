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
use App\Http\Middleware\CompleteProfile;
Route::get('/', function () {
    return redirect('login');
    
})->name('home');
Route::get('/CompleteProfile', function () {
    return Inertia::render('settings/CompleteProfile');
})->middleware(['auth'])->name('complete.profile');
Route::get('dashboard', function () {
    return Inertia::render('Dashboard')->with('message', 'Selamat datang');
})->middleware(['auth', 'verified'])->name('dashboard'); /*, 'verified' <- bila ingin wajib verifikasi email */
Auth::routes(['verify' => true]); // untuk verifikasi email
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
    // $avatarUrl = $googleUser->getAvatar();

    // // Ambil ekstensi file dari URL
    // $path = parse_url($avatarUrl, PHP_URL_PATH);
    // $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg'; // default ke jpg kalau kosong

    // $avatarFilename = 'avatar_' . Str::random(20) . '.' . $ext;

    // $avatarContents = file_get_contents($avatarUrl);
    // Storage::disk('public')->makeDirectory('avatar');
    // Storage::disk('public')->put("avatar/{$avatarFilename}", $avatarContents);

    $user = User::firstOrCreate(
        [
            'email' => $googleUser->getEmail(),
        ]

    );


    Auth::login($user);

    // if (!$user->username || !$user->name || !$user->password) {
    //     return redirect('/settings/profile')->with('message', 'Silakan lengkapi profil Anda terlebih dahulu.');
    // }

    return redirect('/dashboard')->with("message", "Selamat datang");
});


Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
