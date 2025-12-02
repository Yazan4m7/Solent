<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandSetting extends Model
{
    protected $fillable = [
        'tenant',
        'name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'copy',
        'extra',
    ];

    protected $casts = [
        'copy' => 'array',
        'extra' => 'array',
    ];
}
