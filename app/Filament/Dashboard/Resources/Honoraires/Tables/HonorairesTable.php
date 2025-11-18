<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
            ->recordUrl(
                fn ($record) => !$record->trashed()
                    ? HonoraireResource::getUrl('view', ['record' => $record])
                    : null
            )
            ->columns([               
                TextColumn::make('honoraire_number')
                    ->label('Numéro d\'honoraire')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),
                TextColumn::make('amount_ht')
                    ->label('Montant HT')
                    ->numeric()
                    ->money(fn ($record) => $record->currency)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_ttc')
                    ->label('Montant TTC')
                    ->numeric()
                    ->money(fn ($record) => $record->currency)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tva_rate')
                    ->label('Taux TVA')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rs_rate')
                    ->label('Taux RS')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tf_rate')
                    ->label('Taux TF')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->numeric()
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                TextColumn::make('issue_date')
                    ->label('Date d\'émission')
                    ->date()
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
