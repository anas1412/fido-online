<?php

namespace App\Filament\Dashboard\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Type Badge (B2B vs B2C)
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'company' => 'Société',
                        'individual' => 'Particulier',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'company' => 'info',      // Blue for companies
                        'individual' => 'success', // Green for individuals
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nom / Raison Sociale')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('matricule_fiscal')
                    ->label('M. Fiscal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default to keep table clean

                TextColumn::make('contact_person')
                    ->label('Contact')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),

                TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'company' => 'Société',
                        'individual' => 'Particulier',
                    ]),
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