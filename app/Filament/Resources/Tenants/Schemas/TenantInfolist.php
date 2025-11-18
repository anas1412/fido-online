<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations sur l\'organisation')
                ->columns(2)
                ->compact()
                ->components([
                    TextEntry::make('name')
                        ->label('Nom'),
                        
                    TextEntry::make('slug')
                        ->label('Identifiant unique'),

                    // Added Member Count Badge
                    TextEntry::make('users_count')
                        ->label('Membres')
                        ->state(fn ($record) => $record->users()->count()) // Calculates count dynamically
                        ->badge()
                        ->color('info'),

                    TextEntry::make('type')
                        ->label('Type')
                        ->badge(),
                        
                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime()
                        ->placeholder('-'),
                        
                    TextEntry::make('updated_at')
                        ->label('Mis à jour le')
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columnSpan('full'),
        ]);
    }
}