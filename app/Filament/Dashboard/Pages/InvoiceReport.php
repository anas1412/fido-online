<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Invoice;
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
use Filament\Actions\Action;
use App\Filament\Dashboard\Resources\Invoices\InvoiceResource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use UnitEnum;
use BackedEnum;

class InvoiceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Gesttion des Rapports';

    protected static ?string $navigationLabel = 'Rapport Factures';

    protected static ?string $title = 'Rapport des Factures';
    
    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.dashboard.pages.invoice-report';
   

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimer Rapport')
                /* ->url(fn () => route('print.invoices', [
                    'from' => $this->tableFilters['date_range']['from'] ?? null,
                    'until' => $this->tableFilters['date_range']['until'] ?? null,
                ]), shouldOpenInNewTab: true) */
                ->icon('heroicon-o-printer'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('facture')
            ->pluralModelLabel('factures')
            ->query(fn (): Builder => Invoice::query()->latest('issue_date'))
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')->label('Numéro')->searchable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),

                Tables\Columns\TextColumn::make('amount_ht')
                    ->label('Total HT')->money('TND')
                    ->summarize(Sum::make()->label('Total HT')->money('TND')),

                Tables\Columns\TextColumn::make('tva_amount')
                    ->label('TVA')->money('TND')
                    ->summarize(Sum::make()->label('Total TVA')->money('TND'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tf_value')
                    ->label('Timbre')->money('TND')
                    ->summarize(Sum::make()->money('TND'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('amount_ttc')
                    ->label('Total TTC')->money('TND')->weight('bold')
                    ->summarize(Sum::make()->label('Total TTC')->money('TND')),

                // RS is usually less common on Invoices than Honoraires, but still happens
                Tables\Columns\TextColumn::make('rs_amount')
                    ->label('Retenue (RS)')->money('TND')->color('danger')
                    ->summarize(Sum::make()->label('Total RS')->money('TND')),

                Tables\Columns\TextColumn::make('net_to_pay')
                    ->label('Net à Payer')->money('TND')->color('success')->weight('bold')
                    ->summarize(Sum::make()->label('Total Net')->money('TND')),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([DatePicker::make('from')->label('Du'), DatePicker::make('until')->label('Au')])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('issue_date', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('issue_date', '<=', $d))
                    ),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function canAccess(): bool
    {
        return filament()->getTenant()?->usesInvoices() ?? false;
    }
    
}