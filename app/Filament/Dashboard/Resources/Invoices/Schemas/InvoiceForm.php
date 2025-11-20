<?php

namespace App\Filament\Dashboard\Resources\Invoices\Schemas;

use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make()
                ->steps([
                    // --- STEP 1: GENERAL INFO ---
                    Step::make('Infos Facture')
                        ->schema([
                            Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                // --- FIX: Check URL for client_id ---
                                ->default(fn () => request()->query('client_id'))
                                ->disabled(fn () => request()->has('client_id'))
                                ->dehydrated(true) // Always save the value even if disabled
                                // ------------------------------------
                                ->createOptionForm([
                                    TextInput::make('name')->required(),
                                    TextInput::make('contact_person'),
                                    TextInput::make('email')->email(),
                                    TextInput::make('phone')->tel(),
                                    Textarea::make('address'),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $data['tenant_id'] = filament()->getTenant()->id;
                                    return Client::create($data)->getKey();
                                }),

                            TextInput::make('invoice_number')
                                ->label('Numéro')
                                ->placeholder('Généré automatiquement')
                                ->disabled()
                                ->dehydrated(false),

                            DatePicker::make('issue_date')
                                ->label('Date Émission')
                                ->default(now())
                                ->required(),

                            DatePicker::make('due_date')
                                ->label('Date Échéance'),

                            Select::make('status')
                                ->options([
                                    'draft' => 'Brouillon',
                                    'sent' => 'Envoyée',
                                    'paid' => 'Payée',
                                    'overdue' => 'En retard',
                                ])
                                ->default('draft')
                                ->required(),

                            Select::make('currency')
                                ->label('Devise')
                                ->options([
                                    'TND' => 'TND (Dinar Tunisien)',
                                    'EUR' => 'EUR (Euro)',
                                    'USD' => 'USD (Dollar US)',
                                ])
                                ->default(fn () => filament()->getTenant()->currency ?? 'TND')
                                ->required(),
                        ])->columns(2),

                    // --- STEP 2: ITEMS (Smart Tax Logic) ---
                    Step::make('Articles & Services')
                        ->schema([
                            Repeater::make('invoiceItems')
                                ->relationship()
                                ->columnSpanFull()
                                ->columns(12)
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set))
                                ->schema([
                                    // 1. Product Selector
                                    Select::make('product_id')
                                        ->label('Produit')
                                        ->placeholder('Produit (Optionnel)')
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan(12)
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $product = Product::find($state);
                                                if ($product) {
                                                    $set('name', $product->name);
                                                    $set('unit_price', $product->unit_price);
                                                }
                                            }
                                        }),

                                    // 2. Description
                                    TextInput::make('name')
                                        ->label('Description')
                                        ->required()
                                        ->columnSpan(5),

                                    // 3. Quantity
                                    TextInput::make('quantity')
                                        ->label('Qté')
                                        ->numeric()
                                        ->default(1)
                                        ->columnSpan(1)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateRow($get, $set)),

                                    // 4. Price
                                    TextInput::make('unit_price')
                                        ->label('Prix Unit.')
                                        ->numeric()
                                        ->columnSpan(2)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateRow($get, $set)),

                                    // 5. Tax Logic
                                    Group::make([
                                        // Mode Selector (Virtual)
                                        Select::make('tax_option')
                                            ->label('TVA')
                                            ->options(function () {
                                                $settings = Setting::singleton();
                                                return [
                                                    'standard' => "Standard (" . ($settings->tva_rate ?? 19) . "%)",
                                                    'reduced'  => "Réduit (" . ($settings->tva_reduced_rate ?? 7) . "%)",
                                                    'exempt'   => "Exonéré (0%)",
                                                    'custom'   => "Autre / Manuel",
                                                ];
                                            })
                                            ->default('standard')
                                            ->selectablePlaceholder(false)
                                            ->dehydrated(false)
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $settings = Setting::singleton();
                                                $rate = match($state) {
                                                    'standard' => $settings->tva_rate ?? 19.00,
                                                    'reduced'  => $settings->tva_reduced_rate ?? 7.00,
                                                    'exempt'   => 0,
                                                    default    => $get('tva_rate'),
                                                };
                                                $set('tva_rate', $rate);
                                                self::calculateRow($get, $set);
                                                self::updateTotals($get, $set);
                                            })
                                            ->afterStateHydrated(function ($component, $state, Get $get) {
                                                $rate = $get('tva_rate');
                                                // Default new rows to Standard
                                                if ($rate === null) { $component->state('standard'); return; }
                                                
                                                $settings = Setting::singleton();
                                                if ((float)$rate == (float)($settings->tva_rate ?? 19)) $component->state('standard');
                                                elseif ((float)$rate == (float)($settings->tva_reduced_rate ?? 7)) $component->state('reduced');
                                                elseif ((float)$rate == 0) $component->state('exempt');
                                                else $component->state('custom');
                                            }),

                                        // Actual Rate Input
                                        TextInput::make('tva_rate')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(19.00)
                                            ->required()
                                            ->dehydrated(true)
                                            ->visible(fn (Get $get) => $get('tax_option') === 'custom')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotals($get, $set)),

                                    ])->columnSpan(2),

                                    // 6. Line Total
                                    TextInput::make('total')
                                        ->label('Total HT')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric()
                                        ->columnSpan(2),
                                ]),
                        ]),

                    // --- STEP 3: TOTALS ---
                    Step::make('Résumé & Taxes')
                        ->schema([
                             Section::make('Régime Fiscal')
                                ->schema([
                                    Toggle::make('exonere_tva')->label('Exonéré Tout TVA')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                                    Toggle::make('exonere_rs')->label('Exonéré RS')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                                    Toggle::make('exonere_tf')->label('Exonéré Timbre')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                                ])->columns(3),

                            Section::make('Calculs')
                                ->schema([
                                    TextInput::make('amount_ht')->label('Total HT')->readOnly(),
                                    TextInput::make('tva_amount')->label('Total TVA')->readOnly(),
                                    TextInput::make('tf_value')->label('Timbre Fiscal')->readOnly(),
                                    TextInput::make('amount_ttc')->label('Montant TTC')->readOnly(),
                                    TextInput::make('rs_amount')->label('Retenue Source')->readOnly(),
                                    TextInput::make('net_to_pay')->label('NET À PAYER')->readOnly()
                                        ->extraInputAttributes(['style' => 'font-size: 1.5rem; font-weight: bold; color: #16a34a;']),
                                ])->columns(2),
                        ]),
                ])->columnSpanFull(),
        ]);
    }

    // --- MATH LOGIC ---
    public static function calculateRow(Get $get, Set $set): void
    {
        $qty = (float) $get('quantity');
        $price = (float) $get('unit_price');
        $set('total', number_format($qty * $price, 3, '.', ''));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('invoiceItems') ?? [];
        $totalHT = 0;
        $totalTVA = 0;
        $isGlobalExempt = (bool) $get('exonere_tva');

        foreach ($items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            // Ensure valid float for rate
            $rate = isset($item['tva_rate']) && $item['tva_rate'] !== '' ? (float)$item['tva_rate'] : 19.0;
            
            $lineHT = $qty * $price;
            $effectiveRate = $isGlobalExempt ? 0 : $rate;
            $lineTax = $lineHT * ($effectiveRate / 100);
            $totalHT += $lineHT;
            $totalTVA += $lineTax;
        }

        $settings = Setting::singleton();
        $exoRs = (bool) $get('exonere_rs');
        $exoTf = (bool) $get('exonere_tf');
        $tfValue = $exoTf ? 0 : ($settings->tf_rate ?? 1.000);
        $ttc = $totalHT + $totalTVA + $tfValue;
        $rsRate = $exoRs ? 0 : ($settings->rs_rate ?? 0);
        $rsAmount = $ttc * ($rsRate / 100);
        $netToPay = $ttc - $rsAmount;

        $set('amount_ht', number_format($totalHT, 3, '.', ''));
        $set('tva_amount', number_format($totalTVA, 3, '.', ''));
        $set('tf_value', number_format($tfValue, 3, '.', ''));
        $set('amount_ttc', number_format($ttc, 3, '.', ''));
        $set('rs_amount', number_format($rsAmount, 3, '.', ''));
        $set('net_to_pay', number_format($netToPay, 3, '.', ''));
    }
}