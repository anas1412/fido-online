<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Traits\HasFiscalYearScope; // Add this line
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\InvoiceNumberService;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFiscalYearScope, SoftDeletes; // Add this line

    protected $fillable = [
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'status',
        'total_amount',
        'currency',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = (new InvoiceNumberService())->generate($invoice->tenant, Carbon::parse($invoice->issue_date));
            }
        });
    }

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
