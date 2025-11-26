<x-filament-widgets::widget class="fi-dashboard-project-info-widget h-full">
    <x-filament::section class="h-full flex flex-col justify-center">
        <!-- Main Layout Wrapper -->
        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-4">

            <!-- Left Side: Identity (Unchanged) -->
            <div class="flex items-center gap-3">
                <x-filament::icon
                    icon="heroicon-o-sparkles"
                    class="h-10 w-10 text-success-600 dark:text-success-400 shrink-0"
                />

                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                            Fido AI
                        </h2>
                        <x-filament::badge color="success" size="xs">
                            v{{ $this->version }}
                        </x-filament::badge>
                    </div>
                    
                    <a 
                        href="https://fido.tn" 
                        target="_blank" 
                        class="text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition flex items-center gap-1"
                    >
                        Accéder à l'accueil 
                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <!-- Right Side: Actions -->
            <div class="flex items-center gap-2">
                
                <!-- 
                    FIX: Added 'text-center h-auto' to both buttons.
                    'text-center': Ensures "IA" centers under "Assistance".
                    'h-auto': Ensures the button grows if text wraps to 2 lines.
                -->
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Dashboard\Pages\AIHelp::getUrl() }}"
                    color="primary"
                    size="sm"
                    icon="heroicon-m-chat-bubble-left-right"
                    class="text-center h-auto"
                >
                    Assistance IA
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    size="sm"
                    icon="heroicon-m-lifebuoy"
                    x-on:click="$dispatch('open-modal', { id: 'support-modal' })"
                    class="text-center h-auto"
                >
                    Centre de Support
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    <!-- Modal Code (Unchanged) -->
    <x-filament::modal id="support-modal" width="lg" alignment="center">
        <x-slot name="heading">Centre de Support</x-slot>
        <x-slot name="description">Comment pouvons-nous vous aider ?</x-slot>
        <div class="grid gap-4 md:grid-cols-2">
            <div 
                class="group relative flex flex-col items-start gap-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-4 transition hover:border-primary-500/50 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer"
                x-on:click="window.navigator.clipboard.writeText('anas.bassoumi@gmail.com'); $tooltip('Email copié !');"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                    <x-filament::icon icon="heroicon-m-envelope" class="h-5 w-5" />
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</span>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">anas.bassoumi@gmail.com</p>
                </div>
            </div>
            <div 
                class="group relative flex flex-col items-start gap-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-4 transition hover:border-success-500/50 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer"
                 x-on:click="window.navigator.clipboard.writeText('+21650377851'); $tooltip('Numéro copié !');"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                    <x-filament::icon icon="heroicon-m-phone" class="h-5 w-5" />
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">WhatsApp / GSM</span>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">+216 50 377 851</p>
                </div>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'support-modal' })">
                    Fermer
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament-widgets::widget>