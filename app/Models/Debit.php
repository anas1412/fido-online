<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\DocumentNumberService;
use App\Models\Setting;

class Debit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'debit_number',
        'object',
        'issue_date',
        'amount_ht',
        'tva_amount',
        'rs_amount',
        'amount_ttc',
        'net_to_pay',
        'tva_rate',
        'rs_rate',
        'tf_value',
        'exonere_tva',
        'exonere_rs',
        'exonere_tf',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'exonere_tva' => 'boolean',
        'exonere_rs' => 'boolean',
        'exonere_tf' => 'boolean',
        'amount_ht' => 'decimal:3',
        'tva_amount' => 'decimal:3',
        'rs_amount' => 'decimal:3',
        'amount_ttc' => 'decimal:3',
        'net_to_pay' => 'decimal:3',
        'tva_rate' => 'float',
        'rs_rate' => 'float',
        'tf_value' => 'decimal:3',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted()
    {
        static::creating(function ($debit) {
            if (empty($debit->issue_date)) {
                $debit->issue_date = now();
            }

            if (empty($debit->debit_number) && $debit->tenant) {
                $debit->debit_number = (new DocumentNumberService())
                    ->generate($debit->tenant, $debit->issue_date, self::class);
            }
            
            $debit->calculateTaxes();
        });

        static::updating(function ($debit) {
            $debit->calculateTaxes();
        });
    }

    public function calculateTaxes()
    {
        $settings = Setting::singleton();
        
        $this->tva_rate = $this->exonere_tva ? 0 : ($this->tenant?->getDefaultTvaRate() ?? 19.0);
        $this->rs_rate  = $this->exonere_rs  ? 0 : ($settings->rs_rate ?? 3.0);
        $this->tf_value = $this->exonere_tf  ? 0 : ($settings->tf_rate ?? 1.000);

        $ht = (float) $this->amount_ht;
        
        $this->tva_amount = $ht * ($this->tva_rate / 100);
        $this->amount_ttc = $ht + $this->tva_amount + (float) $this->tf_value;
        $this->rs_amount = $this->amount_ttc * ($this->rs_rate / 100);
        $this->net_to_pay = $this->amount_ttc - $this->rs_amount;
    }
}