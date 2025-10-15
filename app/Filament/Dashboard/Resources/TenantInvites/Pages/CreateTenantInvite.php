<?php

namespace App\Filament\Dashboard\Resources\TenantInvites\Pages;

use App\Filament\Dashboard\Resources\TenantInvites\TenantInviteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantInvite extends CreateRecord
{
    protected static string $resource = TenantInviteResource::class;
}
