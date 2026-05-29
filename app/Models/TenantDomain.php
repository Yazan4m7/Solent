<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantDomain extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function getConnectionName()
    {
        return config('tenancy.landlord_connection', 'landlord');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
