<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancePaymentAccount extends Model
{
    use SoftDeletes;

    protected $fillable = ['payment_id', 'account_id'];

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }

    public function payment()
    {
        return $this->belongsTo(config('modules.financing.models.payment'), 'payment_id');
    }
}
