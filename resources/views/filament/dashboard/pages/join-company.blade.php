<x-filament-panels::page>
    <div class="fi-page-content-ctn grid gap-y-8 py-8">
        <x-filament::section>
            <x-slot name="heading">
                Rejoindre l'entreprise: {{ $this->tenant->name }}
            </x-slot>

            <div class="flex flex-col gap-y-4">
                <p class="text-gray-600 dark:text-gray-400">
                    Vous avez été invité à rejoindre l'entreprise <span class="font-bold">{{ $this->tenant->name }}</span>.
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    Type d'entreprise: <span class="font-bold">{{ ucfirst($this->tenant->type) }}</span>
                </p>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    {{ $this->cancelAction }}
                    {{ $this->joinAction }}
                </div>
            </x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>