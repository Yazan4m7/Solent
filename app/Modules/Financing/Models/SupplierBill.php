<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id', 'amount', 'paid_amount', 'due_date', 'status',
        'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierBillPayment::class, 'bill_id');
    }

    public function creator()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'created_by');
    }

    public function recalculateStatus()
    {
        $paid = (float) $this->payments()->sum('amount');
        $this->paid_amount = $paid;

        if ($paid <= 0) {
            $this->status = 'unpaid';
        } elseif ($paid + 0.0001 >= (float) $this->amount) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->due_date && $this->due_date->isPast();
    }
}
