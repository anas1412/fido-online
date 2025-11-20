<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;

class EditTenantProfile extends BaseEditTenantProfile
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('deleteTenant')
                ->label("Supprimer l'organisation")
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(fn () => 'Supprimer ' . (filament()->getTenant()?->name ?? "l'organisation") . ' ?')
                ->modalSubheading("Cette action supprimera définitivement l'organisation et toutes ses données.")
                ->modalButton('Oui, supprimer')
                ->action(function () {
                    $this->tenant->delete();

                    Notification::make()
                        ->title('Succès')
                        ->body("L'organisation {$this->tenant->name} a été supprimée.")
                        ->success()
                        ->send();

                    return \redirect()->to(filament()->getPanel('dashboard')->getUrl());
                })
                ->visible(function () {
                    $user = Auth::user();
                    $tenant = filament()->getTenant();
                    return $user->tenants()->where('tenant_id', $tenant->id)->wherePivot('is_owner', true)->exists();
                }),
        ];
    }

    public static function canAccess(): bool
    {
        $currentUser = Auth::user();
        $currentTenant = filament()->getTenant();

        if (!$currentUser || !$currentTenant) {
            return false;
        }

        if ($currentUser->is_admin) {
            return true;
        }

        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $currentTenant->id)->first()->pivot ?? null;

        if ($currentUserTenantPivot && ($currentUserTenantPivot->is_owner || $currentUserTenantPivot->is_mod)) {
            return true;
        }

        return false;
    }

    public static function getLabel(): string
    {
        return 'Profil de l\'organisation';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                Fieldset::make('Paramètres généraux')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([

                        TextInput::make('name')
                            ->label('Nom de l\'organisation')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $set('slug', Str::slug($state))),
                        
                        TextInput::make('slug')
                            ->label('Identifiant lisible')
                            ->readOnly(),
                        
                        // FIX: Display the type correctly based on the 3 types we defined
                        TextInput::make('type')
                            ->label('Type d\'activité')
                            ->disabled()
                            ->formatStateUsing(fn (string $state): string => match($state) {
                                'commercial' => 'Commerciale (Factures)',
                                'accounting' => 'Consulting / Libérale (Honoraires)',
                                'medical'    => 'Médical / Santé (Honoraires 7%)',
                                default => $state,
                            }),

                        Select::make('currency')
                            ->options([
                                'TND' => 'Dinar tunisien (TND)',
                                'EUR' => 'Euro (EUR)',
                                'USD' => 'Dollar américain (USD)',
                            ])
                            ->label('Devise par défaut')
                            ->default('TND')
                            ->required(),
                    ]),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        $this->tenant->refresh();
        return filament()->getUrl(tenant: $this->tenant);
    }
}