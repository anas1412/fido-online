<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- SECTION 1: IDENTITY ---
                Section::make('Identité de l\'entreprise')
                    ->schema([
                        Grid::make(3)->schema([
                            // Logo Column
                            FileUpload::make('logo_path')
                                ->label('Logo')
                                ->avatar()
                                ->imageEditor()
                                ->directory('tenant-logos')
                                ->columnSpan(1),

                            // Name & Slug
                            Grid::make(1)->schema([
                                TextInput::make('name')
                                    ->label('Nom de l\'entreprise')
                                    ->required(),
                                
                                TextInput::make('slug')
                                    ->label('Identifiant (URL)')
                                    ->disabled() // Usually auto-generated
                                    ->dehydrated(), 
                            ])->columnSpan(2),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('type')
                                ->label('Secteur d\'activité')
                                ->options([
                                    'commercial' => 'Commercial (Factures)',
                                    'accounting' => 'Comptabilité (Honoraires)',
                                    'medical'    => 'Santé (Honoraires 7%)',
                                ])
                                ->required(),

                            Select::make('currency')
                                ->label('Devise')
                                ->options(['TND' => 'TND', 'EUR' => 'EUR', 'USD' => 'USD'])
                                ->default('TND')
                                ->required(),
                        ]),
                    ]),

                // --- SECTION 2: LEGAL & CONTACT (Crucial for Invoices) ---
                Section::make('Informations Légales & Contact')
                    ->description('Ces informations apparaîtront sur vos factures.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('matricule_fiscal')
                                ->label('Matricule Fiscal')
                                ->placeholder('Ex: 1234567/A/M/000')
                                ->required(), // Mandatory in Tunisia for invoices

                            TextInput::make('registre_commerce')
                                ->label('Registre de Commerce (RC)'),

                            TextInput::make('email')
                                ->label('Email de contact')
                                ->email(),

                            TextInput::make('phone')
                                ->label('Téléphone')
                                ->tel(),
                                
                            TextInput::make('website')
                                ->label('Site Web')
                                ->prefix('https://')
                                ->columnSpanFull(),
                        ]),
                    ]),

                // --- SECTION 3: ADDRESS ---
                Section::make('Adresse de Facturation')
                    ->schema([
                        Textarea::make('address')
                            ->label('Adresse Rue')
                            ->rows(2)
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('city')->label('Ville'),
                            TextInput::make('zip_code')->label('Code Postal'),
                        ]),
                    ])->collapsible(),

                // --- SECTION 4: BANKING (For getting paid) ---
                Section::make('Coordonnées Bancaires')
                    ->description('Pour permettre à vos clients de vous payer par virement.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('bank_name')
                                ->label('Nom de la Banque')
                                ->placeholder('Ex: BIAT, Amen Bank...'),

                            TextInput::make('rib')
                                ->label('R.I.B (20 chiffres)')
                                ->placeholder('Ex: 08 123 4567890001234 55')
                                ->length(20)
                                ->numeric(),
                        ]),
                    ])->collapsible(),
            ]);
    }
}