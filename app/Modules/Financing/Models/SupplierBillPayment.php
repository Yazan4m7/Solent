<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBillPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bill_id', 'account_id', 'amount', 'date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function bill()
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }
}
