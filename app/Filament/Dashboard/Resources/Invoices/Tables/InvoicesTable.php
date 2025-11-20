<?php

namespace App\Filament\Dashboard\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use App\Filament\Dashboard\Filters\FiscalYearFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn ($record) => !$record->trashed()
                    ? InvoiceResource::getUrl('view', ['record' => $record])
                    : null
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                
                // Status Badge
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'sent' => 'info',
                        'overdue' => 'danger',
                        'draft' => 'gray',
                        default => 'gray',
                    }),

                // Amounts
                TextColumn::make('amount_ht')
                    ->label('HT')
                    ->money('TND')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money('TND')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                FiscalYearFilter::make('fiscal_year'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->visible(fn ($record) => !$record->trashed()),
                EditAction::make()->visible(fn ($record) => !$record->trashed()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}