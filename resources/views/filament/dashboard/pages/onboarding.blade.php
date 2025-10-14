<x-filament-panels::page.simple>
    <x-filament::section heading="Create New Tenant">
        {{ $this->createTenantForm(new \Filament\Forms\Form())->render() }}

        <div class="flex gap-3 mt-6">
            {{ $this->getRegisterTenantAction() }}
        </div>
    </x-filament::section>

    <x-filament::section heading="Join Existing Tenant" class="mt-8">
        {{ $this->joinTenantForm(new \Filament\Forms\Form())->render() }}

        <div class="flex gap-3 mt-6">
            {{ $this->getJoinTenantAction() }}
        </div>
    </x-filament::section>
</x-filament-panels::page.simple>