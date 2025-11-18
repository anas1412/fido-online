<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Schemas;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use App\Models\Honoraire;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class HonoraireInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('client.name')
                        ->label('Client')
                        ->columnSpan(1)
                        ->url(fn (Honoraire $record): string => ClientResource::getUrl('view', ['record' => $record->client->id])),
                    TextEntry::make('honoraire_number')
                        ->label('Numéro d\'honoraire')
                        ->columnSpan(1),
                    TextEntry::make('issue_date')
                        ->label('Date d\'émission')
                        ->date()
                        ->columnSpan(1),
                    TextEntry::make('object')
                        ->label('Objet')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->compact(),

            Section::make('Montants')
                ->schema([
                    TextEntry::make('amount_ht')
                        ->label('Montant HT')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                    TextEntry::make('amount_ttc')
                        ->label('Montant TTC')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                    TextEntry::make('tva_rate')
                        ->label('Taux TVA')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                    TextEntry::make('rs_rate')
                        ->label('Taux RS')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                    TextEntry::make('tf_rate')
                        ->label('Taux TF')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                    TextEntry::make('total_amount')
                        ->label('Montant Total')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('-'),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->compact(),

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
                ->columnSpanFull()
                ->compact(),
        ]);
    }
}
