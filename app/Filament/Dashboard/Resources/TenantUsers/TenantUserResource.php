<?php

namespace App\Filament\Dashboard\Resources\TenantUsers;

use App\Filament\Dashboard\Resources\TenantUsers\Pages\ListTenantUsers;
use App\Models\TenantUserPivot;
use BackedEnum;
use Filament\Resources\Resource;
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
            ->actions([])
            ->bulkActions([]);
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
