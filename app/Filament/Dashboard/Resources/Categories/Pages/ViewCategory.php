<?php

namespace App\Filament\Dashboard\Resources\Categories\Pages;

use App\Filament\Dashboard\Resources\Categories\CategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getViewContent(): array
    {
        return [
            Section::make('Informations de la catégorie')
                ->description('Détails de cette catégorie')
                ->schema([
                    TextEntry::make('name')->label('Nom de la catégorie'),
                    TextEntry::make('created_at')->label('Créé le')->dateTime(),
                    TextEntry::make('updated_at')->label('Mis à jour le')->dateTime(),
                ]),

            Section::make('Produits liés')
                ->description('Tous les produits appartenant à cette catégorie')
                ->content(fn() => $this->relationManager('products')),
        ];
    }
}
