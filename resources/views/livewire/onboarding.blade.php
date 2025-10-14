<div class="fi-simple-layout bg-gray-50 dark:bg-gray-950">
    <div class="fi-simple-main-ctn flex flex-col items-center min-h-screen justify-center">
        <div class="fi-simple-card w-full max-w-xl rounded-xl bg-white/50 p-6 ring-1 ring-gray-950/5 dark:bg-gray-900/50 dark:ring-white/10">
            {{-- Your title and welcome text --}}

            <div class="mt-8">
                {{-- This renders the form fields --}}
                {{ $this->form }}

                {{-- The actions are now rendered separately --}}
                <div class="flex items-center gap-x-3 mt-6">
                    {{ $this->createTenantAction }}
                    {{ $this->joinTenantAction }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- THIS IS ESSENTIAL FOR ACTIONS TO WORK --}}
    <x-filament-actions::modals />
</div>