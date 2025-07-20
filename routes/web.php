<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Session\Middleware\AuthenticateSession;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard'); /*, 'verified' <- bila ingin wajib verifikasi email */
// Auth::routes(['verify' => true]); // untuk verifikasi email
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
$avatarUrl = $googleUser->getAvatar();

// Ambil ekstensi file dari URL
$path = parse_url($avatarUrl, PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg'; // default ke jpg kalau kosong

$avatarFilename = 'avatar_' . Str::random(20) . '.' . $ext;

$avatarContents = file_get_contents($avatarUrl);
Storage::disk('public')->makeDirectory('avatar');
Storage::disk('public')->put("avatar/{$avatarFilename}", $avatarContents);

    $user = User::firstOrCreate([
        'email' => $googleUser->getEmail(),
    ], [
        'username' => $googleUser->getName(),
        'password' => bcrypt(Str::random(24)),
        'avatar' => "avatar/{$avatarFilename}",
        'remember_token' => Str::random(10),
        'email_verified_at' => now(),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
