<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Setting;
use App\Services\DocumentNumberService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'status',
        'currency',

        // Math Fields
        'amount_ht',
        'tva_rate', 'tva_amount',
        'rs_rate', 'rs_amount',
        'tf_value', // Stamp
        'amount_ttc',
        'net_to_pay',

        // Booleans
        'exonere_tva',
        'exonere_rs',
        'exonere_tf',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'exonere_tf' => 'boolean',
        'exonere_rs' => 'boolean',
        'exonere_tva' => 'boolean',
        
        // Math precision
        'amount_ht' => 'decimal:3',
        'tva_amount' => 'decimal:3',
        'rs_amount' => 'decimal:3',
        'amount_ttc' => 'decimal:3',
        'net_to_pay' => 'decimal:3',
        'tva_rate' => 'float',
        'rs_rate' => 'float',
        'tf_value' => 'decimal:3',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }

            if (empty($invoice->invoice_number) && $invoice->tenant) {
                // Pass self::class so the service knows which config to use
                $invoice->invoice_number = (new DocumentNumberService())
                    ->generate($invoice->tenant, $invoice->issue_date, self::class);
            }
            
            $invoice->calculateTaxes();
        });

        static::updating(function ($invoice) {
            $invoice->calculateTaxes();
        });
    }

    public function calculateTaxes()
    {
        $settings = Setting::singleton();
        $tenant = $this->tenant;

        // 1. Rates
        // For Commercial Invoices, default is usually 19% (Tenant::commercial)
        // But we use the Tenant helper we created earlier
        $this->tva_rate = $this->exonere_tva ? 0 : ($tenant?->getDefaultTvaRate() ?? 19.00);
        $this->rs_rate  = $this->exonere_rs  ? 0 : ($settings->rs_rate ?? 0); // RS is often 0 on Invoices unless B2B specific
        $this->tf_value = $this->exonere_tf  ? 0 : ($settings->tf_rate ?? 1.000);

        // 2. Amounts
        $ht = (float) $this->amount_ht; // This must be fed by the items sum
        
        $this->tva_amount = $ht * ($this->tva_rate / 100);
        $this->amount_ttc = $ht + $this->tva_amount + (float) $this->tf_value;
        
        // RS on Invoices is usually on TTC or HT depending on law. 
        // In Tunisia for invoices, RS (1% or 3%) is usually deducted from the TTC.
        $this->rs_amount = $this->amount_ttc * ($this->rs_rate / 100);
        
        $this->net_to_pay = $this->amount_ttc - $this->rs_amount;
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