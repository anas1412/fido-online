<?php

namespace App\Filament\Dashboard\Resources\TenantInvites;

use App\Filament\Dashboard\Resources\TenantInvites\Pages\CreateTenantInvite;
use App\Filament\Dashboard\Resources\TenantInvites\Pages\EditTenantInvite;
use App\Filament\Dashboard\Resources\TenantInvites\Pages\ListTenantInvites;
use App\Models\TenantInvite;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use UnitEnum;
use BackedEnum;

class TenantInviteResource extends Resource
{
    protected static ?string $model = TenantInvite::class;

    // Correct type for Filament v4
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'code';

    // Correct type for navigation group
    protected static UnitEnum|string|null $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Invites';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->default(fn () => (string) Str::uuid())
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('expires_at')
                    ->required()
                    ->default(fn () => now()->addDays(7))
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('tenant_id')
                    ->required()
                    ->numeric()
                    ->default(fn () => filament()->getTenant()->id)
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('used_by')
                    ->numeric()
                    ->readOnly()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('expires_at')->dateTime(),
                TextColumn::make('used_by')
                    ->label('Used By User ID')
                    ->default('Not Used'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No invites found');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantInvites::route('/'),
            'create' => CreateTenantInvite::route('/create'),
            'edit' => EditTenantInvite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', filament()->getTenant()->id);
    }
}
