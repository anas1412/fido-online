<?php

namespace App\Filament\Dashboard\Resources\TenantInvites\Pages;

use App\Filament\Dashboard\Resources\TenantInvites\TenantInviteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantInvites extends ListRecords
{
    protected static string $resource = TenantInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
