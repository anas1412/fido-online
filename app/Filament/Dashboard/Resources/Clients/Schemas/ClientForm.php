<?php

namespace App\Filament\Dashboard\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get; // Import Get
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Client')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('type')
                            ->label('Type de Client')
                            ->options([
                                'company' => 'Société (B2B)',
                                'individual' => 'Particulier (B2C)',
                            ])
                            ->default('company')
                            ->live() // Make reactive
                            ->required(),

                        TextInput::make('name')
                            ->label(fn (Get $get) => $get('type') === 'company' ? 'Raison Sociale' : 'Nom Complet')
                            ->required()
                            ->maxLength(255),
                    ]),

                    // Only show Matricule Fiscal if it is a Company
                    TextInput::make('matricule_fiscal')
                        ->label('Matricule Fiscal')
                        ->visible(fn (Get $get) => $get('type') === 'company')
                        ->placeholder('Ex: 1234567/A/M/000'),

                    Grid::make(3)->schema([
                        TextInput::make('contact_person')
                            ->label('Interlocuteur')
                            ->prefixIcon('heroicon-m-user'),
                            
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope'),
                            
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->prefixIcon('heroicon-m-phone'),
                    ]),

                    Section::make('Adresse')
                        ->schema([
                            Textarea::make('address')
                                ->label('Adresse (Rue, Immeuble...)')
                                ->rows(2),
                            
                            Grid::make(2)->schema([
                                TextInput::make('city')->label('Ville'),
                                TextInput::make('zip_code')->label('Code Postal')->numeric(),
                            ]),
                        ])->compact(),

                    Textarea::make('notes')
                        ->label('Notes internes')
                        ->columnSpanFull(),
                ])
        ]);
    }
}