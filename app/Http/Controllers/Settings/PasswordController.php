<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        if(Auth::user()->password === null){
            $currentPasswordRule = ['nullable'];
        }else{
            $currentPasswordRule =   ['required', 'current_password','regex:/^[A-Za-z\d\W_]+$/'];
        }
        $validated = $request->validate([
            'current_password' => $currentPasswordRule,
            'password' => ['required', Password::defaults(), 'confirmed', Password::min(8)->numbers()->symbols(), 'max:255' ,'regex:/^[A-Za-z\d\W_]+$/'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
