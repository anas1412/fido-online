<?php

namespace App\Filament\Dashboard\Resources\Products\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations générales')
                ->columns(2)
                ->components([
                    TextEntry::make('name')
                        ->label('Nom')
                        ->columnSpan('full'),    // full width
                    TextEntry::make('description')
                        ->label('Description')
                        ->placeholder('-')
                        ->columnSpan('full'),
                    TextEntry::make('sku')
                        ->label('SKU')
                        ->placeholder('-'),
                    TextEntry::make('category.name')
                        ->label('Catégorie')
                        ->placeholder('-'),
                ])->columnSpan('full')->compact(),

            Section::make('Stock et Prix')
                ->columns(2)
                ->components([
                    TextEntry::make('unit_price')
                        ->label('Prix Unitaire')
                        ->numeric(),
                    TextEntry::make('current_stock')
                        ->label('Stock Actuel')
                        ->numeric(),
                ])->columnSpan('full')->compact(),

            Section::make('Timestamps')
                ->columns(2)
                ->components([
                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->label('Mis à jour le')
                        ->dateTime()
                        ->placeholder('-'),
                ])->columnSpan('full')->compact(),
        ]);
    }
}
