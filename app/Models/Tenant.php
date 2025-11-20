<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\HasName;

class Tenant extends Model implements HasName
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'currency'];

    // --- Relationships ---
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_owner', 'is_mod');
    }

    public function honoraires(): HasMany
    {
        return $this->hasMany(Honoraire::class);
    }
    
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    // --- Logic Helper Methods ---

    /** 
     * Who uses "Factures"? 
     * Devs, Freelancers, Shops
     */
    public function usesInvoices(): bool
    {
        return $this->type === 'commercial';
    }

    /** 
     * Who uses "Honoraires"?
     * Accountants, Auditors, Doctors
     */
    public function usesHonoraires(): bool
    {
        return in_array($this->type, ['accounting', 'medical']);
    }

    /**
     * Get the standard TVA rate for this tenant type
     */
    public function getDefaultTvaRate(): float
    {
        return match ($this->type) {
            'medical' => 7.00,
            default => 19.00,
        };
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }
}