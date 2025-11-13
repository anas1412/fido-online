<?php

namespace App\Filament\Dashboard\Resources\Invoices\Schemas;

use App\Filament\Dashboard\Resources\Clients\ClientResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Générales')
                ->schema([
                    TextEntry::make('client.name')
                        ->label('Client')
                        ->columnSpan(1)
                        ->url(fn (Invoice $record): string => ClientResource::getUrl('view', ['record' => $record->client->id])),
                    TextEntry::make('invoice_number')
                        ->label('Numéro de Facture')
                        ->columnSpan(1),
                    TextEntry::make('status')
                        ->label('Statut')
                        ->columnSpan(1),
                    TextEntry::make('issue_date')
                        ->label("Date d'Émission")
                        ->date()
                        ->columnSpan(1),
                    TextEntry::make('due_date')
                        ->label("Date d'Échéance")
                        ->date()
                        ->columnSpan(1),
                    TextEntry::make('currency')
                        ->label('Devise')
                        ->columnSpan(1),
                    TextEntry::make('total_amount')
                        ->label('Montant Total')
                        ->money(fn (Invoice $record) => $record->currency)
                        ->columnSpan(1),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->compact(),

            Section::make('Articles de la Facture')
                ->schema([
                    RepeatableEntry::make('invoiceItems')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('product.name')
                                ->label('Produit')
                                ->columnSpan(2)
                                ->visible(fn (InvoiceItem $record): bool => filled($record->product_id)),
                            TextEntry::make('name')
                                ->label('Produit')
                                ->columnSpan(2)
                                ->visible(fn (InvoiceItem $record): bool => filled($record->name) && !filled($record->product_id)),
                            TextEntry::make('quantity')
                                ->label('Quantité')
                                ->columnSpan(1),
                            TextEntry::make('unit_price')
                                ->label('Prix Unitaire')
                                ->money(fn (InvoiceItem $record) => $record->invoice->currency)
                                ->columnSpan(1),
                            TextEntry::make('total')
                                ->label('Total')
                                ->money(fn (InvoiceItem $record) => $record->invoice->currency)
                                ->columnSpan(1),
                        ])
                        ->columns(5)
                        ->columnSpanFull(),
                ])->compact()
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
                ->columnSpanFull()->compact(),
        ]);
    }
}
