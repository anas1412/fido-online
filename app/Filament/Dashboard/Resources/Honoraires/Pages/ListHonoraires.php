<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Pages;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHonoraires extends ListRecords
{
    protected static string $resource = HonoraireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
