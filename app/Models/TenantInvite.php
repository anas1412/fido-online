<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantInvite extends Model
{
    protected $fillable = ['tenant_id', 'code', 'used_by'];
}
