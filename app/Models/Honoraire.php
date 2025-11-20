<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\HonoraireNumberService;
use App\Models\Setting;

class Honoraire extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'honoraire_number',
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
        
        // Casts for calculations
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
        static::creating(function ($honoraire) {
            if (empty($honoraire->issue_date)) {
                $honoraire->issue_date = now();
            }
            // Generate Number if missing
            if (empty($honoraire->honoraire_number) && $honoraire->tenant) {
                $honoraire->honoraire_number = (new HonoraireNumberService())
                    ->generate($honoraire->tenant, $honoraire->issue_date);
            }
            
            // Force calculation on backend to ensure DB data integrity
            $honoraire->calculateTaxes();
        });

        static::updating(function ($honoraire) {
            $honoraire->calculateTaxes();
        });
    }

    public function calculateTaxes()
    {
        $settings = Setting::singleton();
        
        // Rates
        $this->tva_rate = $this->exonere_tva ? 0 : ($this->tenant?->getDefaultTvaRate() ?? 19.0);
        $this->rs_rate  = $this->exonere_rs  ? 0 : ($settings->rs_rate ?? 3.0);
        $this->tf_value = $this->exonere_tf  ? 0 : ($settings->tf_rate ?? 1.000);

        // Amounts
        $ht = (float) $this->amount_ht;
        $this->tva_amount = $ht * ($this->tva_rate / 100);
        $this->amount_ttc = $ht + $this->tva_amount + (float) $this->tf_value;
        
        // RS Calculation (Usually on TTC)
        $this->rs_amount = $this->amount_ttc * ($this->rs_rate / 100);
        $this->net_to_pay = $this->amount_ttc - $this->rs_amount;
    }
}