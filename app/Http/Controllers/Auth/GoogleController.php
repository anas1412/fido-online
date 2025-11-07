<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Filament\Facades\Filament;

use App\Models\TenantInvite;

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
                ]
            );
            \Log::info('User created or updated:', ['user_id' => $user->id]);

            Auth::login($user);
            \Log::info('User logged in:', ['user_id' => $user->id]);

            // Check for invite code in session
            if (session()->has('invite_code')) {
                $inviteCode = session('invite_code');
                session()->forget('invite_code'); // Clear the invite code from session

                $invite = TenantInvite::where('code', $inviteCode)
                    ->whereNull('used_by')
                    ->where('expires_at', '>', now())
                    ->first();

                if ($invite) {
                    // Attach user to tenant
                    $user->tenants()->syncWithoutDetaching([$invite->tenant_id]);

                    // Mark invite as used
                    $invite->update(['used_by' => $user->id]);

                    $tenant = $invite->tenant;
                    \Log::info('User joined tenant via invite:', ['user_id' => $user->id, 'tenant_id' => $tenant->id]);

                    // Redirect to tenant dashboard
                    return redirect(Filament::getPanel('dashboard')->getUrl(tenant: $tenant));
                } else {
                    \Log::warning('Invalid or expired invite code in session:', ['code' => $inviteCode, 'user_id' => $user->id]);
                    // If invite is invalid/expired, proceed with normal login flow
                }
            }

            // Redirect to the intended URL, or the default dashboard if no intended URL is set.
            return redirect()->intended(filament()->getUrl());

        } catch (\Exception $e) {
            // Handle exceptions (e.g., user denies access)
            \Log::error('Google login failed:', ['error' => $e->getMessage()]);
            return redirect('/')->with('error', 'Login with Google failed. Please try again.');
        }
    }
}
