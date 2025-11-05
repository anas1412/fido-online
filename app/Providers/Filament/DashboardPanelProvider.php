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
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;

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
                'edit_profile' => Action::make('profile')
                    ->label('Modifier vos paramètres')
                    ->url(url('/dashboard/profile'))
                    ->icon('heroicon-o-cog-6-tooth'), 
                'admin_panel' => Action::make('admin_panel')
                    ->label('Accéder au Admin Panel')
                    ->url(url('/admin'))
                    ->icon('heroicon-o-shield-check')
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
            ->topbar(false)
            ->spa()
            ->maxContentWidth(Width::Full)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->collapsibleNavigationGroups(false)
            ->sidebarCollapsibleOnDesktop()

            /* ->navigationItems([
                NavigationItem::make('Analytics')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Reports')
                    ->sort(3),
            ]) */


            ->tenant(
                Tenant::class,
                slugAttribute: 'slug', // Use the 'slug' column for tenant URLs
            )


            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class)
            ->tenantMenuItems([
                'leave_tenant' => fn () => \App\Filament\Dashboard\Pages\LeaveTenant::leaveAction()
                ->label(function () {
                    $user = Auth::user();
                    $tenant = filament()->getTenant();
                    $isOwner = $user->tenants()->where('tenant_id', $tenant->id)->wherePivot('is_owner', true)->exists();
                    return $isOwner ? 'Supprimer ' . ($tenant?->name ?? 'l\'organisation') : 'Quitter ' . ($tenant?->name ?? 'l\'organisation');
                })
                ->icon(function () {
                    $user = Auth::user();
                    $tenant = filament()->getTenant();
                    $isOwner = $user->tenants()->where('tenant_id', $tenant->id)->wherePivot('is_owner', true)->exists();
                    return $isOwner ? 'heroicon-o-trash' : 'heroicon-o-arrow-left-on-rectangle';
                }),
                'profile' => fn (Action $action) => $action->label('Modifier les paramètres'),
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
                /* \MartinPetricko\FilamentSentryFeedback\FilamentSentryFeedbackPlugin::make()
                    /* ->sentryUser(function (): ?SentryUser {
                            return new SentryUser(auth()->user()->name, auth()->user()->email);
                        }) 
                    ->showName(true)
                    ->isNameRequired(true)
                    ->showEmail(true)
                    ->isEmailRequired(true)
                    ->enableScreenshot(true), */

                EasyFooterPlugin::make()
                ->withBorder()
                ->withFooterPosition('footer')
                /* ->withGithub(showLogo: true, showUrl: true) */
                /* ->withLoadTime('Cette page a été chargée en') */
                ->withLogo(asset('images/logo.png'))
                ->withLinks([
                    ['title' => 'À propos', 'url' => 'https://example.com/about'],
                    ['title' => 'Mentions légales', 'url' => 'https://example.com/legal'],
                    ['title' => 'Politique de confidentialité', 'url' => 'https://example.com/privacy-policy'],
                ])
                ->hiddenFromPages([ 'dashboard/login', 'dashboard/new', 'dashboard/profile' ])
                ->hiddenFromPagesEnabled(),
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
            ])
            ->viteTheme('resources/css/filament/dashboard/theme.css');
    }
}
