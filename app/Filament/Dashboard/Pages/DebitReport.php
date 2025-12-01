<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Debit;
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
use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use UnitEnum;
use BackedEnum;
use Filament\Actions\Action; 

class DebitReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;
    protected static UnitEnum|string|null $navigationGroup = 'Gesttion des Rapports';
    protected static ?string $navigationLabel = 'Rapport Débits';
    protected static ?string $title = 'Rapport des Notes de Débit';
    protected static ?int $navigationSort = 13;
    protected string $view = 'filament.dashboard.pages.debit-report';

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
            ->query(fn (): Builder => Debit::query()->latest('issue_date'))
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('debit_number')->label('Numéro')->searchable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('amount_ht')->label('Honoraires HT')->money('TND')->summarize(Sum::make()->money('TND')),
                Tables\Columns\TextColumn::make('debours_amount')->label('Débours')->money('TND')->color('warning')->summarize(Sum::make()->label('Total Débours')->money('TND')),
                Tables\Columns\TextColumn::make('amount_ttc')->label('Total TTC')->money('TND')->weight('bold')->summarize(Sum::make()->money('TND')),
                Tables\Columns\TextColumn::make('net_to_pay')->label('Net à Payer')->money('TND')->color('success')->weight('bold')->summarize(Sum::make()->label('Total Net')->money('TND')),
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
                    ->url(fn (Debit $record): string => DebitResource::getUrl('view', ['record' => $record])),
            ]);
    }
}