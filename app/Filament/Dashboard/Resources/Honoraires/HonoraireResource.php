<?php

namespace App\Filament\Dashboard\Resources\Honoraires;

use App\Filament\Dashboard\Resources\Honoraires\Pages\CreateHonoraire;
use App\Filament\Dashboard\Resources\Honoraires\Pages\EditHonoraire;
use App\Filament\Dashboard\Resources\Honoraires\Pages\ViewHonoraire;
use App\Filament\Dashboard\Resources\Honoraires\Pages\ListHonoraires;
use App\Filament\Dashboard\Resources\Honoraires\Schemas\HonoraireForm;
use App\Filament\Dashboard\Resources\Honoraires\Tables\HonorairesTable;
use App\Filament\Dashboard\Resources\Honoraires\Schemas\HonoraireInfolist;
use App\Models\Honoraire;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HonoraireResource extends Resource
{
    protected static ?string $model = Honoraire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'honoraire_number';

    protected static ?string $pluralModelLabel = 'Notes d\'Honoraires';

    protected static ?string $navigationLabel = 'Notes d\'Honoraires';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function canViewAny(): bool
    {
        $tenant = filament()->getTenant();
        return $tenant && $tenant->usesHonoraires();
    }

    public static function form(Schema $schema): Schema
    {
        return HonoraireForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HonoraireInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HonorairesTable::configure($table);
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
            'index' => ListHonoraires::route('/'),
            'create' => CreateHonoraire::route('/create'),
            'view' => ViewHonoraire::route('/{record}'),
            'edit' => EditHonoraire::route('/{record}/edit'),
        ];
    }
}
