<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Filament\Schemas\Schema;
use Exception;

class AIHelp extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $pluralModelLabel = 'Assistance IA';
    protected static ?string $navigationLabel = 'Assistance IA';
    protected static ?string $title = 'Assistance IA';

    protected string $view = 'filament.dashboard.pages.a-i-help';

    public ?array $data = [];
    public string $prompt = '';
    public ?string $response = null;
    public array $chatHistory = [];

    // Instructions for Fido's expertise
    private string $fidoInstructions = "Vous êtes Fido, un assistant IA expert en comptabilité. Répondez de manière concise, claire et professionnelle.";

    public function mount(): void
    {
        $this->initializeChat();
        $this->form->fill();
    }

    private function initializeChat(): void
    {
        $userName = auth()->user()?->name ?? 'Utilisateur';

        // Initial greeting is clean; instructions are NOT displayed
        $this->chatHistory = [
            [
                'role' => 'model',
                'parts' => [[
                    'text' => "Bonjour {$userName}, je suis Fido, votre assistant expert en comptabilité. Comment puis-je vous aider aujourd'hui ?"
                ]],
            ],
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('prompt')
                    ->label('Votre Requête')
                    ->placeholder("Entrez votre question ou requête pour Fido...")
                    ->rows(5)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submitPrompt(): void
    {
        $data = $this->form->getState();
        $prompt = $data['prompt'] ?? null;

        if (blank($prompt)) {
            Notification::make()
                ->title('Requête vide')
                ->body('Veuillez entrer une question avant de soumettre.')
                ->danger()
                ->send();
            return;
        }

        $this->response = null;

        $this->chatHistory[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            Notification::make()
                ->title('Clé API manquante')
                ->body('Définissez GEMINI_API_KEY dans votre fichier .env.')
                ->danger()
                ->send();
            return;
        }

        try {
            $aiResponse = $this->getGeminiResponse($apiKey, $prompt);

            if ($aiResponse) {
                $this->chatHistory[] = ['role' => 'model', 'parts' => [['text' => $aiResponse]]];
                $this->response = $aiResponse;
                // Dispatch scroll event
                $this->dispatch('scroll-to-bottom');
            } else {
                Notification::make()
                    ->title('Erreur')
                    ->body('Aucune réponse valide reçue du modèle.')
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Une erreur est survenue : ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }


    /**
     * Prepend Fido instructions only to the latest user input
     */
    private function getGeminiResponse(string $apiKey, string $latestUserPrompt): ?string
    {
        $contents = [];

        foreach ($this->chatHistory as $turn) {
            if ($turn['role'] === 'user' && $turn['parts'][0]['text'] === $latestUserPrompt) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [[
                        'text' => $this->fidoInstructions . "\n" . $latestUserPrompt
                    ]],
                ];
            } elseif ($turn['role'] === 'user' || $turn['role'] === 'model') {
                $contents[] = $turn; // previous messages sent as-is
            }
        }

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
            ['contents' => $contents]
        );

        if (!$response->successful()) {
            \Log::error('Gemini API error: ' . $response->body());
            return null;
        }

        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function clearConversation(): void
    {
        $this->initializeChat();
        $this->response = null;
        $this->prompt = '';
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
