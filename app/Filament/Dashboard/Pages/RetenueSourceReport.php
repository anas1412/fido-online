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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\ViewAction;
use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action; 

class RetenueSourceReport extends Page implements HasTable
{
    use InteractsWithTable;

    // STRICT SYNTAX: Matches HonoraireResource
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScissors;

    // STRICT SYNTAX: Matches HonoraireResource
    protected static UnitEnum|string|null $navigationGroup = 'Gesttion des Rapports';

    protected static ?string $navigationLabel = 'Rapport RS';

    protected static ?string $title = 'Suivi des Retenues à la Source';
    
    protected static ?int $navigationSort = 12;

    // STRICT SYNTAX: Must be non-static in Pages
    protected string $view = 'filament.dashboard.pages.retenue-source-report';

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
            ->modelLabel('Retenue à la Source')
            ->pluralModelLabel('Retenues à la Source')
            ->query(fn (): Builder => Honoraire::query()->where('rs_amount', '>', 0)->latest('issue_date'))
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable()->description(fn (Honoraire $record) => $record->honoraire_number),
                Tables\Columns\TextColumn::make('amount_ttc')->label('Assiette (TTC)')->money('TND'),
                Tables\Columns\TextColumn::make('rs_rate')->label('Taux')->suffix('%')->alignCenter(),
                Tables\Columns\TextColumn::make('rs_amount')->label('Montant Retenu')->money('TND')->weight('bold')->color('danger')->summarize(Sum::make()->label('Total Retenu')->money('TND')),
                Tables\Columns\TextColumn::make('net_to_pay')->label('Net Payé')->money('TND'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([DatePicker::make('from')->label('Date Début'), DatePicker::make('until')->label('Date Fin')])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('issue_date', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('issue_date', '<=', $d))
                    ),
            ], layout: FiltersLayout::AboveContent)
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