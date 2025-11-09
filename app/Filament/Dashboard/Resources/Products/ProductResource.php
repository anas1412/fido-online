<?php

namespace App\Filament\Dashboard\Resources\Products;

use App\Filament\Dashboard\Resources\Products\Pages\CreateProduct;
use App\Filament\Dashboard\Resources\Products\Pages\EditProduct;
use App\Filament\Dashboard\Resources\Products\Pages\ListProducts;
use App\Filament\Dashboard\Resources\Products\Pages\ViewProduct;
use App\Filament\Dashboard\Resources\Products\Schemas\ProductForm;
use App\Filament\Dashboard\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Dashboard\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?string $navigationLabel = 'Produits';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function canViewAny(): bool
    {
        $tenant = filament()->getTenant();
        return $tenant && $tenant->type === 'commercial';
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
