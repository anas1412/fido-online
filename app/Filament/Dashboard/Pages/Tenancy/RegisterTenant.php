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
        $data['slug'] = Str::slug($data['name']);

        $tenant = Tenant::create($data);

        Auth::user()->tenants()->attach($tenant, ['is_owner' => true, 'is_mod' => true]);

        return $tenant;
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
                    $inviteCode = $data['invite_code'];
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
                        return;
                    }

                    // Attach user to tenant
                    $user->tenants()->syncWithoutDetaching([$invite->tenant_id]);

                    // Mark invite as used
                    $invite->update(['used_by' => $user->id]);

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
