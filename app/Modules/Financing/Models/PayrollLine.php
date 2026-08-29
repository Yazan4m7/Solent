<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollLine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'run_id', 'user_id', 'base_salary', 'bonus', 'deductions', 'net', 'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net' => 'decimal:2',
    ];

    public function run()
    {
        return $this->belongsTo(PayrollRun::class, 'run_id');
    }

    public function user()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'user_id');
    }

    public function calculateNet()
    {
        return max(0, (float) $this->base_salary + (float) $this->bonus - (float) $this->deductions);
    }
}
