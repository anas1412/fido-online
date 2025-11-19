<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Filament\Actions\Action;
use MartinPetricko\FilamentSentryFeedback\SentryUser;
use Filament\Navigation\NavigationItem;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Filament\Pages\Settings;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            /* ->login() */
            /* ->profile() */
            ->topNavigation()
            /* ->userMenu(position: UserMenuPosition::Sidebar) */
            ->userMenuItems([
                /* 'profile' => fn (Action $action) => $action->label(fn (): string => auth()->user()?->name ?? 'Utilisateur')->icon('heroicon-o-cog-6-tooth'), */
                /* 'edit_profile' => Action::make('profile')
                    ->label('Modifier profil')
                    ->url(url('/dashboard/profile'))
                    ->icon('heroicon-o-cog-6-tooth'), */
                'dashboard_panel' => Action::make('dashboard_panel')
                    ->label('Accéder au Dashboard')
                    ->icon('heroicon-o-home')
                    ->url(url('/dashboard')),
            ])
            // 1. Add our Google Login button before the form
            ->renderHook(
                'panels::auth.login.form.before',
                fn (): string => Blade::render('<x-google-login-button />')
            )
            
            // 2. Add custom CSS to hide the original form
            ->renderHook(
                'panels::styles.after',
                fn (): string => Blade::render('<style>
                    form[wire\\:submit=\'authenticate\'] {
                        display: none;
                    }
                </style>')
            )
            ->brandName('Administration')
            /* ->brandLogo(asset('images/logo.png')) */
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::Blue,
            ])   
            ->spa()
            ->maxContentWidth(Width::Full)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                Settings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->navigationItems([
                /* NavigationItem::make('Paramètres')
                    ->url(fn (): string => \App\Filament\Pages\Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'), */
            ])
            /* ->plugins([
                \MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin::make()
                    ->sentryUser(function (): ?SentryUser {
                        return new SentryUser(auth()->user()->name, auth()->user()->email);
                    }),
            ]) */
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ]);
    }
}
