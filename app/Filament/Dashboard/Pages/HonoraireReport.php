<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Honoraire;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction; 
use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout; // <--- ADD THIS IMPORT
use UnitEnum;
use BackedEnum;
use Filament\Actions\Action;

class HonoraireReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static UnitEnum|string|null $navigationGroup = 'Gesttion des Rapports';
    protected static ?string $navigationLabel = 'Rapport Honoraires';
    protected static ?string $title = 'Rapport des Honoraires';
    protected static ?int $navigationSort = 11;
    protected string $view = 'filament.dashboard.pages.honoraire-report';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimer Rapport (WIP)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('info')
                ->action(fn () => $this->js('window.print()')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('honoraire')
            ->pluralModelLabel('honoraires')
            ->query(fn (): Builder => Honoraire::query()->latest('issue_date'))
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('honoraire_number')->label('Numéro')->searchable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('amount_ht')->label('Total HT')->money('TND')->summarize(Sum::make()->label('Total HT')->money('TND')),
                Tables\Columns\TextColumn::make('tva_amount')->label('TVA')->money('TND')->summarize(Sum::make()->label('Total TVA')->money('TND'))->toggleable(isToggledHiddenByDefault: true), 
                Tables\Columns\TextColumn::make('amount_ttc')->label('Total TTC')->money('TND')->weight('bold')->summarize(Sum::make()->label('Total TTC')->money('TND')),
                Tables\Columns\TextColumn::make('rs_amount')->label('Retenue (RS)')->money('TND')->color('danger')->summarize(Sum::make()->label('Total RS')->money('TND')),
                Tables\Columns\TextColumn::make('net_to_pay')->label('Net Perçu')->money('TND')->color('success')->weight('bold')->summarize(Sum::make()->label('Total Net')->money('TND')),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([DatePicker::make('from')->label('Du'), DatePicker::make('until')->label('Au')])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('issue_date', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('issue_date', '<=', $d))
                    ),
            ], layout: FiltersLayout::AboveContent) // <--- THIS LINE MAKES IT VISIBLE
            ->actions([
                ViewAction::make()
                    ->url(fn (Honoraire $record): string => HonoraireResource::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function canAccess(): bool
    {
        // Only show if the Tenant allows Honoraires (Accounting/Medical)
        return filament()->getTenant()?->usesHonoraires() ?? false;
    }

}