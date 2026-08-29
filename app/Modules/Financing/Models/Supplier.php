<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'payment_terms_days', 'notes',
    ];

    public function bills()
    {
        return $this->hasMany(SupplierBill::class, 'supplier_id');
    }

    public function getBalanceOwedAttribute()
    {
        return (float) $this->bills()->sum('amount') - (float) $this->bills()->sum('paid_amount');
    }
}
