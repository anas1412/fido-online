<?php

namespace App\Filament\Dashboard\Resources\Categories;

use App\Filament\Dashboard\Resources\Categories\Pages\ManageCategories;
use App\Filament\Dashboard\Resources\Categories\Pages\ViewCategory;
use App\Filament\Dashboard\Resources\Categories\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $pluralModelLabel = 'Catégories';
    protected static ?string $navigationLabel = 'Catégories';
    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 4;
    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function canViewAny(): bool
    {
        $tenant = filament()->getTenant();
        return $tenant && $tenant->type === 'commercial';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom de la catégorie')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom de la catégorie')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
            'view' => ViewCategory::route('/{record}'),
        ];
    }
}
