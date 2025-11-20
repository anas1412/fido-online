<?php

namespace App\Filament\Dashboard\Resources\Invoices\Schemas;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // SECTION 1: HEADER INFO (Full Width)
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label('Numéro')
                        ->size('large') // String fixed
                        ->weight(FontWeight::Bold)
                        ->copyable(),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid' => 'success',
                            'sent' => 'info',
                            'overdue' => 'danger',
                            'draft' => 'gray',
                            default => 'gray',
                        }),

                    TextEntry::make('client.name')
                        ->label('Client')
                        ->url(fn (Invoice $record): string => ClientResource::getUrl('view', ['record' => $record->client->id]))
                        ->color('primary')
                        ->weight(FontWeight::SemiBold),

                    TextEntry::make('issue_date')
                        ->label('Date d\'Émission')
                        ->date('d M Y'),

                    TextEntry::make('due_date')
                        ->label('Date d\'Échéance')
                        ->date('d M Y')
                        ->color(fn (Invoice $record) => $record->due_date < now() && $record->status !== 'paid' ? 'danger' : 'gray'),

                    TextEntry::make('currency')
                        ->label('Devise')
                        ->badge()
                        ->color('gray'),
                ])
                ->columns(6), // Spread info across the top row
            // SECTION 2: FOOTER
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

            // SECTION 3: ITEMS (Full Width - No wrapping guaranteed)
            Section::make('Articles & Services')
                ->schema([
                    RepeatableEntry::make('invoiceItems')
                        ->hiddenLabel()
                        ->contained(false)
                        ->schema([
                            Grid::make(12)->schema([
                                // Name takes 50%
                                Group::make([
                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::Bold)
                                        ->color('gray-900'),
                                    
                                    TextEntry::make('product.name')
                                        ->hiddenLabel()
                                        ->size('small')
                                        ->color('gray')
                                        ->formatStateUsing(fn ($state) => "Réf: " . $state)
                                        ->visible(fn (InvoiceItem $record) => filled($record->product_id)),
                                ])->columnSpan(['default' => 12, 'md' => 6]),

                                // Quantity
                                TextEntry::make('quantity')
                                    ->hiddenLabel()
                                    ->formatStateUsing(fn ($state) => $state . ' x')
                                    ->alignEnd()
                                    ->color('gray')
                                    ->columnSpan(['default' => 3, 'md' => 1]),

                                // Price
                                TextEntry::make('unit_price')
                                    ->hiddenLabel()
                                    ->money(fn (InvoiceItem $record) => $record->invoice->currency)
                                    ->color('gray')
                                    ->alignEnd()
                                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                                    ->columnSpan(['default' => 4, 'md' => 2]),

                                // Total
                                TextEntry::make('total')
                                    ->hiddenLabel()
                                    ->money(fn (InvoiceItem $record) => $record->invoice->currency)
                                    ->weight(FontWeight::Bold)
                                    ->alignEnd()
                                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                                    ->columnSpan(['default' => 5, 'md' => 3]),
                            ]),
                        ]),
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
                                ->money(fn (Invoice $record) => $record->currency)
                                ->alignEnd(),

                            TextEntry::make('tva_amount')
                                ->label('TVA')
                                ->money(fn (Invoice $record) => $record->currency)
                                ->prefix(fn (Invoice $record) => "({$record->tva_rate}%) ")
                                ->alignEnd(),
                            
                            TextEntry::make('tf_value')
                                ->label('Timbre Fiscal')
                                ->money(fn (Invoice $record) => $record->currency)
                                ->alignEnd(),

                            TextEntry::make('rs_amount')
                                ->label('Retenue à la Source')
                                ->badge()
                                ->color('danger')
                                ->money(fn (Invoice $record) => $record->currency)
                                ->alignEnd()
                                ->visible(fn (Invoice $record) => $record->rs_amount > 0),

                            TextEntry::make('net_to_pay')
                                ->label('NET À PAYER')
                                ->money(fn (Invoice $record) => $record->currency)
                                ->size('large') // String fixed
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