<?php

namespace App\Filament\Dashboard\Resources\Honoraires\Pages;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHonoraire extends CreateRecord
{
    protected static string $resource = HonoraireResource::class;

    protected static bool $canCreateAnother = false;
}
