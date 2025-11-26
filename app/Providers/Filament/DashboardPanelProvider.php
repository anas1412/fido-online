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
use App\Filament\Dashboard\Pages\Auth\Login;
use Alareqi\FilamentPwa\FilamentPwaPlugin;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use App\Filament\Dashboard\Pages\AIHelp;


class DashboardPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END,
            fn (): string => Blade::render(<<<'HTML'
                <li class="fi-sidebar-item mt-auto list-none" x-data>
                    <button
                        id="pwa-sidebar-button"
                        onclick="window.pwaInstaller.installApp()"
                        style="display: none;"
                        class="fi-sidebar-item-button w-full relative rounded-lg px-2 py-2 text-sm outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 font-medium text-gray-700 dark:text-gray-200"
                    >
                        <div class="flex items-center gap-3">
                            <svg 
                                class="h-6 w-6 text-gray-400 dark:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>

                            <span class="truncate" x-show="$store.sidebar.isOpen">
                                Installer l'application
                            </span>
                        </div>
                    </button>

                </li>
            HTML)
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END, 
            fn (): string => Blade::render('<x-tenant-plan-widget />')
        );


    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login(Login::class)
            /* ->profile() */
            /* ->userMenu(position: UserMenuPosition::Sidebar) */
            ->userMenuItems([
                /* 'edit_profile' => Action::make('profile')
                    ->label('Modifier vos paramètres')
                    ->url(url('/dashboard/profile'))
                    ->icon('heroicon-o-cog-6-tooth'),  */
                'admin_panel' => Action::make('admin_panel')
                    ->label('Accéder au Admin Panel')
                    ->url(url('/admin'))
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn (): bool => auth()->user()?->is_admin ?? false),
            ])
            
            
            /* // 1. Add our Google Login button before the form
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
            ) */
            ->brandName('Fido')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => '#6fbf44',
            ])
            ->topbar(false)
            ->spa()

            ->maxContentWidth(Width::Full)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->collapsibleNavigationGroups(false)
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false)
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
                /* FilamentInfoWidget::class, */
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
                        ['title' => 'À propos', 'url' => url('/about')],
                        ['title' => 'Mentions légales', 'url' => url('/legal')],
                        ['title' => 'Politique de confidentialité', 'url' => url('/privacy-policy')],
                    ])
                    ->hiddenFromPages([ 'dashboard/login', 'dashboard/new', 'dashboard/profile' ])
                    ->hiddenFromPagesEnabled(),
                FilamentPwaPlugin::make()
                    ->name('Fido')
                    ->shortName('Fido')
                    ->description('Votre tableau de bord Fido')
                    ->enableInstallation()
                    /* ->enableDebugBanner() */
                    ->startUrl('/dashboard')
                    ->scope('/')
                    ->themeColor('#18181b')
                    ->backgroundColor('#ffffff')
                    ->standalone()
                    ->landscape() // Consider using 'portrait' or 'natural' for mobile phones
                    ->language('fr')
                    ->addShortcut('Dashboard', '/dashboard', 'Accéder au tableau de bord')
                    ->serviceWorker(
                        cacheName: 'my-app-v3.0.0',
                        offlineUrl: '/offline',
                        cacheUrls: ['/dashboard'], 
                    )
                    ->icons('images/icons', [72, 96, 128, 144, 152, 192, 384, 512]),
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
