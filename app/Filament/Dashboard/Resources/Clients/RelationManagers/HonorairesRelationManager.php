<?php

namespace App\Filament\Dashboard\Resources\Clients\RelationManagers;

use App\Filament\Dashboard\Resources\Honoraires\HonoraireResource;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HonorairesRelationManager extends RelationManager
{
    protected static string $relationship = 'honoraires';

    protected static ?string $relatedResource = HonoraireResource::class;

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedDocumentText;

    protected static ?string $title = 'Notes d\'Honoraires';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('honoraire_number')
            ->columns([               
                TextColumn::make('honoraire_number')
                    ->label('Numéro')
                    ->searchable(),

                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('amount_ht')
                    ->label('HT')
                    ->money(fn ($record) => $record->currency ?? 'TND')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tf_value') 
                    ->label('Timbre')
                    ->money(fn ($record) => $record->currency ?? 'TND')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('net_to_pay')
                    ->label('Net à Payer')
                    ->money(fn ($record) => $record->currency ?? 'TND') 
                    ->weight('bold')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn () => HonoraireResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])->bulkActions([
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