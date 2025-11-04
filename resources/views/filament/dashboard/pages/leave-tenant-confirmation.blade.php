<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-4 text-center">
            <div class="text-base font-medium text-gray-800">
                Confirmer le départ de <strong>{{ filament()->getTenant()?->name }}</strong>
            </div>

            <div class="flex justify-center gap-3">
                {{ $this->confirmLeave }}

                <x-filament::button
                    color="gray"
                    tag="a"
                    href="{{ filament()->getUrl() }}"
                >
                    Annuler
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
