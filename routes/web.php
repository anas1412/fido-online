<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/invite/{code}', function (string $code) {
    session(['invite_code' => $code]);

    if (Auth::check()) {
        return redirect()->route('filament.dashboard.pages.onboarding');
    }

    return redirect()->route('login');
})->name('invite');

