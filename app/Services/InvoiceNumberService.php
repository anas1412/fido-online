<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use Carbon\Carbon;

class InvoiceNumberService
{
    public function generate(Tenant $tenant, Carbon $issueDate): string
    {
        $fiscalYear = (new FiscalYearService())->getFiscalYear($issueDate);
        
        $latestInvoice = $tenant->invoices()
            ->whereYear('issue_date', $fiscalYear)
            ->withTrashed()
            ->latest('invoice_number')
            ->first();

        $sequence = 1;
        if ($latestInvoice) {
            $lastSequence = (int) substr($latestInvoice->invoice_number, 0, 2);
            $sequence = $lastSequence + 1;
        }

        return sprintf('%02d%d', $sequence, $fiscalYear);
    }
}
