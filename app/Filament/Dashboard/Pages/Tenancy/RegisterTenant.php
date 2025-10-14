<?php

namespace App\Filament\Dashboard\Pages\Tenancy;

use App\Models\Tenant;
use App\Models\TenantInvite;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterTenant extends BaseRegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register or Join Company';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Company Type')
                    ->options([
                        'accounting' => 'Accounting',
                        'commercial' => 'Commercial',
                    ])
                    ->required(),
            ]);
    }

    // This override is no longer needed because the parent logic is sufficient
    // once we fix the slug issue correctly. We will use handleRegistration instead.
    
    protected function handleRegistration(array $data): Tenant
    {
        $data['slug'] = Str::slug($data['name'] . '-' . Str::random(5));
        
        $tenant = Tenant::create($data);

        $user = Auth::user();
        $user->tenants()->attach($tenant);

        return $tenant;
    }


    public function getFormActions(): array
    {
        $joinAction = Action::make('join')
            ->label('Join an Existing Company')
            ->color('gray')
            ->modalWidth('md')
            ->schema([
                TextInput::make('invite_code')
                    ->label('Invite Code')
                    ->required(),
            ])
            ->action(function (array $data) {
                $inviteCode = $data['invite_code'];
                $user = Auth::user();

                $tenantInvite = TenantInvite::where('code', $inviteCode)
                    ->whereNull('used_by')
                    ->first();

                if (! $tenantInvite) {
                    Notification::make()->title('Invalid or used invite code.')->danger()->send();
                    $this->halt();
                }

                $user->tenants()->attach($tenantInvite->tenant_id);
                $tenantInvite->update(['used_by' => $user->id]);

                Notification::make()->title('Successfully joined company!')->success()->send();
                
                $tenant = Tenant::find($tenantInvite->tenant_id);
                
                // The redirect is the only thing needed to switch the tenant context.
                $this->redirect(filament()->getUrl(tenant: $tenant));
            });

        return [
            parent::getRegisterFormAction(),
            $joinAction,
        ];
    }
}