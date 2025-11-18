<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'tva_rate',
        'tva_reduced_rate',
        'rs_rate',
        'tf_rate',
        'site_name',
        'support_email',
        'support_phone',
        'about_content',
        'legal_content',
        'privacy_content',
    ];

    public static function singleton(): self
    {
        return static::firstOrCreate([]);
    }
}
