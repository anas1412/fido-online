<?php

namespace App\Models;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUserPivot extends Pivot
{
    protected $table = 'tenant_user';
    protected $fillable = ['tenant_id', 'user_id', 'is_owner', 'is_mod'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
