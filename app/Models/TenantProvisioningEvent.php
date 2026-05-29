<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantProvisioningEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
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
