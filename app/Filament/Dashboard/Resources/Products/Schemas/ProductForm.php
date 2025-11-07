<?php

namespace App\Filament\Dashboard\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('unit_price')
                    ->label('Prix Unitaire')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('current_stock')
                    ->label('Stock Actuel')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('category')
                    ->label('Catégorie'),
            ]);
    }
}
