<?php

namespace App\Filament\Dashboard\Resources\Invoices;

// ... keep your existing imports
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
use Illuminate\Database\Eloquent\Model; // Add this import

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    // You can keep this as the default fallback, but getGlobalSearchAttributes overrides logic
    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $navigationLabel = 'Factures';

    protected static UnitEnum|string|null $navigationGroup = 'Gestion Commerciale';

    protected static ?int $navigationSort = 2;
    
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

    public static function getGlobalSearchAttributes(): array
    {
        return [
            'invoice_number',
            'client.name',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->invoice_number . ' • ' . $record->client?->name; 
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Client' => $record->client?->name,
            'Date' => $record->issue_date,
        ];
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