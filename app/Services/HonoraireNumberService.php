<?php

namespace App\Services;

use App\Models\Honoraire;
use App\Models\Tenant;
use Carbon\Carbon;

class HonoraireNumberService
{
    public function generate(Tenant $tenant, Carbon $issueDate): string
    {
        $fiscalYear = (new FiscalYearService())->getFiscalYear($issueDate);
        
        $latestHonoraire = $tenant->honoraires()
            ->whereYear('issue_date', $fiscalYear)
            ->latest('honoraire_number')
            ->first();

        $sequence = 1;
        if ($latestHonoraire) {
            // Assuming honoraire_number format is similar to invoice_number (e.g., YYXXXX)
            // where YY is sequence and XXXX is year. Adjust if format is different.
            $lastSequence = (int) substr($latestHonoraire->honoraire_number, 0, 2);
            $sequence = $lastSequence + 1;
        }

        return sprintf('%02d%d', $sequence, $fiscalYear);
    }
}
