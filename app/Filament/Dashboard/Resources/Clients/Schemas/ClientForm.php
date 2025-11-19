<?php

namespace App\Filament\Dashboard\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Nom')
                    ->hint("Il peut s'agir d'une entreprise ou d'une personne physique")
                    ->required(),
                TextInput::make('contact_person')
                    ->label('Personne à contacter'),
                TextInput::make('email')
                    ->email()
                    ->label('Adresse e-mail'),
                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel(),
                Textarea::make('address')
                    ->label('Adresse')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}
