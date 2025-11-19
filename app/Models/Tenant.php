<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\HasName;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model implements HasName
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'currency'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_owner', 'is_mod');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function honoraires() : HasMany
    {
        return $this->hasMany(Honoraire::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function categories(): HasMany  
    { 
        return $this->hasMany(Category::class); 
    
    }
    public function products(): HasMany
    { 
        return $this->hasMany(Product::class); 
    }


    public function getFilamentName(): string
    {
        return "{$this->name} {$this->subscription_plan}";
    }
    
}