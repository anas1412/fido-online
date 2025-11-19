<?php

namespace App\Filament\Dashboard\Pages\Tenancy;

use App\Models\Tenant;
use App\Models\TenantInvite;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added for data safety
use Illuminate\Support\Str;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return 'Créer une nouvelle entreprise';
    }

    public function getTitle(): string
    {
        return 'Créer une nouvelle entreprise';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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
        ]);
    }

    protected function handleRegistration(array $data): Tenant
    {
        // Wrapped in transaction to prevent "Ghost Tenants" (created but not attached to user)
        return DB::transaction(function () use ($data) {
            
            // Fix: Unique Slug Generation
            // Without this loop, creating a 2nd company with the same name will crash your app.
            $originalSlug = Str::slug($data['name']);
            $slug = $originalSlug;
            $count = 1;

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

    protected function getFormActions(): array
    {
        return [
            // Primary action: create a new company
            parent::getRegisterFormAction()->label('Créer une nouvelle entreprise'),

            // Secondary action: join existing company via modal
            Action::make('join')
                ->label('Rejoindre une entreprise existante')
                ->color('gray')
                ->modalWidth('md')
                ->form([
                    TextInput::make('invite_code')
                        ->label('Code d\'invitation')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $rawInput = $data['invite_code'];

                    // Fix: Safer URL parsing. 
                    // 'basename' handles trailing slashes better than 'explode/end'
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
                            ->title('Code d\'invitation invalide ou déjà utilisé.')
                            ->danger()
                            ->send();
                        
                        $this->halt(); // Stops the modal from closing if error
                        return;
                    }

                    // Fix: Check if user is ALREADY in the tenant
                    if ($user->tenants()->where('tenant_id', $invite->tenant_id)->exists()) {
                         Notification::make()
                            ->title('Vous êtes déjà membre de cette entreprise.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Fix: Transaction ensures invite is marked used ONLY if attach succeeds
                    DB::transaction(function () use ($user, $invite) {
                        $user->tenants()->syncWithoutDetaching([$invite->tenant_id]);
                        $invite->update(['used_by' => $user->id]);
                    });

                    Notification::make()
                        ->title('Vous avez rejoint l\'entreprise avec succès !')
                        ->success()
                        ->send();

                    // Redirect to tenant dashboard
                    $this->redirect(filament()->getUrl(tenant: $invite->tenant));
                }),
        ];
    }
}