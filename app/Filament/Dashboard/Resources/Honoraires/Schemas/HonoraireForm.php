<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Schemas;

use App\Models\Client;
use App\Models\Setting;
// Import the shared Client Form Schema
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
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class HonoraireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make()
                ->steps([
                    // --- STEP 1: INFO ---
                    Step::make('Honoraire Info')
                        ->schema([
                            Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                // --- CONTEXT LOGIC: Check URL for client_id ---
                                ->default(fn () => request()->query('client_id'))
                                ->disabled(fn () => request()->has('client_id'))
                                ->dehydrated(true) // Always save
                                // ----------------------------------------------
                                // REUSE SHARED CLIENT FORM:
                                ->createOptionForm(ClientForm::components())
                                ->createOptionUsing(function (array $data): int {
                                    $data['tenant_id'] = filament()->getTenant()->id;
                                    return Client::create($data)->getKey();
                                }),

                            TextInput::make('honoraire_number')
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

                            // Amount HT Input
                            TextInput::make('amount_ht')
                                ->label('Montant HT')
                                ->numeric()
                                ->required()
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
        
        $exoTva = (bool) $get('exonere_tva');
        $exoRs = (bool) $get('exonere_rs');
        $exoTf = (bool) $get('exonere_tf');

        // Logic: 0 if exempt, else Tenant Default (7 or 19)
        $tvaRate = $exoTva ? 0 : ($tenant?->getDefaultTvaRate() ?? 19.0);
        $rsRate = $exoRs ? 0 : ($settings->rs_rate ?? 3.0);
        $tfValue = $exoTf ? 0 : ($settings->tf_rate ?? 1.000);

        $tvaAmount = $ht * ($tvaRate / 100);
        $ttc = $ht + $tvaAmount + $tfValue;
        
        $rsAmount = $ttc * ($rsRate / 100); 
        
        $netToPay = $ttc - $rsAmount;

        $set('tva_rate', $tvaRate);
        $set('tva_amount', number_format($tvaAmount, 3, '.', ''));
        $set('tf_value', number_format($tfValue, 3, '.', ''));
        $set('amount_ttc', number_format($ttc, 3, '.', ''));
        $set('rs_rate', $rsRate);
        $set('rs_amount', number_format($rsAmount, 3, '.', ''));
        $set('net_to_pay', number_format($netToPay, 3, '.', ''));
    }
}