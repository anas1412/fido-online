<?php

namespace App\Filament\Dashboard\Resources\Debits;

use App\Filament\Dashboard\Resources\Debits\Pages\CreateDebit;
use App\Filament\Dashboard\Resources\Debits\Pages\EditDebit;
use App\Filament\Dashboard\Resources\Debits\Pages\ListDebits;
use App\Filament\Dashboard\Resources\Debits\Pages\ViewDebit;
use App\Filament\Dashboard\Resources\Debits\Schemas\DebitForm;
use App\Filament\Dashboard\Resources\Debits\Schemas\DebitInfolist;
use App\Filament\Dashboard\Resources\Debits\Tables\DebitsTable;
use App\Models\Debit;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DebitResource extends Resource
{
    protected static ?string $model = Debit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentPlus;

    protected static ?string $recordTitleAttribute = 'debit_number';

    protected static ?string $pluralModelLabel = 'Notes de Débit';

    protected static ?string $navigationLabel = 'Notes de Débit';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return DebitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DebitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DebitsTable::configure($table);
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
            'index' => ListDebits::route('/'),
            'create' => CreateDebit::route('/create'),
            'view' => ViewDebit::route('/{record}'),
            'edit' => EditDebit::route('/{record}/edit'),
        ];
    }

}
