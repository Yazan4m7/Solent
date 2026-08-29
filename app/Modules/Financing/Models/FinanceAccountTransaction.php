<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAccountTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id', 'direction', 'amount', 'date', 'description',
        'source_type', 'source_id', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'created_by');
    }

    public function getSignedAmountAttribute()
    {
        return $this->direction === 'inflow'
            ? (float) $this->amount
            : -1 * (float) $this->amount;
    }
}
