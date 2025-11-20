<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Schemas;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use App\Models\Honoraire;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class HonoraireInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // SECTION 1: HEADER INFO (Full Width)
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('honoraire_number')
                        ->label('Numéro')
                        ->size('large')
                        ->weight(FontWeight::Bold)
                        ->copyable(),

                    TextEntry::make('client.name')
                        ->label('Client')
                        ->url(fn (Honoraire $record): string => ClientResource::getUrl('view', ['record' => $record->client->id]))
                        ->color('primary')
                        ->weight(FontWeight::SemiBold),

                    TextEntry::make('issue_date')
                        ->label('Date d\'émission')
                        ->date('d M Y'),
                ])
                ->columns(3), // Adjusted columns since Honoraires have fewer fields than Invoices

            // SECTION 2: METADATA (Matching your Invoice layout order)
            Section::make('Métadonnées')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime('d/m/Y H:i')
                        ->color('gray'),

                    TextEntry::make('updated_at')
                        ->label('Mis à jour le')
                        ->dateTime('d/m/Y H:i')
                        ->color('gray'),
                ])
                ->columns(2),
            
            // SECTION 3: OBJECT (Equivalent to Articles)
            Section::make('Objet de la Note')
                ->schema([
                    TextEntry::make('object')
                        ->label('Description')
                        ->prose() // Keeps formatting nice
                        ->columnSpanFull(),
                ]),

            
            // SECTION 4: TOTALS (Right Aligned)
            Section::make('Calcul du Total')
                ->schema([
                    Grid::make(12)->schema([
                        // Spacer (Left side empty)
                        Group::make([])->columnSpan(['default' => 0, 'md' => 6]),

                        // Totals (Right side)
                        Group::make([
                            TextEntry::make('amount_ht')
                                ->label('Total Hors Taxe')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->alignEnd(),

                            TextEntry::make('tva_amount')
                                ->label('TVA')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->prefix(fn (Honoraire $record) => "({$record->tva_rate}%) ")
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),
                            
                            TextEntry::make('tf_value')
                                ->label('Timbre Fiscal')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('amount_ttc')
                                ->label('Montant TTC')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('rs_amount')
                                ->label('Retenue à la Source')
                                ->badge()
                                ->color('danger')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->alignEnd()
                                ->visible(fn (Honoraire $record) => $record->rs_amount > 0)
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('net_to_pay')
                                ->label('NET À PAYER')
                                ->money(fn ($record) => $record->currency ?? 'TND')
                                ->size('large')
                                ->weight(FontWeight::Bold)
                                ->color('success')
                                ->alignEnd()
                                ->extraAttributes(['class' => 'pt-4 border-t border-gray-200 dark:border-gray-700 whitespace-nowrap']),
                        ])->columnSpan(['default' => 12, 'md' => 6]),
                    ]),
                ]),

            
        ]);
    }
}