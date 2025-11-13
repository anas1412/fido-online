<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Traits\HasFiscalYearScope; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFiscalYearScope; // Add this line

    protected $fillable = [
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'status',
        'total_amount',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
