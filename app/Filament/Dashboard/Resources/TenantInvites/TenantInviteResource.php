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
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use UnitEnum;
use BackedEnum;
use Webbingbrasil\FilamentCopyActions\Tables\CopyableTextColumn;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;
use LaraZeus\Popover\Tables\PopoverColumn;
use Illuminate\Support\Facades\Auth;

class TenantInviteResource extends Resource
{
    protected static ?string $model = TenantInvite::class;

    // Correct type for Filament v4
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $pluralModelLabel = 'Invitations';

    // Correct type for navigation group
    protected static UnitEnum|string|null $navigationGroup = 'Gestion Administrative';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Invitations';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(255)
                    ->default(fn () => (string) Str::random(10))
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('expires_at')
                    ->label('Expire le')
                    ->required()
                    ->default(fn () => now()->addWeeks(1))
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('tenant_id')
                    ->label('ID du Locataire')
                    ->required()
                    ->numeric()
                    ->default(fn () => filament()->getTenant()->id)
                    ->readOnly()
                    ->hiddenOn('edit'),

                TextInput::make('used_by')
                    ->label('Utilisé par l\'utilisateur ID')
                    ->numeric()
                    ->readOnly()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                CopyableTextColumn::make('code')
                    ->copyMessage('Code d\'invitation copié')
                    ->label('Code'),
                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime(),
                TextColumn::make('user.name')
                    ->label('Utilisé par')
                    ->default('Non utilisé'),
                TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->default('Inconnu'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                CopyAction::make('copyCode')
                    ->label('Copier le code')
                    ->copyable(fn ($record) => $record->code)
                    ->icon('heroicon-o-clipboard-document')
                    ->successNotificationTitle('Code copié!'),
                CopyAction::make('copyLink')
                    ->label('Copier le lien')
                    ->copyable(fn ($record) => route('invite', ['code' => $record->code]))
                    ->icon('heroicon-o-link')
                    ->successNotificationTitle('Lien copié!'),
                DeleteAction::make()
                    ->label('Supprimer'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune invitation trouvée');
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
            'edit' => EditTenantInvite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', filament()->getTenant()->id);
    }
}
