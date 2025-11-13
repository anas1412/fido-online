<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\HasName;

class Tenant extends Model implements HasName
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'currency'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_owner', 'is_mod');
    }

    public function getFilamentName(): string
    {
        $dbType = $this->getAttribute('type');

        $typeLabel = match($dbType) {
            'commercial' => 'Société Commerciale',
            'accounting' => 'Société Comptabilité',
            default => $dbType,
        };

        return "{$this->name} {$typeLabel}";
    }

    /* public function getFilamentName(): string
    {
        return "{$this->name} {$this->subscription_plan}";
    } */
    
}