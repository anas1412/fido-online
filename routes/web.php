<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/onboarding', function () {
    // This will be a full page component later
    return 'Welcome! Please create or join a tenant.';
})->middleware('auth')->name('onboarding');
