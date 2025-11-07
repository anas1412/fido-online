<?php

namespace App\Filament\Dashboard\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('invoice_number')
                    ->label('Numéro de Facture')
                    ->required(),
                DatePicker::make('issue_date')
                    ->label('Date d\'Émission')
                    ->required(),
                DatePicker::make('due_date')
                    ->label('Date d\'Échéance')
                    ->required(),
                TextInput::make('status')
                    ->label('Statut')
                    ->required()
                    ->default('pending'),
                TextInput::make('total_amount')
                    ->label('Montant Total')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Repeater::make('invoiceItems')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('Produit')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Quantité')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('total', $state * $get('unit_price'));
                            }),
                        TextInput::make('unit_price')
                            ->label('Prix Unitaire')
                            ->numeric()
                            ->required()
                            ->default(0.00)
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('total', $state * $get('quantity'));
                            }),
                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->dehydrated(true)
                            ->readOnly(),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->createItemButtonLabel('Ajouter un article')
                    ->deleteAction(fn ($action) => $action->label('Supprimer'))
                    ->reorderable(false)
                    ->columnSpanFull(),
            ]);
    }
}
