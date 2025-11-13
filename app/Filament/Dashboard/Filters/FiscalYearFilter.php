<?php

namespace App\Filament\Dashboard\Filters;

use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class FiscalYearFilter extends SelectFilter
{
    protected string $dateColumn = 'created_at';

    public static function make(?string $name = null): static
    {
        $name = $name ?? 'fiscal_year';
        $currentYear = now()->year;
        $column = 'created_at';

        // Build options for the last 3 years including current
        $options = [];
        for ($year = $currentYear - 2; $year <= $currentYear; $year++) {
            $options[$year] = 'Exercice Fiscal ' . $year;
        }

        return parent::make($name)
            ->label('Exercice Fiscal')
            ->options($options)
            ->default($currentYear)
            ->query(function (Builder $query, array $data) use ($column) {
                if (empty($data['value'])) {
                    return $query;
                }

                return $query->whereYear($column, (int) $data['value']);
            });
    }

    public function forDateColumn(string $column): static
    {
        $this->dateColumn = $column;

        return $this;
    }
}
