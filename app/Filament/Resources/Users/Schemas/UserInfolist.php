<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nom'),
                TextEntry::make('email')
                    ->label('Adresse e-mail'), 
                TextEntry::make('google_id')
                    ->label('ID Google')
                    ->placeholder('-'),
                IconEntry::make('is_admin')
                    ->label('Est administrateur') // Translated
                    ->boolean(),
                TextEntry::make('owned_tenants')
                    ->label('Organisations possédées')
                    ->formatStateUsing(function (string $state, \App\Models\User $record) {
                        if ($record->tenants->isEmpty()) {
                            return 'Aucune';
                        }
                        $ownedTenants = $record->tenants->filter(fn ($tenant) => $tenant->pivot->is_owner);
                        return $ownedTenants->pluck('name')->join(', ');
                    }),
                TextEntry::make('member_tenants')
                    ->label('Membre des organisations')
                    ->formatStateUsing(function (string $state, \App\Models\User $record) {
                        if ($record->tenants->isEmpty()) {
                            return 'Aucune';
                        }
                        return $record->tenants->pluck('name')->join(', ');
                    }),
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
