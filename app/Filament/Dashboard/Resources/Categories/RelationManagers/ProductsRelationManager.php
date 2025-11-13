<?php

namespace App\Filament\Dashboard\Resources\Categories\RelationManagers;

use App\Filament\Dashboard\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';
    protected static ?string $relatedResource = ProductResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required(),
            Textarea::make('description')->label('Description')->columnSpanFull(),
            TextInput::make('sku')->label('SKU'),
            TextInput::make('unit_price')->label('Prix Unitaire')->numeric()->default(0.0)->required(),
            TextInput::make('current_stock')->label('Stock Actuel')->numeric()->default(0)->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nom du produit')->searchable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('unit_price')->label('Prix Unitaire')->numeric()->sortable(),
                TextColumn::make('current_stock')->label('Stock Actuel')->numeric()->sortable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}