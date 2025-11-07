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

    $tenant = $invite->tenant;

    // If user is not logged in, store invite code and show join/cancel page
    if (! Auth::check()) {
        session(['invite_code' => $code]);
        return view('invite.valid', [
            'code' => $code,
            'tenant' => $tenant,
        ]);
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

    session(['invite_code' => $code]);
            return redirect('/dashboard/login');
})->name('invite.join');

Route::view('/about', 'pages.about')->name('about');
Route::view('/legal', 'pages.legal')->name('legal');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy-policy');
