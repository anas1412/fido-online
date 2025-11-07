<?php

namespace App\Filament\Dashboard\Resources\Clients\Pages;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'Clients';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
