<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class HonorairesRelationManager extends RelationManager
{
    protected static string $relationship = 'honoraires';

    protected static ?string $relatedResource = HonoraireResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([               
                TextColumn::make('honoraire_number')
                    ->label('Numéro d\'honoraire')
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label('Date d\'émission')
                    ->date()
                    ->sortable(),
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
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
