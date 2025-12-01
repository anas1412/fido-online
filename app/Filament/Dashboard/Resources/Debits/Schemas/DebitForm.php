<?php

namespace App\Filament\Dashboard\Resources\Debits\Schemas;

use App\Models\Client;
use App\Models\Setting;
// Ensure you have this class, otherwise remove the createOptionForm line
use App\Filament\Dashboard\Resources\Clients\Schemas\ClientForm; 
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DebitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make()
                ->steps([
                    // --- STEP 1: INFO ---
                    Step::make('Informations Note')
                        ->schema([
                            Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                ->default(fn () => request()->query('client_id'))
                                ->disabled(fn () => request()->has('client_id'))
                                ->dehydrated(true)
                                // Reuse Client Form for consistency
                                ->createOptionForm(class_exists(ClientForm::class) ? ClientForm::components() : [])
                                ->createOptionUsing(function (array $data): int {
                                    $data['tenant_id'] = filament()->getTenant()->id;
                                    return Client::create($data)->getKey();
                                }),

                            Select::make('invoice_id')
                                ->relationship('invoice', 'invoice_number')
                                ->label('Facture de référence')
                                ->searchable()
                                ->placeholder('Lier à une facture')
                                ->visible(fn () => filament()->getTenant()->usesInvoices()),

                            // 2. Show this IF tenant uses Honoraires (Accounting/Medical)
                            Select::make('honoraire_id')
                                ->relationship('honoraire', 'honoraire_number')
                                ->label('Honoraire de référence')
                                ->searchable()
                                ->placeholder('Lier à une note d\'honoraire')
                                ->visible(fn () => filament()->getTenant()->usesHonoraires()),

                            TextInput::make('debit_number')
                                ->label('Numéro')
                                ->placeholder('Généré automatiquement')
                                ->disabled()
                                ->dehydrated(false),

                            Textarea::make('object')
                                ->label('Objet')
                                ->columnSpanFull(),

                            DatePicker::make('issue_date')
                                ->label('Date d\'émission')
                                ->default(now())
                                ->required(),
                        ]),

                    // --- STEP 2: AMOUNTS ---
                    Step::make('Montants & Taxes')
                        ->schema([
                            // Exemption Toggles
                            Section::make('Régime Fiscal')
                                ->schema([
                                    Toggle::make('exonere_tva')->label('Exonéré TVA')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotals($get, $set)),
                                    Toggle::make('exonere_rs')->label('Exonéré RS')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotals($get, $set)),
                                    Toggle::make('exonere_tf')->label('Exonéré Timbre')->live()->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotals($get, $set)),
                                ])->columns(3),

                            // Inputs
                            TextInput::make('amount_ht')
                                ->label('Montant HT')
                                ->numeric()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotals($get, $set)),

                            TextInput::make('debours_amount')
                                ->label('Débours (Non taxés)')
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateTotals($get, $set)),

                            // Read-only Calculated Fields
                            TextInput::make('tva_rate')->label('Taux TVA (%)')->readOnly(),
                            TextInput::make('tva_amount')->label('Montant TVA')->readOnly(),
                            TextInput::make('tf_value')->label('Timbre Fiscal')->readOnly(),
                            TextInput::make('amount_ttc')->label('Montant TTC')->readOnly(),
                            TextInput::make('rs_rate')->label('Taux RS (%)')->readOnly(),
                            TextInput::make('rs_amount')->label('Montant RS')->readOnly(),

                            TextInput::make('net_to_pay')
                                ->label('NET À PAYER')
                                ->numeric()
                                ->extraInputAttributes(['style' => 'font-weight: bold; font-size: 1.2em; color: green'])
                                ->readOnly(),
                        ])->columns(2),
                ])->columnSpanFull(),
        ]);
    }

    public static function calculateTotals(Get $get, Set $set): void
    {
        $tenant = filament()->getTenant();
        $settings = Setting::singleton();
        
        $ht = (float) $get('amount_ht');
        $debours = (float) $get('debours_amount');
        
        $exoTva = (bool) $get('exonere_tva');
        $exoRs = (bool) $get('exonere_rs');
        $exoTf = (bool) $get('exonere_tf');

        // Rates
        $tvaRate = $exoTva ? 0 : ($tenant?->getDefaultTvaRate() ?? 19.0);
        // RS is usually 1% for invoices/debits, 3% for honoraires. Adjust if needed.
        $rsRate = $exoRs ? 0 : ($settings->rs_rate ?? 1.0); 
        $tfValue = $exoTf ? 0 : ($settings->tf_rate ?? 1.000);

        // Calculations
        $tvaAmount = $ht * ($tvaRate / 100);
        
        // TTC includes Debours
        $ttc = $ht + $tvaAmount + $tfValue + $debours;
        
        // RS is calculated on (TTC - Debours) because you don't withhold tax on reimbursement
        $baseForRs = $ttc - $debours;
        $rsAmount = $baseForRs * ($rsRate / 100);
        
        $netToPay = $ttc - $rsAmount;

        // Set State
        $set('tva_rate', $tvaRate);
        $set('tva_amount', number_format($tvaAmount, 3, '.', ''));
        $set('tf_value', number_format($tfValue, 3, '.', ''));
        $set('amount_ttc', number_format($ttc, 3, '.', ''));
        $set('rs_rate', $rsRate);
        $set('rs_amount', number_format($rsAmount, 3, '.', ''));
        $set('net_to_pay', number_format($netToPay, 3, '.', ''));
    }
}