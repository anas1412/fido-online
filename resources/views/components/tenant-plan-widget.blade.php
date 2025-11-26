@php
    $tenant = filament()->getTenant();
    $isPro = $tenant?->isPro() ?? false;
@endphp

@if($tenant)
    <div 
        x-data 
        x-show="$store.sidebar.isOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        class="px-2 pb-2 pt-4"
    >
        <div @class([
            'relative overflow-hidden rounded-lg p-4 shadow-sm ring-1 transition-all',
            'bg-white ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => !$isPro,
            'bg-gradient-to-br from-amber-50 to-white ring-amber-200 dark:from-amber-950/30 dark:to-gray-900 dark:ring-amber-500/20' => $isPro,
        ])>
            
            @if($isPro)
                <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-amber-500/10 blur-2xl pointer-events-none"></div>
            @endif

            <div class="relative z-10 flex flex-col gap-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Votre offre
                    </span>
                    
                    @if($isPro)
                        <div class="inline-flex items-center gap-1 rounded-md px-3 py-1 text-[10px] font-bold
                            bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-300
                            dark:bg-amber-700 dark:text-white dark:ring-amber-600">
                            <span>PRO</span>
                            <x-heroicon-s-star class="h-3 w-3" />
                        </div>
                    @else
                        <!-- FIX: Better Padding & Colors -->
                        <div class="inline-flex items-center rounded-md px-3 py-1 text-[10px] font-bold
                            bg-gray-100 text-gray-900 ring-1 ring-inset ring-gray-300
                            dark:bg-gray-800 dark:text-white dark:ring-gray-700">
                            GRATUIT
                        </div>


                    @endif
                </div>

                {{-- Tenant Name --}}
                <div>
                    <div @class([
                        'text-sm font-semibold truncate',
                        'text-gray-950 dark:text-white' => !$isPro, 
                        'text-amber-700 dark:text-amber-400' => $isPro,
                    ])>
                        {{ $tenant->name }}
                    </div>
                    
                    <div class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $isPro ? 'Accès illimité' : 'Fonctionnalités limitées' }}
                    </div>
                </div>

                {{-- Upgrade Button --}}
                @if(!$isPro)
                    <button 
                        type="button"
                        onclick="window.location.href='/dashboard/billing'" 
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors"
                    >
                        <x-heroicon-m-sparkles class="h-4 w-4" />
                        Passer en PRO
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif