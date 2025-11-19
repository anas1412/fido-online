<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class HonoraireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make()
                ->steps([
                    Step::make('Honoraire Info')
                        ->schema([
                            Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')->label('Nom du client')->required()->maxLength(255),
                                    TextInput::make('contact_person')->label('Personne à contacter')->maxLength(255),
                                    TextInput::make('email')->label('Adresse e-mail')->email()->maxLength(255),
                                    TextInput::make('phone')->label('Téléphone')->tel()->maxLength(255),
                                    Textarea::make('address')->label('Adresse')->columnSpanFull(),
                                    Textarea::make('notes')->label('Notes')->columnSpanFull(),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $data['tenant_id'] = filament()->getTenant()->id;
                                    $client = Client::create($data);
                                    Notification::make()->title('Client créé')->success()->send();
                                    return $client->getKey();
                                }),

                            TextInput::make('honoraire_number')
                                ->label('Numéro d\'honoraire')
                                ->disabled(),

                            Textarea::make('object')
                                ->label('Objet')
                                ->columnSpanFull(),

                            DatePicker::make('issue_date')
                                ->label('Date d\'émission')
                                ->required(),
                        ]),

                    Step::make('Montants & Résumé')
                        ->schema([
                            TextInput::make('amount_ht')
                                ->label('Montant HT')
                                ->numeric()
                                ->required(),

                            TextInput::make('amount_ttc')
                                ->label('Montant TTC')
                                ->numeric()
                                ->required(),

                            TextInput::make('tva_rate')
                                ->label('Taux TVA')
                                ->numeric(),

                            TextInput::make('rs_rate')
                                ->label('Taux RS')
                                ->numeric(),

                            TextInput::make('tf_rate')
                                ->label('Taux TF')
                                ->numeric(),

                            TextInput::make('total_amount')
                                ->label('Montant Total')
                                ->numeric()
                                ->disabled(),
                        ]),
                ])->columnSpan('full'),
        ]);
    }
}
