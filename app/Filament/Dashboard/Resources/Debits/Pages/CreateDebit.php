<?php

namespace App\Filament\Dashboard\Resources\Debits\Pages;

use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDebit extends CreateRecord
{
    protected static string $resource = DebitResource::class;
}
