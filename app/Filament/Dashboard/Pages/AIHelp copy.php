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
use Illuminate\Support\Facades\Http;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Exception;

class AIHelp extends Page implements HasForms
{
    use InteractsWithForms;

    // --- ORIGINAL DECLARATIONS (KEPT EXACTLY AS REQUESTED) ---
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $pluralModelLabel = 'Assistance IA';
    protected static ?string $navigationLabel = 'Assistance IA';
    protected static ?string $title = 'Assistance IA';
    protected static ?string $slug = 'assistance-ia';
    protected string $view = 'filament.dashboard.pages.assistance-ia';

    public ?array $data = [];
    public string $prompt = '';
    public ?string $response = null;
    public array $chatHistory = [];

    private string $fidoInstructions = "Vous êtes Fido, un assistant IA expert en comptabilité. Répondez de manière concise, claire et professionnelle. Utilisez le Markdown.";

    public function mount(): void
    {
        $this->initializeChat();
        $this->form->fill();
    }

    private function initializeChat(): void
    {
        if (!empty($this->chatHistory)) return;

        $userName = auth()->user()?->name ?? 'Utilisateur';

        $this->chatHistory = [
            [
                'role' => 'model',
                'parts' => [[
                    'text' => "Bonjour **{$userName}**, je suis Fido. Comment puis-je vous aider aujourd'hui ?"
                ]],
                'timestamp' => now(),
            ],
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('prompt')
                    ->hiddenLabel() // UX: Hide label to look like a chat app
                    ->placeholder("Entrez votre question... (Maj+Entrée pour une nouvelle ligne)")
                    ->rows(1)
                    ->autosize() // UX: Auto-grow the textarea
                    ->required()
                    ->extraAttributes(['class' => 'resize-none']),
            ])
            ->statePath('data');
    }

    public function submitPrompt(): void
    {
        $data = $this->form->getState();
        $prompt = $data['prompt'] ?? null;

        if (blank($prompt)) return;

        // Add User Message
        $this->chatHistory[] = [
            'role' => 'user', 
            'parts' => [['text' => $prompt]],
            'timestamp' => now()
        ];

        // UX: Clear input immediately
        $this->form->fill(['prompt' => '']); 
        $this->dispatch('scroll-to-bottom');

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            Notification::make()->title('Clé API manquante')->danger()->send();
            return;
        }

        try {
            $aiResponse = $this->getGeminiResponse($apiKey, $prompt);

            if ($aiResponse) {
                $this->chatHistory[] = [
                    'role' => 'model', 
                    'parts' => [['text' => $aiResponse]],
                    'timestamp' => now()
                ];
                $this->response = $aiResponse;
                $this->dispatch('scroll-to-bottom');
            } else {
                Notification::make()->title('Erreur')->body('Aucune réponse.')->danger()->send();
            }

        } catch (\Exception $e) {
            Notification::make()->title('Erreur')->body($e->getMessage())->danger()->send();
        }
    }

    private function getGeminiResponse(string $apiKey, string $latestUserPrompt): ?string
    {
        $contents = [];

        // Simplified context builder
        foreach ($this->chatHistory as $turn) {
            // Skip the very last user message we just added locally, we will reconstruct it with instructions
            if ($turn['role'] === 'user' && $turn['parts'][0]['text'] === $latestUserPrompt) {
                continue;
            }
            // Send previous history for context (filtering out timestamps)
            $contents[] = [
                'role' => $turn['role'],
                'parts' => $turn['parts']
            ];
        }

        // Append current prompt with instructions
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $this->fidoInstructions . "\n" . $latestUserPrompt]]
        ];

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            ['contents' => $contents]
        );

        if (!$response->successful()) {
            \Log::error('Gemini Error: ' . $response->body());
            return null;
        }

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function clearConversation(): void
    {
        $this->chatHistory = [];
        $this->initializeChat();
        $this->response = null;
        Notification::make()->title('Conversation réinitialisée')->success()->send();
    }

    // UX: Move the clear button to the header to clean up the input area
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