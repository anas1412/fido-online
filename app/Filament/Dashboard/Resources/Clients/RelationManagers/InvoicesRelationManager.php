<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $relatedResource = InvoiceResource::class;

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

                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money(fn ($record) => $record->currency) 
                    ->weight('bold')
                    ->sortable(),
            ])
            ->headerActions([
                // FIX: Redirect to Full Page Create with Client ID in URL
                CreateAction::make()
                    ->url(fn () => InvoiceResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}