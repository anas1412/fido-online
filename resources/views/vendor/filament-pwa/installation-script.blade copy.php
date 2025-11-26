{{-- PWA Installation Script --}}
<script>
// PWA Installation Manager
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isStandalone = false;
        this.banner = null;
        this.config = @json($config);
        
        this.init();
    }

    init() {
        // 1. INJECT CUSTOM POPUP STYLES
        this.injectStyles();

        // Check if app is already installed
        this.checkInstallationStatus();
        
        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA] beforeinstallprompt event fired');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallBanner();
        });

        // Listen for appinstalled event
        window.addEventListener('appinstalled', (e) => {
            console.log('[PWA] App was installed');
            this.isInstalled = true;
            this.hideInstallBanner();
            this.trackInstallation();
        });

        // Register service worker
        this.registerServiceWorker();

        // Create install banner
        this.createInstallBanner();

        // In debug mode, show banner immediately if enabled
        const isDebugMode = {{ config('app.debug') ? 'true' : 'false' }};
        const showBannerInDebug = this.config.installation?.show_banner_in_debug ?? true;
        if (isDebugMode && showBannerInDebug && this.banner) {
            console.log('[PWA] Debug mode: Showing banner immediately');
            this.showInstallBanner();
        }

        // Handle iOS installation
        this.handleIOSInstallation();
    }

    // NEW: Function to style the popup
    injectStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* Modal Background (Overlay) */
            .pwa-install-banner {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background-color: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            /* Visible State */
            .pwa-install-banner.show {
                opacity: 1;
                pointer-events: auto;
            }

            /* The Card Itself */
            .pwa-install-content {
                background: white;
                color: #1f2937;
                padding: 1.5rem;
                border-radius: 1rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                max-width: 24rem;
                width: 90%;
                transform: scale(0.95);
                transition: transform 0.3s ease;
                text-align: center;
            }

            .pwa-install-banner.show .pwa-install-content {
                transform: scale(1);
            }

            .pwa-install-title {
                font-size: 1.25rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                color: #111827;
            }

            .pwa-install-description {
                font-size: 0.95rem;
                color: #6b7280;
                margin-bottom: 1.5rem;
                line-height: 1.5;
            }

            .pwa-install-actions {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .pwa-install-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s;
                border: 1px solid transparent;
            }

            /* Primary Button (Install) */
            .pwa-install-btn.primary {
                background-color: {{ $config['theme_color'] ?? '#6fbf44' }};
                color: white !important;
            }
            
            /* FIX: Explicitly force white text on hover */
            .pwa-install-btn.primary:hover {
                filter: brightness(90%);
                color: white !important; 
            }

            /* Secondary Button (Dismiss) */
            .pwa-install-btn:not(.primary) {
                background-color: transparent;
                color: #6b7280;
                border-color: #e5e7eb;
            }
            .pwa-install-btn:not(.primary):hover {
                background-color: #f9fafb;
                color: #374151;
            }

            .pwa-install-icon {
                width: 1.25rem;
                height: 1.25rem;
                margin-right: 0.5rem;
            }
        `;
        document.head.appendChild(style);
    }

    checkInstallationStatus() {
        this.isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                          window.navigator.standalone === true;

        this.isInstalled = this.isStandalone || 
                          localStorage.getItem('pwa-installed') === 'true';

        console.log('[PWA] Installation status:', {
            isStandalone: this.isStandalone,
            isInstalled: this.isInstalled
        });
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('{{ route("filament-pwa.service-worker") }}', {
                    scope: this.config.scope || '/'
                });
                
                console.log('[PWA] Service Worker registered successfully:', registration);

                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateAvailable();
                        }
                    });
                });

            } catch (error) {
                console.error('[PWA] Service Worker registration failed:', error);
            }
        }
    }

    createInstallBanner() {
        const isDebugMode = {{ config('app.debug') ? 'true' : 'false' }};
        const showBannerInDebug = this.config.installation?.show_banner_in_debug ?? true;

        if (isDebugMode && showBannerInDebug) {
            console.log('[PWA] Debug mode: Ready to show banner');
        } else {
            if (this.isInstalled || !this.config.installation_prompts?.enabled) return;
        }

        const banner = document.createElement('div');
        banner.className = 'pwa-install-banner'; // Uses the styles injected in init()
        banner.innerHTML = `
            <div class="pwa-install-content">
                <div class="pwa-install-text">
                    <div class="pwa-install-title">Installer l'application</div>
                    <div class="pwa-install-description">
                        Installez <strong>{{ config('app.name') }}</strong> sur votre appareil pour un accès plus rapide et une utilisation hors ligne.
                    </div>
                </div>
                <div class="pwa-install-actions">
                    <button class="pwa-install-btn primary" id="pwa-install-btn">
                        <svg class="pwa-install-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Installer maintenant
                    </button>
                    <button class="pwa-install-btn" id="pwa-dismiss-btn">
                        Plus tard
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(banner);
        this.banner = banner;

        document.getElementById('pwa-install-btn').addEventListener('click', () => {
            this.installApp();
        });

        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            this.dismissInstallBanner();
        });
    }

    showInstallBanner() {
        if (!this.banner) return;

        const isDebugMode = {{ config('app.debug') ? 'true' : 'false' }};
        const showBannerInDebug = this.config.installation?.show_banner_in_debug ?? true;

        if (isDebugMode && showBannerInDebug) {
            console.log('[PWA] Debug mode: Showing banner now');
            setTimeout(() => {
                this.banner.classList.add('show');
            }, 500);
            return;
        }

        if (this.isInstalled) return;

        const dismissed = localStorage.getItem('pwa-banner-dismissed');
        if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) {
            return;
        }

        const delay = this.config.installation_prompts?.delay || 2000;
        setTimeout(() => {
            this.banner.classList.add('show');
        }, delay);
    }

    hideInstallBanner() {
        if (this.banner) {
            this.banner.classList.remove('show');
        }
    }

    dismissInstallBanner() {
        this.hideInstallBanner();
        localStorage.setItem('pwa-banner-dismissed', Date.now().toString());
    }

    async installApp() {
        if (!this.deferredPrompt) {
            console.log('[PWA] No deferred prompt available (Check if https or already installed)');
            // Fallback for debug mode or if prompt is missing
            return;
        }

        try {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log('[PWA] User choice:', outcome);
            
            if (outcome === 'accepted') {
                this.hideInstallBanner();
            } else {
                this.dismissInstallBanner();
            }
            this.deferredPrompt = null;
        } catch (error) {
            console.error('[PWA] Error during installation:', error);
        }
    }

    handleIOSInstallation() {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isInStandaloneMode = window.navigator.standalone;

        if (isIOS && !isInStandaloneMode && !this.isInstalled) {
            const delay = this.config.installation_prompts?.ios_instructions_delay || 5000;
            setTimeout(() => {
                this.showIOSInstallInstructions();
            }, delay);
        }
    }

    showIOSInstallInstructions() {
        // ... (Keep existing IOS logic or customize if needed) ...
    }

    showUpdateAvailable() {
        // ... (Keep existing update logic) ...
    }

    trackInstallation() {
        localStorage.setItem('pwa-installed', 'true');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { new PWAInstaller(); });
} else {
    new PWAInstaller();
}
</script>