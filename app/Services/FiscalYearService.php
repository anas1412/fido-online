<?php

namespace App\Services;

use Carbon\Carbon;

class FiscalYearService
{
    /**
     * Get the fiscal year for a given date (which is the calendar year in this case).
     *
     * @param Carbon|null $date
     * @return int
     */
    public function getFiscalYear(Carbon $date = null): int
    {
        return ($date ?? Carbon::now())->year;
    }

    /**
     * Get the start date of a given fiscal year (January 1st).
     *
     * @param int|null $fiscalYear
     * @return Carbon
     */
    public function getFiscalYearStartDate(int $fiscalYear = null): Carbon
    {
        $fiscalYear = $fiscalYear ?? $this->getFiscalYear();
        return Carbon::create($fiscalYear, 1, 1)->startOfDay();
    }

    /**
     * Get the end date of a given fiscal year (December 31st).
     *
     * @param int|null $fiscalYear
     * @return Carbon
     */
    public function getFiscalYearEndDate(int $fiscalYear = null): Carbon
    {
        $fiscalYear = $fiscalYear ?? $this->getFiscalYear();
        return Carbon::create($fiscalYear, 12, 31)->endOfDay();
    }
}
