<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRun extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'period_month', 'period_year', 'notes', 'total', 'posted_at', 'created_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(PayrollLine::class, 'run_id');
    }

    public function creator()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'created_by');
    }

    public function getIsPostedAttribute()
    {
        return ! is_null($this->posted_at);
    }
}
