<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantInvite;
use Filament\Facades\Filament;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Invite link
Route::get('/invite/{code}', function (string $code) {

    $invite = TenantInvite::where('code', $code)
        ->whereNull('used_by')
        ->where('expires_at', '>', now())
        ->first();

    if (! $invite) {
        return view('invite.invalid', ['code' => $code]);
    }

    // If user is not logged in, redirect to login/registration
    if (! Auth::check()) {
        session(['url.intended' => url("/invite/{$code}")]);
        return redirect()->route('login'); // or your registration page
    }

    $tenant = $invite->tenant;

    // If user is already part of tenant, redirect immediately
    if (Auth::user()->tenants->contains($tenant)) {
        return redirect(Filament::getPanel('dashboard')->getUrl(tenant: $tenant));
    }

    // Show join/cancel page
    return view('invite.valid', [
        'code' => $code,
        'tenant' => $tenant,
    ]);
})->name('invite');

// Handle Join action
Route::post('/invite/{code}/join', function (string $code) {

    if (! Auth::check()) {
        session(['url.intended' => url("/invite/{$code}")]);
        return redirect()->route('login'); // force login first
    }

    $invite = TenantInvite::where('code', $code)
        ->whereNull('used_by')
        ->where('expires_at', '>', now())
        ->first();

    if (! $invite) {
        return view('invite.invalid', ['code' => $code]);
    }

    $user = Auth::user();

    // Attach user to tenant
    $user->tenants()->syncWithoutDetaching([$invite->tenant_id]);

    // Mark invite as used
    $invite->update(['used_by' => $user->id]);

    $tenant = $invite->tenant;

    // Redirect to tenant dashboard
    return redirect(Filament::getPanel('dashboard')->getUrl(tenant: $tenant));
})->name('invite.join');
