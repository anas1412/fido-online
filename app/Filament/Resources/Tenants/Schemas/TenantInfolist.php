<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Header
            Section::make('Aperçu')
                ->schema([
                    Grid::make(4)->schema([
                        ImageEntry::make('logo_path')
                            ->hiddenLabel()
                            ->circular()
                            ->columnSpan(1),
                        
                        Group::make([
                            TextEntry::make('name')->weight('bold')->size('large'),
                            TextEntry::make('slug')->color('gray')->prefix('@'),
                            TextEntry::make('type')->badge(),
                        ])->columnSpan(3),
                    ]),
                ]),

            // Legal & Bank
            Section::make('Détails Administratifs')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('matricule_fiscal')->label('Matricule Fiscal'),
                        TextEntry::make('registre_commerce')->label('RC'),
                        TextEntry::make('rib')->label('RIB Bancaire')->copyable(),
                    ]),
                    
                    Grid::make(3)->schema([
                        TextEntry::make('email')->icon('heroicon-m-envelope'),
                        TextEntry::make('phone')->icon('heroicon-m-phone'),
                        TextEntry::make('address')->label('Adresse')->columnSpan(1),
                    ]),
                ]),
                
            // Stats
            Section::make('Statistiques')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('users_count')
                            ->label('Utilisateurs')
                            ->state(fn ($record) => $record->users()->count())
                            ->badge(),
                        TextEntry::make('created_at')->label('Inscrit le')->date(),
                    ]),
                ])->collapsed(),
        ]);
    }
}