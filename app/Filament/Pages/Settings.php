<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * @property-read Schema $form
 */
class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;
    
    protected static ?string $slug = 'settings';
    protected static ?string $title = 'Paramètres du site';
    protected static ?string $navigationLabel = 'Paramètres Globaux';

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    
                    // --- SECTION 1: Informations Générales ---
                    Section::make('Informations Générales')
                        ->description('Détails de contact et identification de la société.')
                        ->schema([
                            TextInput::make('site_name')
                                ->label('Nom de la société')
                                ->required()
                                ->columnSpanFull(),

                            TextInput::make('support_email')
                                ->label('Email de contact')
                                ->email()
                                ->required(),

                            TextInput::make('support_phone')
                                ->label('Téléphone')
                                ->tel()
                                ->required(),
                        ])
                        ->columns(2),

                    // --- SECTION 2: Fiscalité (Taxes) ---
                    Section::make('Taux et Taxes')
                        ->description('Configuration des taux de TVA, RS et Timbre Fiscal.')
                        ->schema([
                            // TVA 19%
                            TextInput::make('tva_rate')
                                ->label('Taux de TVA (Standard)')
                                ->helperText('Taux normal (ex: 19%).')
                                ->numeric()
                                ->step(0.01)
                                ->suffix('%')
                                ->default(19.00)
                                ->required(),

                            // TVA 7%
                            TextInput::make('tva_reduced_rate')
                                ->label('Taux de TVA (Réduit)')
                                ->helperText('Taux réduit (ex: 7%).')
                                ->numeric()
                                ->step(0.01)
                                ->suffix('%')
                                ->default(7.00)
                                ->required(),

                            // RS (Retenue à la Source)
                            TextInput::make('rs_rate')
                                ->label('Taux de RS (Retenue)')
                                ->helperText('Retenue à la source (ex: 3%).')
                                ->numeric()
                                ->step(0.01)
                                ->suffix('%')
                                ->default(3.00)
                                ->required(),

                            // Timbre Fiscal
                            TextInput::make('tf_rate')
                                ->label('Timbre Fiscal')
                                ->helperText('Montant fixe par facture.')
                                ->numeric()
                                ->step(0.001)
                                ->suffix('TND')
                                ->default(1.000)
                                ->required(),
                        ])
                        ->columns(2),
                ])
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Enregistrer les paramètres')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $record = $this->getRecord();
        
        $record->update($data);

        Notification::make()
            ->title('Paramètres mis à jour avec succès')
            ->success()
            ->send();
    }

    public function getRecord(): Setting
    {
        return Setting::singleton();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }
}