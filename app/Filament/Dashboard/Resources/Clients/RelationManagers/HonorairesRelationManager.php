<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
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
            ->recordTitleAttribute('honoraire_number')
            ->columns([               
                TextColumn::make('honoraire_number')
                    ->label('Numéro')
                    ->searchable(),

                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('amount_ht')
                    ->label('HT')
                    ->money(fn ($record) => $record->currency ?? 'TND')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tf_value') 
                    ->label('Timbre')
                    ->money(fn ($record) => $record->currency ?? 'TND')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money(fn ($record) => $record->currency ?? 'TND') 
                    ->weight('bold')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                // FIX: Redirect to Full Page Create with Client ID in URL
                CreateAction::make()
                    ->url(fn () => HonoraireResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}