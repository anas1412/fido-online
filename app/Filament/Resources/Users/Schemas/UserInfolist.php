<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations générales')
                ->columns(2)
                ->components([
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('google_id')->label('ID Google')->placeholder('-'),
                    TextEntry::make('email')->label('Adresse e-mail'),
                    
                    IconEntry::make('is_admin')->label('Est administrateur')->boolean(),
                ])
                ->columnSpan('full')
                ->compact(),

            Section::make('Timestamps')
                ->columns(2)
                ->components([
                    TextEntry::make('created_at')->label('Créé le')->dateTime()->placeholder('-'),
                    TextEntry::make('updated_at')->label('Mis à jour le')->dateTime()->placeholder('-'),
                ])
                ->columnSpan('full')
                ->compact(),
        ]);
    }
}
