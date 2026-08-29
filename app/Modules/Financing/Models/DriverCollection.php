<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverCollection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'invoice_id', 'collected_amount', 'submitted_amount',
        'submitted_at', 'account_id', 'notes',
    ];

    protected $casts = [
        'collected_amount' => 'decimal:2',
        'submitted_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'user_id');
    }

    public function invoice()
    {
        return $this->belongsTo(config('modules.financing.models.invoice'), 'invoice_id');
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }

    public function getOutstandingGapAttribute()
    {
        return max(0, (float) $this->collected_amount - (float) $this->submitted_amount);
    }

    public function getIsFullySubmittedAttribute()
    {
        return $this->outstanding_gap <= 0.0001;
    }
}
