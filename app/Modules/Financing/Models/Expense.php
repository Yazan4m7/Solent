<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'account_id', 'amount', 'description', 'date',
        'receipt_path', 'is_recurring', 'recurring_day', 'recurring_parent_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'created_by');
    }

    public function recurringParent()
    {
        return $this->belongsTo(self::class, 'recurring_parent_id');
    }

    public function recurringOccurrences()
    {
        return $this->hasMany(self::class, 'recurring_parent_id');
    }
}
