<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'context' => 'array',
        'branding' => 'array',
        'activated_at' => 'datetime',
        'failed_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function getConnectionName()
    {
        return config('tenancy.landlord_connection', 'landlord');
    }

    public function domains()
    {
        return $this->hasMany(TenantDomain::class);
    }

    public function primaryDomain()
    {
        return $this->hasOne(TenantDomain::class)->where('is_primary', true);
    }

    public function provisioningEvents()
    {
        return $this->hasMany(TenantProvisioningEvent::class);
    }
}
