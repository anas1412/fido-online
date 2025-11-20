<?php

namespace App\Models;

use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasAvatar; // Import this
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // Import this
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasTenants, HasAvatar
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar_url',
        'phone',
        'is_admin',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    // --- Filament Avatar ---
    public function getFilamentAvatarUrl(): ?string
    {
        // Return Google Avatar or Uploaded Avatar or Null (Gravatar fallback)
        return $this->avatar_url; 
    }

    // --- Relationships ---
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->using(TenantUserPivot::class) // USE THE CUSTOM PIVOT
            ->withPivot('is_owner', 'is_mod')
            ->withTimestamps();
    }

    // --- Helpers ---
    public function isOwnerOfTenant(Tenant $tenant): bool
    {
        return $this->tenants()->where('tenant_id', $tenant->id)->wherePivot('is_owner', true)->exists();
    }

    public function isModeratorOfTenant(Tenant $tenant): bool
    {
        return $this->tenants()->where('tenant_id', $tenant->id)->wherePivot('is_mod', true)->exists();
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenants()->whereKey($tenant)->exists();
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (static::count() === 0) {
                $user->is_admin = true;
            }
        });
    }
}