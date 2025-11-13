<?php

namespace App\Filament\Dashboard\Resources\Clients\Pages;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Créer un client';
}
