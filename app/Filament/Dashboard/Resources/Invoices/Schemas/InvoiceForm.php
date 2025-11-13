<?php

namespace App\Filament\Dashboard\Resources\Invoices\Schemas;

use App\Models\Client;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification; // Add this line
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make()
                ->steps([
                    Step::make('Invoice Info')
                        ->schema([
                            Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nom du client')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('contact_person')
                                        ->label('Personne à contacter')
                                        ->maxLength(255),
                                    TextInput::make('email')
                                        ->label('Adresse e-mail')
                                        ->email()
                                        ->maxLength(255),
                                    TextInput::make('phone')
                                        ->label('Téléphone')
                                        ->tel()
                                        ->maxLength(255),
                                    Textarea::make('address')
                                        ->label('Adresse')
                                        ->columnSpanFull(),
                                    Textarea::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull(),
                                    TextInput::make('status')
                                        ->label('Statut')
                                        ->required()
                                        ->default('active')
                                        ->maxLength(255),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $data['tenant_id'] = filament()->getTenant()->id;
                                    $client = Client::create($data);

                                    Notification::make()
                                        ->title('Client créé')
                                        ->success()
                                        ->send();

                                    return $client->getKey();
                                }),

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
                        ]),

                    Step::make('Invoice Items')
                        ->schema([
                            Repeater::make('invoiceItems')
                                ->relationship()
                                ->columns(4)
                                ->defaultItems(1)
                                ->createItemButtonLabel('Ajouter un article')
                                ->deleteAction(fn ($action) => $action->label('Supprimer'))
                                ->reorderable(false)
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    // Update total_amount whenever repeater changes
                                    $set('total_amount', collect($state ?? [])
                                        ->sum(fn ($item) => ((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0)))
                                    );
                                })
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Produit')
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->nullable()
                                        ->requiredWithout('name')
                                        ->reactive()
                                        ->hidden(fn ($get) => filled($get('name')))
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            if ($state) {
                                                $unitPrice = Product::find($state)?->unit_price ?? 0;
                                                $set('unit_price', $unitPrice);
                                                $quantity = $get('quantity') ?? 1;
                                                $set('total', $unitPrice * $quantity);
                                            }
                                        }),

                                    TextInput::make('name')
                                        ->label('Nom du Service/Produit')
                                        ->nullable()
                                        ->requiredWithout('product_id')
                                        ->reactive()
                                        ->hidden(fn ($get) => filled($get('product_id'))),

                                    TextInput::make('quantity')
                                        ->label('Quantité')
                                        ->numeric()
                                        ->required()
                                        ->default(1)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $unitPrice = $get('unit_price') ?? 0;
                                            $set('total', ($state ?? 1) * $unitPrice);
                                        }),

                                    TextInput::make('unit_price')
                                        ->label('Prix Unitaire')
                                        ->numeric()
                                        ->required()
                                        ->default(0.0)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $quantity = $get('quantity') ?? 1;
                                            $set('total', ($state ?? 0) * $quantity);
                                        }),

                                    TextInput::make('total')
                                        ->label('Total')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->reactive(),
                                ]),
                        ]),

                    Step::make('Summary')
                        ->schema([
                            TextInput::make('total_amount')
                                ->label('Montant Total')
                                ->numeric()
                                ->disabled() // prevents editing but updates reactively
                                ->dehydrated(true)
                                ->reactive()
                                ->default(0.0),
                        ]),
                ])->columnSpan('full'),
        ]);
    }
}
