<?php

namespace App\Models\Traits;

use App\Services\FiscalYearService;
use Illuminate\Database\Eloquent\Builder;

trait HasFiscalYearScope
{
    /**
     * Scope a query to only include records for a given fiscal year.
     *
     * @param Builder $query
     * @param int $fiscalYear
     * @param string $dateColumn The column to use for fiscal year calculation (e.g., 'issue_date', 'created_at').
     * @return Builder
     */
    public function scopeFiscalYear(Builder $query, int $fiscalYear, string $dateColumn = 'created_at'): Builder
    {
        return $query->whereYear($dateColumn, $fiscalYear);
    }
}
