<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Debits\DebitResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use App\Models\Debit;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DebitsRelationManager extends RelationManager
{
    protected static string $relationship = 'debits';

    protected static ?string $relatedResource = DebitResource::class;

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedDocumentPlus;

    protected static ?string $title = 'Notes de Débit';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('debit_number')
            ->columns([
                TextColumn::make('debit_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                // Logic for Commercial Tenants (Invoices)
                TextColumn::make('invoice.invoice_number')
                    ->label('Réf. Facture')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-')
                    ->visible(fn () => filament()->getTenant()->usesInvoices()),

                // Logic for Accounting/Medical Tenants (Honoraires)
                TextColumn::make('honoraire.honoraire_number')
                    ->label('Réf. Honoraire')
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->visible(fn () => filament()->getTenant()->usesHonoraires()),

                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money('TND')
                    ->weight('bold')
                    ->color('success'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn () => DebitResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}