<?php

namespace App\Filament\Dashboard\Resources\Invoices\RelationManagers;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OtherClientInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'client'; 
    protected static ?string $title = 'Autres Factures du Client';

    // This allows us to use the InvoiceResource for the table structure if needed,
    // but usually for "Sibling" records, we define the table explicitly here.
    
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'sent' => 'info',
                        'overdue' => 'danger',
                        'draft' => 'gray',
                        default => 'gray',
                    }),
                
                // CORRECTED: Use net_to_pay and dynamic currency
                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money(fn ($record) => $record->currency) 
                    ->weight('bold')
                    ->sortable(),
            ])
            ->headerActions([
                //CreateAction::make(), 
            ])
            ->actions([
                // CRITICAL: Allow user to click to see the other invoice
                ViewAction::make()
                    ->url(fn ($record) => InvoiceResource::getUrl('view', ['record' => $record])),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        // Get the invoice currently being viewed
        $currentInvoice = $this->getOwnerRecord();

        // Safety check
        if (!$currentInvoice || !$currentInvoice->client_id) {
            return \App\Models\Invoice::query()->whereRaw('1 = 0');
        }

        // Logic: Find all invoices for this client, EXCEPT the one we are currently looking at
        return \App\Models\Invoice::query()
            ->where('client_id', $currentInvoice->client_id)
            ->where('id', '!=', $currentInvoice->id)
            ->latest('issue_date');
    }
}