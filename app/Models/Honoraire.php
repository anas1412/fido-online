<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\Traits\HasFiscalYearScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\HonoraireNumberService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Honoraire extends Model
{
    use HasFiscalYearScope, SoftDeletes;

    protected $fillable = [
        'client_id',
        'honoraire_number',
        'object',
        'amount_ht',
        'amount_ttc',
        'tva_rate',
        'rs_rate',
        'tf_rate',
        'total_amount',
        'issue_date',
    ];

    protected $casts = [
        'exonere_tf' => 'boolean',
        'exonere_rs' => 'boolean',
        'exonere_tva' => 'boolean',
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
                $honoraire->issue_date = now()->toDateString();
            }
            if (empty($honoraire->honoraire_number)) {
                $honoraire->honoraire_number = (new HonoraireNumberService())->generate($honoraire->tenant, \Carbon\Carbon::parse($honoraire->issue_date));
            }
        });
    }


}
