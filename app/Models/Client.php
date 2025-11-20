<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Honoraire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes; // Use SoftDeletes

    protected $fillable = [
        'tenant_id',
        'type', // 'company' or 'individual'
        'name',
        'matricule_fiscal', // B2B Requirement
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'zip_code',
        'notes',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function honoraires(): HasMany
    {
        return $this->hasMany(Honoraire::class);
    }
}