<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn; // Added for boolean flags
use Filament\Tables\Table;
use App\Filament\Dashboard\Filters\FiscalYearFilter;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;

class HonorairesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => !$record->trashed() ? HonoraireResource::getUrl('view', ['record' => $record]) : null)
            ->columns([               
                TextColumn::make('honoraire_number')->label('Numéro')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Client')->searchable(),
                TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                
                // Status Flags
                IconColumn::make('exonere_tva')->label('Exo TVA')->boolean()->toggleable(isToggledHiddenByDefault: true),

                // Financials
                TextColumn::make('amount_ht')->label('HT')->money('TND')->sortable(),
                TextColumn::make('amount_ttc')->label('TTC')->money('TND')->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money('TND')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('created_at')->label('Créé le')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                FiscalYearFilter::make('fiscal_year'),
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