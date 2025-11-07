<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations sur l\'organisation')
                    ->columns(5)
                    ->compact()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nom'),
                        TextEntry::make('slug')
                            ->label('Identifiant unique'),
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
                    ]),
            ]);
    }
}
