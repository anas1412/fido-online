<?php

namespace App\Filament\Dashboard\Resources\Debits\Tables;

use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use App\Filament\Dashboard\Filters\FiscalYearFilter; // Assuming you have this
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;

class DebitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => !$record->trashed() ? DebitResource::getUrl('view', ['record' => $record]) : null)
            ->columns([
                TextColumn::make('debit_number')->label('Numéro')->searchable()->sortable()->weight('bold'),
                TextColumn::make('client.name')->label('Client')->searchable(),
                
                TextColumn::make('invoice.invoice_number')
                    ->label('Réf. Facture')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-')
                    ->visible(fn () => filament()->getTenant()->usesInvoices()), // Hide if accountant

                TextColumn::make('honoraire.honoraire_number')
                    ->label('Réf. Honoraire')
                    ->badge()
                    ->color('info') 
                    ->placeholder('-')
                    ->visible(fn () => filament()->getTenant()->usesHonoraires()), // Hide if commercial

                TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                
                // Status Flags
                IconColumn::make('exonere_tva')->label('Exo TVA')->boolean()->toggleable(isToggledHiddenByDefault: true),

                // Financials
                TextColumn::make('amount_ht')->label('HT')->money('TND')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_ttc')->label('TTC')->money('TND')->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money('TND')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')->label('Créé le')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Remove FiscalYearFilter if you don't have it generally available, or import it
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->visible(fn ($record) => !$record->trashed()),
                EditAction::make()->visible(fn ($record) => !$record->trashed()),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}