<?php

namespace App\Filament\Dashboard\Resources\Invoices\Pages;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printInvoice')
                ->label('Imprimer')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('info')
                ->action(fn () => $this->js('window.print()')),
            EditAction::make(),
        ];
    }
}
