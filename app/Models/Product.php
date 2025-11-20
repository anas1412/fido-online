<?php

namespace App\Models;

use App\Models\Tenant;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'description',
        'sku',
        'unit_price',
        'current_stock',
        'track_stock',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:3',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
