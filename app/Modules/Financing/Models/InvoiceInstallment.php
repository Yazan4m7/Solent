<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceInstallment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id', 'client_id', 'amount', 'due_date', 'paid_at', 'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(config('modules.financing.models.invoice'), 'invoice_id');
    }

    public function client()
    {
        return $this->belongsTo(config('modules.financing.models.client'), 'client_id');
    }

    public function payment()
    {
        return $this->belongsTo(config('modules.financing.models.payment'), 'payment_id');
    }

    public function getIsOverdueAttribute()
    {
        return ! $this->paid_at && $this->due_date && $this->due_date->isPast();
    }
}
