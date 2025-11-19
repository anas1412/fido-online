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
            // General Information
            Section::make('Informations Générales')
                ->columns(3) // 3-column layout
                ->components([
                    TextEntry::make('name')
                        ->label('Nom du Client'),
                    TextEntry::make('address')
                        ->label('Adresse')
                        ->placeholder('-'),
                    TextEntry::make('contact_person')
                        ->label('Personne de contact')
                        ->placeholder('-'),
                    TextEntry::make('email')
                        ->label('Adresse e-mail')
                        ->placeholder('-'),
                    TextEntry::make('phone')
                        ->label('Téléphone')
                        ->placeholder('-'),

                    
                ])
                ->columnSpan('full')
                ->compact(),

            // Notes
            Section::make('Notes')
                ->columns(1)
                ->components([
                    TextEntry::make('notes')
                        ->label('Notes Internes')
                        ->placeholder('-')
                        ->markdown()
                        ->columnSpan('full'),
                ])
                ->columnSpan('full')
                ->compact(),

            // Tracking & Metadata
            Section::make('Suivi et Métadonnées')
                ->columns(2)
                ->components([
                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->label('Mis à jour le')
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columnSpan('full')
                ->compact(),
        ]);
    }
}
