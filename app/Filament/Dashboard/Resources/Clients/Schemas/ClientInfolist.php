<?php

namespace App\Filament\Dashboard\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // --- MAIN CARD: Everything important in one place ---
            Section::make('Fiche Client')
                ->schema([
                    // ROW 1: Identity (Takes full width)
                    Grid::make(4)->schema([
                        TextEntry::make('name')
                            ->label('Nom / Raison Sociale')
                            ->weight(FontWeight::Bold)
                            ->size('large') // String fixed
                            ->columnSpan(2), // Name takes more space

                        TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'company' => 'Société (B2B)',
                                'individual' => 'Particulier (B2C)',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'company' => 'info',
                                'individual' => 'success',
                                default => 'gray',
                            }),

                        TextEntry::make('matricule_fiscal')
                            ->label('Matricule Fiscal')
                            ->icon('heroicon-m-identification')
                            ->copyable()
                            ->color('gray')
                            ->visible(fn ($record) => $record->type === 'company')
                            ->placeholder('Non applicable'),
                    ]),

                    // ROW 2: Contact Details (3 Columns)
                    Grid::make(3)->schema([
                        TextEntry::make('contact_person')
                            ->label('Interlocuteur')
                            ->icon('heroicon-m-user')
                            ->placeholder('-'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->url(fn ($record) => "mailto:{$record->email}")
                            ->color('primary')
                            ->placeholder('-'),

                        TextEntry::make('phone')
                            ->label('Téléphone')
                            ->icon('heroicon-m-phone')
                            ->url(fn ($record) => "tel:{$record->phone}")
                            ->color('primary')
                            ->placeholder('-'),
                    ])->extraAttributes(['class' => 'mt-4 pt-4 border-t border-gray-100 dark:border-gray-800']), // Visual separator

                    // ROW 3: Location (3 Columns)
                    Grid::make(3)->schema([
                        TextEntry::make('address')
                            ->label('Adresse')
                            ->icon('heroicon-m-map-pin')
                            ->placeholder('-'),

                        TextEntry::make('city')
                            ->label('Ville')
                            ->placeholder('-'),

                        TextEntry::make('zip_code')
                            ->label('Code Postal')
                            ->placeholder('-'),
                    ]),
                ]),

            // --- SECTION 2: NOTES & META ---
            Section::make('Notes & Suivi')
                ->schema([
                    Grid::make(3)->schema([
                        // Notes takes 2/3 width
                        TextEntry::make('notes')
                            ->label('Notes Internes')
                            ->markdown()
                            ->placeholder('Aucune note enregistrée.')
                            ->columnSpan(2),

                        // Meta takes 1/3 width
                        Grid::make(1)->schema([
                            TextEntry::make('created_at')
                                ->label('Créé le')
                                ->dateTime('d M Y')
                                ->color('gray'),
                            
                            TextEntry::make('updated_at')
                                ->label('Mise à jour')
                                ->dateTime('d M Y')
                                ->color('gray'),
                        ])->columnSpan(1),
                    ]),
                ])->collapsible(),
        ]);
    }
}