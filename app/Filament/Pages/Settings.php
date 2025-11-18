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
    protected static ?string $navigationLabel = 'Paramètres Globals';

    
    // Use the simple view provided by Filament 4 logic
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Fill the form with the singleton record
        // We use the helper method getRecord() to keep it clean
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // In Filament 4, we wrap fields in a Form component
                Form::make([
                    Section::make('Site Settings')
                        ->schema([
                            TextInput::make('site_name')
                                ->label('Site Name')
                                ->required(),

                            TextInput::make('support_email')
                                ->label('Support Email')
                                ->email()
                                ->required(),

                            TextInput::make('support_phone')
                                ->label('Support Phone')
                                ->required(),

                            TextInput::make('tva_rate')
                                ->label('TVA Rate')
                                ->numeric()
                                ->step(0.01)
                                ->required(),

                            TextInput::make('rs_rate')
                                ->label('RS Rate')
                                ->numeric()
                                ->step(0.01)
                                ->required(),

                            TextInput::make('tf_rate')
                                ->label('TF Rate')
                                ->numeric()
                                ->step(0.01)
                                ->required(),
                        ])
                        ->columns(2),
                ])
                // Link the enter key and submit event to the 'save' method
                ->livewireSubmitHandler('save')
                // Define the Save button in the footer of the form card
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Enregistrer')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
            ])
            // Bind the form to the $this->data property
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $record = $this->getRecord();
        
        $record->update($data);

        Notification::make()
            ->title('Paramètres enregistrés avec succès')
            ->success()
            ->send();
    }

    // Helper to get the singleton
    public function getRecord(): Setting
    {
        return Setting::singleton();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }
    
    // In Filament 4 Singular Resources, we don't typically use getHeaderActions() 
    // or getFormActions() for the main save button anymore; it's in the Form footer.
}