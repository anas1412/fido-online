<?php

namespace App\Filament\Dashboard\Pages\Tenancy;

use App\Models\Tenant;
use App\Models\TenantInvite;
use Filament\Actions\Action; // Correct import for v4 Actions
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section; // Correct import for v4 Sections
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema; // Correct import for v4 Schema
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return 'Bienvenue';
    }

    public function getTitle(): string
    {
        return 'Commencez votre aventure';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            
            // --- SECTION 1: CREATE NEW TENANT ---
            Section::make('Créer une nouvelle entreprise')
                ->icon('heroicon-o-plus-circle')
                ->description('Configurez votre espace de travail pour commencer.')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom de l\'entreprise')
                        ->required()
                        ->maxLength(255),

                    Select::make('type')
                        ->label('Type d\'entreprise')
                        ->options([
                            'accounting' => 'Société comptable',
                            'commercial' => 'Société commerciale',
                        ])
                        ->required()
                        ->placeholder('Sélectionnez un type'),
                ])
                // Native v4 way to add buttons to a section
                ->footer([
                    Action::make('register')
                        ->label('Créer mon entreprise')
                        ->action('register') // Calls the standard register() method
                        ->color('primary')
                        ->icon('heroicon-m-sparkles'),
                ]),

            // --- SECTION 2: JOIN EXISTING TENANT ---
            Section::make('Rejoindre une équipe')
                ->icon('heroicon-o-user-group')
                ->description('Vous avez reçu un code ou un lien d\'invitation ?')
                ->collapsed(false) 
                ->schema([]) // Empty schema, just using the footer for the button
                ->footer([
                    Action::make('open_join_modal')
                        ->label('Saisir un code d\'invitation')
                        ->color('gray')
                        ->icon('heroicon-m-arrow-right-end-on-rectangle')
                        ->modalWidth('md')
                        ->modalHeading('Rejoindre une entreprise')
                        ->form([
                            TextInput::make('invite_code')
                                ->label('Code ou Lien d\'invitation')
                                ->placeholder('Ex: https://fido.tn/invite/xyz...')
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            $this->processJoin($data['invite_code']);
                        }),
                ]),
        ]);
    }

    // --- LOGIC: Create Tenant ---
    protected function handleRegistration(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $originalSlug = Str::slug($data['name']);
            $slug = $originalSlug;
            $count = 1;

            // Ensure unique slug
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $data['slug'] = $slug;

            $tenant = Tenant::create($data);

            Auth::user()->tenants()->attach($tenant, ['is_owner' => true, 'is_mod' => true]);

            return $tenant;
        });
    }

    // --- LOGIC: Join Tenant ---
    protected function processJoin($rawInput)
    {
        if (filter_var($rawInput, FILTER_VALIDATE_URL)) {
            $inviteCode = basename(parse_url($rawInput, PHP_URL_PATH));
        } else {
            $inviteCode = $rawInput;
        }

        $user = Auth::user();
        
        $invite = TenantInvite::where('code', $inviteCode)
            ->whereNull('used_by')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invite) {
            Notification::make()
                ->title('Invitation invalide')
                ->body('Ce code est introuvable ou expiré.')
                ->danger()
                ->send();
            
            $this->halt();
            return;
        }

        if ($user->tenants()->where('tenant_id', $invite->tenant_id)->exists()) {
             Notification::make()
                ->title('Déjà membre')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () use ($user, $invite) {
            $user->tenants()->syncWithoutDetaching([$invite->tenant_id]);
            $invite->update(['used_by' => $user->id]);
        });

        Notification::make()
            ->title('Bienvenue !')
            ->success()
            ->send();

        $this->redirect(filament()->getUrl(tenant: $invite->tenant));
    }

    // --- CONFIG: Hide Default Footer Buttons ---
    protected function getFormActions(): array
    {
        return [];
    }
}