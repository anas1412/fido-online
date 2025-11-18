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
                    TextEntry::make('name')->label('Nom')->columnSpan('full'),
                    TextEntry::make('email')->label('Adresse e-mail')->columnSpan('full'),
                    TextEntry::make('google_id')->label('ID Google')->placeholder('-'),
                    IconEntry::make('is_admin')->label('Est administrateur')->boolean(),
                ])
                ->columnSpan('full')
                ->compact(),

            /* Section::make('Organisations')
                ->columns(2)
                ->components([
                    TextEntry::make('owned_tenants')
                        ->label('Organisations possédées')
                        ->formatStateUsing(fn (string $state, $record) => 
                            $record->tenants->filter(fn ($t) => $t->pivot->is_owner)->pluck('name')->join(', ') ?: 'Aucune'
                        ),
                    TextEntry::make('member_tenants')
                        ->label('Membre des organisations')
                        ->formatStateUsing(fn (string $state, $record) =>
                            $record->tenants->pluck('name')->join(', ') ?: 'Aucune'
                        ),
                ])
                ->columnSpan('full')
                ->compact(), */

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
