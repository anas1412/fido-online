<?php

namespace App\Filament\Dashboard\Resources\Debits\Pages;

use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDebit extends ViewRecord
{
    protected static string $resource = DebitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
