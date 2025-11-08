<?php

namespace App\Filament\Dashboard\Resources\Clients;

use App\Filament\Dashboard\Resources\Clients\RelationManagers\InvoicesRelationManager;
use App\Filament\Dashboard\Resources\Clients\Pages\CreateClient;
use App\Filament\Dashboard\Resources\Clients\Pages\EditClient;
use App\Filament\Dashboard\Resources\Clients\Pages\ListClients;
use App\Filament\Dashboard\Resources\Clients\Pages\ViewClient;
use App\Filament\Dashboard\Resources\Clients\Schemas\ClientForm;
use App\Filament\Dashboard\Resources\Clients\Schemas\ClientInfolist;
use App\Filament\Dashboard\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $navigationLabel = 'Clients';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
