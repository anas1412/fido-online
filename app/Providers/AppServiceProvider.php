<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
/* use App\Filament\Dashboard\Pages\Auth\LoginResponse; */
use App\Filament\Dashboard\Pages\Auth\LogoutResponse;
/* use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract; */
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Schedule $schedule): void
    {
        $schedule->command('invites:cleanup')->daily();
    }
}
