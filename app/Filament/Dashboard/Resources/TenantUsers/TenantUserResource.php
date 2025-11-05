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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

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
                        $tenantId = $record->tenant_id;
                        $user = Auth::user();
                        return !$user->tenants()->where('tenant_id', $tenantId)->wherePivot('is_owner', true)->exists();
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

                        // Check if current user is owner or mod of this tenant
                        $currentUserTenantPivot = $currentUser->tenants()->where('tenant_id', $tenantId)->first()->pivot ?? null;

                        if (!$currentUserTenantPivot || (!$currentUserTenantPivot->is_owner && !$currentUserTenantPivot->is_mod)) {
                            return false; // Current user is neither owner nor mod
                        }

                        // Constraint 1: is_mod cannot remove is_admin
                        if ($currentUserTenantPivot->is_mod && $record->user->is_admin) {
                            return false;
                        }

                        // Constraint 2: is_admin cannot remove himself
                        if ($currentUser->is_admin && $record->user->id === $currentUser->id) {
                            return false;
                        }

                        // Constraint 3: is_mod cannot kick themselves
                        if ($currentUserTenantPivot->is_mod && $record->user->id === $currentUser->id) {
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsers::route('/'),
        ];
    }
}
