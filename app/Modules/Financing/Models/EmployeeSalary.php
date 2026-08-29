<?php

namespace App\Modules\Financing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'base_salary'];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(config('modules.financing.models.user'), 'user_id');
    }
}
