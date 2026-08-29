<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'balance', 'currency', 'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(FinanceAccountTransaction::class, 'account_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'account_id');
    }
}
