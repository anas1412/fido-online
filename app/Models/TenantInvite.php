<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantInvite extends Model
{
    protected $fillable = ['tenant_id', 'code', 'used_by'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
