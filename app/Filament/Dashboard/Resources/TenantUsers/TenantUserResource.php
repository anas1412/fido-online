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


class TenantUserResource extends Resource
{
    protected static ?string $model = TenantUserPivot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'user.name';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Administrative';

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

                        // Check if the current authenticated user is the owner of this tenant
                        $currentUserIsOwner = $currentUser->tenants()->where('tenant_id', $tenantId)->wherePivot('is_owner', true)->exists();

                        // If the current authenticated user is not an owner, disable the toggle
                        if (!$currentUserIsOwner) {
                            return true;
                        }

                        // If the current authenticated user IS an owner, and the record being displayed is for THEM,
                        // then disable the toggle (because an owner is implicitly a mod)
                        if ($currentUserIsOwner && $record->user_id === $currentUser->id) {
                            return true;
                        }

                        // Otherwise, allow the toggle (owner can change other members' mod status)
                        // Explicitly enable if current user is owner and target is another user
                        if ($currentUserIsOwner && $record->user_id !== $currentUser->id) {
                            return false;
                        }

                        return true;
                    }),
            ])
            ->actions([
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

                        // Rule: is_admin can never be kicked.
                        if ($record->user->is_admin) {
                            return false;
                        }

                        // Rule: is_owner can never be kicked from his tenant.
                        if ($record->is_owner) {
                            return false;
                        }

                        // Rule: Only is_owner or is_mod can see the action.
                        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $tenantId)->first()->pivot ?? null;
                        if (!$currentUserTenantPivot || (!$currentUserTenantPivot->is_owner && !$currentUserTenantPivot->is_mod)) {
                            return false; // Current user is neither owner nor mod
                        }

                        // Rule: is_admin cannot remove himself (system admin).
                        if ($currentUser->is_admin && $record->user->id === $currentUser->id) {
                            return false;
                        }

                        // Rule: Tenant owner or mod cannot kick themselves.
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

        if (!$currentUser || !$currentTenant) {
            return false;
        }

        // System admin can view any resource
        if ($currentUser->is_admin) {
            return true;
        }

        // Check if current user is owner or mod of the current tenant
        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $currentTenant->id)->first()->pivot ?? null;

        if ($currentUserTenantPivot && ($currentUserTenantPivot->is_owner || $currentUserTenantPivot->is_mod)) {
            return true;
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsers::route('/'),
        ];
    }
}
