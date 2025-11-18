<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Pages;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewHonoraire extends ViewRecord
{
    protected static string $resource = HonoraireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printHonoraire')
                ->label('Imprimer (WIP)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('info')
                ->action(fn () => $this->js('window.print()')),
            EditAction::make(),
        ];
    }
}
