<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:'.User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults(), Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),'regex:/^[A-Za-z\d\W_]+$/'],
            'g-recaptcha-response' => 'required',
        ], [
            'name.required' => 'Name is required.',
            'username.unique' => 'Username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'password.regex'=> 'Password can olny contain letters, numbers, and underscores.',
            'password.confirmed' => 'Passwords do not match'
        ]);
        // Verifikasi ke Google reCAPTCHA
        // dd($request->all()); -> ini buat debug ges
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);
        if (!($response->json('success'))) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.',
            ])->withInput();
        }
        


        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' =>  Hash::make($request->password),
        ]);

        event(new Registered($user));
        // Auth::login($user);
        session(['message' => 'verify email']);
        return to_route('login');
    }
}
