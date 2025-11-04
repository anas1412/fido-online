<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            \Log::info('Handling Google callback.');

            $googleUser = Socialite::driver('google')->user();
            \Log::info('Google user retrieved:', ['user' => $googleUser->getName()]);

            // Find a user by their google_id, or create a new one
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    // 'tenant_id' and 'is_admin' will remain at their defaults (null/false)
                ]
            );
            \Log::info('User created or updated:', ['user_id' => $user->id]);

            Auth::login($user);
            \Log::info('User logged in:', ['user_id' => $user->id]);

            // Redirect to the intended URL, or the default dashboard if no intended URL is set.
            return redirect()->intended(filament()->getUrl());

        } catch (\Exception $e) {
            // Handle exceptions (e.g., user denies access)
            \Log::error('Google login failed:', ['error' => $e->getMessage()]);
            return redirect('/dashboard')->with('error', 'Login with Google failed. Please try again.');
        }
    }
}
