<?php

namespace App\Filament\Dashboard\Resources\Debits\Pages;

use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDebits extends ListRecords
{
    protected static string $resource = DebitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
