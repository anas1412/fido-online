<?php

namespace App\Filament\Dashboard\Resources\Categories\Pages;

use App\Filament\Dashboard\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $title = 'Gérer les catégories';

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tenant_id'] = filament()->getTenant()->id;

                    return $data;
                }),
        ];
    }
}
