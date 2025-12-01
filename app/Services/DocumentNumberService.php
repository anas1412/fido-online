<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Honoraire;
use App\Models\Debit;
use Carbon\Carbon;
use Exception;

class DocumentNumberService
{
    public function generate(Tenant $tenant, Carbon $issueDate, string $modelClass): string
    {
        // 1. Define configuration for each model type
        $config = match ($modelClass) {
            Invoice::class => [
                'relation' => 'invoices',
                'column'   => 'invoice_number'
            ],
            Honoraire::class => [
                'relation' => 'honoraires',
                'column'   => 'honoraire_number'
            ],
            Debit::class => [
                'relation' => 'debits',
                'column'   => 'debit_number'
            ],
            default => throw new Exception("Type de document non supporté : $modelClass"),
        };

        $relation = $config['relation'];
        $column = $config['column'];
        
        // 2. Get Fiscal Year
        $fiscalYear = (new FiscalYearService())->getFiscalYear($issueDate);
        
        // 3. Query the specific relationship dynamically
        $latestRecord = $tenant->$relation()
            ->whereYear('issue_date', $fiscalYear)
            ->withTrashed() // Important: Include deleted records so numbers aren't reused
            ->latest($column)
            ->first();

        // 4. Calculate Sequence
        $sequence = 1;
        
        if ($latestRecord) {
            // Get the actual number string from the record
            $currentNumber = $latestRecord->$column;
            
            // Extract the first 2 digits (Sequence)
            $lastSequence = (int) substr($currentNumber, 0, 2);
            $sequence = $lastSequence + 1;
        }

        // 5. Return formatted string (Sequence + Year) e.g., "052025"
        return sprintf('%02d%d', $sequence, $fiscalYear);
    }
}