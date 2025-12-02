<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class abutmentReceiveLogs extends Model
{
    use SoftDeletes;
    protected $table = 'abutments_receive_logs';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function abutmentDeliveryRecrod()
    {
        return $this->belongsTo('App\abutmentDeliveryRecord', 'abut_delivery_id', 'id');
    }
    public function by()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
