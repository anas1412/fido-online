<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
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
                    ->money('usd') 
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
