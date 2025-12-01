<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model implements HasName, HasAvatar
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'type', 
        'plan',
        'currency',
        // Legal & Contact
        'matricule_fiscal',
        'registre_commerce',
        'address',
        'city',
        'zip_code',
        'email',
        'phone',
        'website',
        'logo_path',
        // Banking
        'bank_name',
        'rib',
    ];

    // --- Relationships ---
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(TenantUserPivot::class) // Use Pivot
            ->withPivot('is_owner', 'is_mod')
            ->withTimestamps();
    }

    public function honoraires(): HasMany { return $this->hasMany(Honoraire::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function debits(): HasMany { return $this->hasMany(Debit::class); }
    public function clients(): HasMany { return $this->hasMany(Client::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function categories(): HasMany { return $this->hasMany(Category::class); }

    // --- Filament Methods ---

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    // --- Logic Helpers ---

    public function usesInvoices(): bool
    {
        return $this->type === 'commercial';
    }

    public function usesHonoraires(): bool
    {
        return in_array($this->type, ['accounting', 'medical']);
    }

    public function getDefaultTvaRate(): float
    {
        return match ($this->type) {
            'medical' => 7.00,
            default => 19.00,
        };
    }

    public function isPro(): bool
    {
        // TODO: Create a migration to add $table->string('plan')->default('free');
        // For now, we return false (Free Tier) by default.
        // You can test the UI by changing this to: return true;
        return $this->getAttribute('plan') === 'pro';
    }

    public function getPlanLabel(): string
    {
        return $this->isPro() ? 'PRO' : 'Gratuit';
    }
}