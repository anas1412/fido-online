<?php

namespace App\Filament\Dashboard\Resources\TenantUsers;

use App\Filament\Dashboard\Resources\TenantUsers\Pages\ListTenantUsers;
use App\Models\TenantUserPivot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use UnitEnum;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class TenantUserResource extends Resource
{
    protected static ?string $model = TenantUserPivot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'user.name';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Administrative';

    protected static ?int $navigationSort = 11;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                IconColumn::make('is_owner') 
                    ->label('') 
                    ->boolean() 
                    ->trueIcon(Heroicon::OutlinedStar) 
                    ->falseIcon(Heroicon::OutlinedUser)
                    ->trueColor('warning')
                    ->falseColor('primary'),
                TextColumn::make('user.name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('is_mod')
                    ->label('Moderateur')
                    ->toggleable()
                    ->onIcon(Heroicon::Bolt)
                    ->offIcon(Heroicon::User)
                    ->getStateUsing(fn(TenantUserPivot $record) => $record->is_mod)
                    ->action(function (TenantUserPivot $record, bool $state) {
                        $record->update(['is_mod' => $state]);
                    })
                    ->disabled(function (TenantUserPivot $record) {
                        $currentUser = Auth::user();
                        $tenantId = $record->tenant_id;

                        // Check if current user is owner
                        $currentUserIsOwner = $currentUser->tenants()
                            ->where('tenant_id', $tenantId)
                            ->wherePivot('is_owner', true)
                            ->exists();

                        if (!$currentUserIsOwner) return true;

                        // Owner implies Mod, cannot disable own mod status
                        if ($record->user_id === $currentUser->id) return true;

                        return false;
                    }),
            ])
            ->actions([
                // --- TRANSFER OWNERSHIP ACTION ---
                Action::make('transfer_ownership')
                    ->label('Céder la propriété')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Transférer la propriété')
                    ->modalDescription('Êtes-vous sûr ? Vous perdrez vos droits de propriétaire et deviendrez modérateur.')
                    ->action(function (TenantUserPivot $record) {
                        $currentUser = Auth::user();
                        
                        DB::transaction(function () use ($record, $currentUser) {
                            // 1. Downgrade current owner (becomes Mod only)
                            TenantUserPivot::where('tenant_id', $record->tenant_id)
                                ->where('user_id', $currentUser->id)
                                ->update([
                                    'is_owner' => false, 
                                    'is_mod' => true
                                ]);

                            // 2. Upgrade target user (becomes Owner & Mod)
                            $record->update([
                                'is_owner' => true, 
                                'is_mod' => true
                            ]);
                        });

                        Notification::make()
                            ->title('Propriété transférée')
                            ->success()
                            ->send();
                            
                        return redirect()->to('/dashboard'); // Reload to refresh permissions
                    })
                    ->visible(function (TenantUserPivot $record) {
                        $currentUser = Auth::user();
                        
                        // Visible only if I am the owner AND the row is not me
                        $amIOwner = $currentUser->tenants()
                            ->where('tenant_id', $record->tenant_id)
                            ->wherePivot('is_owner', true)
                            ->exists();

                        return $amIOwner && $record->user_id !== $currentUser->id;
                    }),

                Action::make('kick')
                    ->label('Exclure')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TenantUserPivot $record) {
                        $record->tenant->users()->detach($record->user_id);
                    })
                    ->visible(function (TenantUserPivot $record) {
                        $currentUser = Auth::user();
                        $tenantId = $record->tenant_id;

                        if ($record->user->is_admin) return false;
                        if ($record->is_owner) return false;

                        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $tenantId)->first()->pivot ?? null;
                        if (!$currentUserTenantPivot || (!$currentUserTenantPivot->is_owner && !$currentUserTenantPivot->is_mod)) {
                            return false;
                        }

                        if ($currentUser->is_admin && $record->user->id === $currentUser->id) return false;

                        if (($currentUserTenantPivot->is_owner || $currentUserTenantPivot->is_mod) && $record->user->id === $currentUser->id) {
                            return false;
                        }

                        return true;
                    })
            ])
            ->bulkActions([]);
    }

    public static function canViewAny(): bool
    {
        $currentUser = Auth::user();
        $currentTenant = filament()->getTenant();

        if (!$currentUser || !$currentTenant) return false;
        if ($currentUser->is_admin) return true;

        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $currentTenant->id)->first()->pivot ?? null;

        return $currentUserTenantPivot && ($currentUserTenantPivot->is_owner || $currentUserTenantPivot->is_mod);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsers::route('/'),
        ];
    }
}