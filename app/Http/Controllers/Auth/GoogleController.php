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
            $googleUser = Socialite::driver('google')->user();

            // Find a user by their google_id, or create a new one
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    // 'tenant_id' and 'is_admin' will remain at their defaults (null/false)
                ]
            );

            Auth::login($user);

            // REDIRECTION LOGIC: This is key to your app flow
            
            // 1. Admins go to the admin panel
            if ($user->is_admin) {
                return redirect()->intended('/admin');
            }

            // 2. Users with a tenant go to their dashboard
            if ($user->tenant_id) {
                return redirect()->intended('/dashboard');
            }

            // 3. New users without a tenant go to the onboarding page
            // We will create this page in the next step.
            return redirect()->route('onboarding');

        } catch (\Exception $e) {
            // Handle exceptions (e.g., user denies access)
            return redirect('/login')->with('error', 'Login with Google failed. Please try again.');
        }
    }
}
