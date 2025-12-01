<?php

namespace App\Filament\Dashboard\Resources\Debits\Schemas;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use App\Models\Debit;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use App\Filament\Dashboard\Resources\Invoices\InvoiceResource; 

class DebitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // SECTION 1: HEADER INFO
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('debit_number')
                        ->label('Numéro Note')
                        ->size('large')
                        ->weight(FontWeight::Bold)
                        ->copyable(),

                    TextEntry::make('client.name')
                        ->label('Client')
                        ->url(fn (Debit $record): string => ClientResource::getUrl('view', ['record' => $record->client->id]))
                        ->color('primary')
                        ->weight(FontWeight::SemiBold),

                    TextEntry::make('issue_date')
                        ->label('Date d\'émission')
                        ->date('d M Y'),

                    TextEntry::make('invoice.invoice_number')
                        ->label('Réf. Facture')
                        ->placeholder('Aucune')
                        ->visible(fn () => filament()->getTenant()->usesInvoices())
                        // Make it clickable
                        ->color('primary') 
                        ->weight(FontWeight::SemiBold)
                        ->url(fn (Debit $record) => $record->invoice_id 
                            ? InvoiceResource::getUrl('view', ['record' => $record->invoice_id]) 
                            : null
                        ),

                    // --- HONORAIRE REFERENCE (Accounting/Medical) ---
                    TextEntry::make('honoraire.honoraire_number')
                        ->label('Réf. Honoraire')
                        ->placeholder('Aucune')
                        ->visible(fn () => filament()->getTenant()->usesHonoraires())
                        // Make it clickable
                        ->color('primary') 
                        ->weight(FontWeight::SemiBold)
                        ->url(fn (Debit $record) => $record->honoraire_id 
                            ? HonoraireResource::getUrl('view', ['record' => $record->honoraire_id]) 
                            : null
                        ),
                ])
                ->columns(4),

            // SECTION 2: METADATA
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
            
            // SECTION 3: OBJECT
            Section::make('Objet de la Note')
                ->schema([
                    TextEntry::make('object')
                        ->label('Description')
                        ->prose()
                        ->columnSpanFull(),
                ]),

            // SECTION 4: TOTALS
            Section::make('Calcul du Total')
                ->schema([
                    Grid::make(12)->schema([
                        // Spacer
                        Group::make([])->columnSpan(['default' => 0, 'md' => 6]),

                        // Totals
                        Group::make([
                            TextEntry::make('amount_ht')
                                ->label('Total Hors Taxe')
                                ->money('TND')
                                ->alignEnd(),

                            TextEntry::make('debours_amount')
                                ->label('Débours (Non taxé)')
                                ->money('TND')
                                ->alignEnd()
                                ->visible(fn (Debit $record) => $record->debours_amount > 0),

                            TextEntry::make('tva_amount')
                                ->label('TVA')
                                ->money('TND')
                                ->prefix(fn (Debit $record) => "({$record->tva_rate}%) ")
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),
                            
                            TextEntry::make('tf_value')
                                ->label('Timbre Fiscal')
                                ->money('TND')
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('amount_ttc')
                                ->label('Montant TTC')
                                ->money('TND')
                                ->alignEnd()
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('rs_amount')
                                ->label('Retenue à la Source')
                                ->badge()
                                ->color('danger')
                                ->money('TND')
                                ->alignEnd()
                                ->visible(fn (Debit $record) => $record->rs_amount > 0)
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextEntry::make('net_to_pay')
                                ->label('NET À PAYER')
                                ->money('TND')
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