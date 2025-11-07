<?php

namespace App\Filament\Dashboard\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label('Numéro de Facture')
                    ->searchable(),
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
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
