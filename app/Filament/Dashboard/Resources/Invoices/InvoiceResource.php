<?php

namespace App\Filament\Dashboard\Resources\Invoices;

use App\Filament\Dashboard\Resources\Invoices\RelationManagers\OtherClientInvoicesRelationManager;
use App\Filament\Dashboard\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Dashboard\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Dashboard\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Dashboard\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Dashboard\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Dashboard\Resources\Invoices\Schemas\InvoiceInfolist;
use App\Filament\Dashboard\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $navigationLabel = 'Factures';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function canViewAny(): bool
    {
        $tenant = filament()->getTenant();
        return $tenant && $tenant->type === 'commercial';
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OtherClientInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
