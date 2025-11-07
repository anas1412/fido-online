<?php

namespace App\Filament\Dashboard\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nom'),
                TextEntry::make('contact_person')
                    ->label('Personne de contact')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Adresse e-mail')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('Téléphone')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label('Adresse')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->label('Notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('Statut'),
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
