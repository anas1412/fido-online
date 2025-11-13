<?php

namespace App\Filament\Dashboard\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification; // Add this line
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
                Select::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->nullable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $data['tenant_id'] = filament()->getTenant()->id;
                        $category = Category::create($data);

                        Notification::make()
                            ->title('Catégorie créée')
                            ->success()
                            ->send();

                        return $category->getKey();
                    }),
            ]);
    }
}
