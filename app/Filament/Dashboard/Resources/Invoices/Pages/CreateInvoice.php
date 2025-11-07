<?php

namespace App\Filament\Dashboard\Resources\Invoices\Pages;

use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
