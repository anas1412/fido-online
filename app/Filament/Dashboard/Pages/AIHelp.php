<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class AIHelp extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static ?string $pluralModelLabel = 'Assistance IA';
    protected static ?string $navigationLabel = 'Assistance IA';
    protected static ?string $title = 'Assistance IA';
    protected static ?string $slug = 'assistance-ia';
    
    protected string $view = 'filament.dashboard.pages.assistance-ia';

    /**
     * Form State container
     */
    public ?array $data = [];
    
    /**
     * Chat History container
     * Initialized in PHP, updated by JS, and synced back via saveConversation()
     */
    public array $chatHistory = [];

    public function mount(): void
    {
        $this->form->fill();
        
        // FUTURE TODO: If you want to load previous chat history from the database,
        // you would do it here. For now, we start empty.
        // $this->chatHistory = auth()->user()->last_conversation ?? [];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('prompt')
                    ->hiddenLabel()
                    ->placeholder("Entrez votre question...")
                    ->rows(1)
                    ->autosize()
                    ->required()
                    // CRITICAL: This specific ID allows the AlpineJS 'document.getElementById' 
                    // to find the input value reliably, bypassing Livewire latency.
                    ->extraAttributes([
                        'class' => 'resize-none', 
                        'id' => 'fido-prompt-input' 
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Sync Method:
     * Called by AlpineJS via @this.saveConversation(history) when the stream ends.
     * This keeps the PHP state in sync with the JS UI.
     */
    public function saveConversation(array $history): void
    {
        $this->chatHistory = $history;
        
        // Example: Save to Database (Optional)
        // auth()->user()->update(['last_chat_history' => $history]);
    }

    /**
     * Resets the conversation state.
     */
    public function clearConversation(): void
    {
        $this->chatHistory = [];
        
        Notification::make()
            ->title('Conversation réinitialisée')
            ->success()
            ->send();
            
        // Reload the page to reset the JavaScript state cleanly
        $this->redirect(request()->header('Referer'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear')
                ->label('Effacer la conversation')
                ->color('gray')
                ->icon('heroicon-m-trash')
                ->action('clearConversation')
                ->requiresConfirmation(),
        ];
    }
}