<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\Settings\ProfileUpdateRequest;

class CompleteProfileController extends Controller
{


    public function completeprofile(Request $request): Response
    {
        return Inertia::render('settings/Completeprofile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }
    public function complete(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults(), Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),'regex:/^[a-zA-Z0-9_]+$/'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->user()->save();
        return redirect()->route('dashboard')->with('message', 'Selamat Datang');
    }
}
