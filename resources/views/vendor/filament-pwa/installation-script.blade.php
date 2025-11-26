{{-- PWA Installation Script --}}
<script>
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.banner = null;
        this.bannerTimeout = null;
        this.hasRefused = false; // Fix: Tracks if user cancelled
        this.config = @json($config);
        
        window.pwaInstaller = this;
        this.init();
    }

    init() {
        this.injectStyles();
        this.checkInstallationStatus();
        
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA] Ready to install');
            e.preventDefault();
            this.deferredPrompt = e;
            
            // 1. Enable Sidebar Button
            this.toggleSidebarButton(true);

            // 2. Schedule Auto-Popup
            this.scheduleInstallBanner();
        });

        window.addEventListener('appinstalled', (e) => {
            this.isInstalled = true;
            this.hideInstallBanner();
            this.toggleSidebarButton(false);
            this.trackInstallation();
        });

        this.registerServiceWorker();
        this.createInstallBanner();
        this.handleIOSInstallation();
    }

    // -------------------------------------------------------------------------
    // INSTALL LOGIC
    // -------------------------------------------------------------------------
    async installApp() {
        if (!this.deferredPrompt) return;

        // 1. Stop any pending auto-popups immediately
        this.hideInstallBanner();

        try {
            // 2. Show Native Prompt
            this.deferredPrompt.prompt();
            
            // 3. Wait for choice
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log('[PWA] User choice:', outcome);
            
            if (outcome === 'accepted') {
                this.toggleSidebarButton(false);
                this.trackInstallation();
            } else {
                console.log('[PWA] User cancelled');
                // 4. FIX: Mark that user refused, so we don't auto-show the popup again
                this.hasRefused = true;
            }
            
            this.deferredPrompt = null;
        } catch (error) {
            console.error('[PWA] Install error:', error);
        }
    }

    // -------------------------------------------------------------------------
    // POPUP SCHEDULING
    // -------------------------------------------------------------------------
    scheduleInstallBanner() {
        // If user already cancelled this session, do not show again
        if (this.hasRefused) return;

        if (this.bannerTimeout) clearTimeout(this.bannerTimeout);

        const isDebugMode = {{ config('app.debug') ? 'true' : 'false' }};
        const showBannerInDebug = this.config.installation?.show_banner_in_debug ?? true;

        if (isDebugMode && showBannerInDebug) {
            this.bannerTimeout = setTimeout(() => this.showInstallBanner(), 500);
            return;
        }

        if (this.isInstalled) return;

        const dismissed = localStorage.getItem('pwa-banner-dismissed');
        if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) return;

        const delay = this.config.installation_prompts?.delay || 3000;
        this.bannerTimeout = setTimeout(() => this.showInstallBanner(), delay);
    }

    showInstallBanner() {
        if (this.hasRefused) return; // Double check refusal
        if (this.banner) this.banner.classList.add('show');
    }

    hideInstallBanner() {
        if (this.bannerTimeout) {
            clearTimeout(this.bannerTimeout);
            this.bannerTimeout = null;
        }
        if (this.banner) this.banner.classList.remove('show');
    }

    dismissInstallBanner() {
        this.hideInstallBanner();
        localStorage.setItem('pwa-banner-dismissed', Date.now().toString());
    }

    // -------------------------------------------------------------------------
    // UI & STYLES
    // -------------------------------------------------------------------------
    injectStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .pwa-install-banner { position: fixed; inset: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
            .pwa-install-banner.show { opacity: 1; pointer-events: auto; }
            .pwa-install-content { background: white; color: #1f2937; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-width: 24rem; width: 90%; transform: scale(0.95); transition: transform 0.3s ease; text-align: center; }
            .pwa-install-banner.show .pwa-install-content { transform: scale(1); }
            .pwa-install-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #111827; }
            .pwa-install-description { font-size: 0.95rem; color: #6b7280; margin-bottom: 1.5rem; line-height: 1.5; }
            .pwa-install-actions { display: flex; flex-direction: column; gap: 0.75rem; }
            .pwa-install-btn { display: flex; align-items: center; justify-content: center; padding: 0.75rem 1rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; }
            .pwa-install-btn.primary { background-color: {{ $config['theme_color'] ?? '#6fbf44' }}; color: white !important; }
            .pwa-install-btn.primary:hover { filter: brightness(90%); color: white !important; }
            .pwa-install-btn:not(.primary) { background-color: transparent; color: #6b7280; border-color: #e5e7eb; }
            .pwa-install-btn:not(.primary):hover { background-color: #f9fafb; color: #374151; }
            .pwa-install-icon { width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; }
        `;
        document.head.appendChild(style);
    }

    createInstallBanner() {
        const banner = document.createElement('div');
        banner.className = 'pwa-install-banner';
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
                        <svg class="pwa-install-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        Installer maintenant
                    </button>
                    <button class="pwa-install-btn" id="pwa-dismiss-btn">Plus tard</button>
                </div>
            </div>`;
        document.body.appendChild(banner);
        this.banner = banner;
        document.getElementById('pwa-install-btn').addEventListener('click', () => this.installApp());
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => this.dismissInstallBanner());
    }

    toggleSidebarButton(show) {
        const btn = document.getElementById('pwa-sidebar-button');
        if (btn) btn.style.display = show ? 'flex' : 'none';
    }

    checkInstallationStatus() {
        this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        this.isInstalled = this.isStandalone || localStorage.getItem('pwa-installed') === 'true';
        if (this.isInstalled) this.toggleSidebarButton(false);
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try { await navigator.serviceWorker.register('{{ route("filament-pwa.service-worker") }}', { scope: '/' }); } 
            catch (e) { console.error('SW Error:', e); }
        }
    }

    handleIOSInstallation() {}
    trackInstallation() { localStorage.setItem('pwa-installed', 'true'); }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new PWAInstaller());
} else {
    new PWAInstaller();
}
</script>