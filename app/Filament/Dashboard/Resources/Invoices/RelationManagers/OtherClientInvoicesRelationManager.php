<?php

namespace App\Filament\Dashboard\Resources\Invoices\RelationManagers;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OtherClientInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'client'; // This is a dummy, we'll override the query
    protected static ?string $model = \App\Models\Invoice::class;
    protected static ?string $relatedResource = InvoiceResource::class;
    protected static ?string $title = 'Autres Factures du Client';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Numéro de Facture')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('issue_date')
                    ->label('Date d\'Émission')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Date d\'Échéance')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('usd') // Assuming USD, adjust as needed
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    protected function getTableQuery(): Builder
    {
        // Get the parent invoice record
        $parentInvoice = $this->ownerRecord;

        // Ensure the parent invoice has a client
        if (!$parentInvoice || !$parentInvoice->client) {
            return \App\Models\Invoice::query()->whereRaw('1 = 0'); // Return an empty query
        }

        // Return other invoices belonging to the same client, excluding the current invoice
        return \App\Models\Invoice::query()
            ->where('client_id', $parentInvoice->client->id)
            ->where('id', '!=', $parentInvoice->id);
    }
}
