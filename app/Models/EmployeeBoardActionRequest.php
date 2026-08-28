<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBoardActionRequest extends Model
{
    protected $table = 'employee_board_action_requests';

    protected $fillable = [
        'request_key',
        'user_id',
        'action',
        'case_id',
        'stage',
        'payload_hash',
        'response_payload',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'case_id' => 'integer',
        'stage' => 'integer',
        'response_payload' => 'array',
        'completed_at' => 'datetime',
    ];
}
