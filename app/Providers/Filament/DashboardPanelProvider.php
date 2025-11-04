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
use App\Models\Tenant;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use App\Filament\Dashboard\Pages\Tenancy\RegisterTenant;
use Filament\Actions\Action;
use App\Filament\Dashboard\Pages\EditTenantProfile;
use App\Filament\Dashboard\Pages\ManageInvites;
use MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin;
use Filament\Navigation\MenuItem;
use App\Filament\Dashboard\Pages\LeaveTenant;


class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->profile()
            /* ->userMenu(position: UserMenuPosition::Sidebar) */
            ->userMenuItems([
                'profile' => fn (Action $action) => $action->label(fn (): string => auth()->user()?->name ?? 'Utilisateur')->icon('heroicon-o-cog-6-tooth'),
                /* 'edit_profile' => Action::make('profile')
                    ->label('Modifier profil')
                    ->url(url('/dashboard/profile'))
                    ->icon('heroicon-o-cog-6-tooth'), */
                'admin_panel' => Action::make('admin_panel')
                    ->label('Accéder au Admin Panel')
                    ->url(url('/admin'))
                    ->visible(fn (): bool => auth()->user()?->is_admin ?? false),
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
            ->brandName('Fido')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->spa()
            ->maxContentWidth(Width::Full)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->tenant(
                Tenant::class,
                slugAttribute: 'slug', // Use the 'slug' column for tenant URLs
            )


            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class)
            ->tenantMenuItems([
                'leave_tenant' => fn () => \App\Filament\Dashboard\Pages\LeaveTenant::leaveAction()
                
                ->label(fn () => 'Quitter ' . filament()->getTenant()?->name)
                ->icon('heroicon-o-arrow-left-on-rectangle'),
                'billing' => fn (Action $action) => $action->label('Facturation')->visible(fn (): bool => auth()->user()?->is_mod ?? false),
                'profile' => fn (Action $action) => $action->label('Modifier les paramètres')->visible(fn (): bool => auth()->user()?->is_mod ?? false),
                'register' => fn (Action $action) => $action->label('Ajouter une organisation'),
                
            ])
            /* ->tenantDomain('{tenant:slug}.fido.tn') */
            /* ->tenantSlugInPath() */
            ->tenantBillingRouteSlug('payement')
            ->discoverResources(in: app_path('Filament/Dashboard/Resources'), for: 'App\Filament\Dashboard\Resources')
            ->discoverPages(in: app_path('Filament/Dashboard/Pages'), for: 'App\Filament\Dashboard\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Dashboard/Widgets'), for: 'App\Filament\Dashboard\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                \MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin::make()
                    /* ->sentryUser(function (): ?SentryUser {
                            return new SentryUser(auth()->user()->name, auth()->user()->email);
                        }) */
                    ->showName(true)
                    ->isNameRequired(true)
                    ->showEmail(true)
                    ->isEmailRequired(true)
                    ->enableScreenshot(true),
            ])
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
            ]);
    }
}
