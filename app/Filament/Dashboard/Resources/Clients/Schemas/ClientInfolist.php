<?php

namespace App\Filament\Dashboard\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('name')
                        ->label('Nom du Client')
                        ->columnSpan(1),
                    TextEntry::make('contact_person')
                        ->label('Personne de contact')
                        ->placeholder('-')
                        ->columnSpan(1),
                    TextEntry::make('email')
                        ->label('Adresse e-mail')
                        ->placeholder('-')
                        ->columnSpan(1),
                    TextEntry::make('phone')
                        ->label('Téléphone')
                        ->placeholder('-')
                        ->columnSpan(1),
                    TextEntry::make('address')
                        ->label('Adresse')
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->columnSpan(1),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Notes')
                ->schema([
                    TextEntry::make('notes')
                        ->label('Notes Internes')
                        ->placeholder('-')
                        ->markdown()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Suivi et Métadonnées')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime()
                        ->placeholder('-')
                        ->columnSpan(1),
                    TextEntry::make('updated_at')
                        ->label('Mis à jour le')
                        ->dateTime()
                        ->placeholder('-')
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
