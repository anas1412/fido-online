<?php

namespace App\Filament\Dashboard\Resources\TenantInvites\Pages;

use App\Filament\Dashboard\Resources\TenantInvites\TenantInviteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantInvite extends EditRecord
{
    protected static string $resource = TenantInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
