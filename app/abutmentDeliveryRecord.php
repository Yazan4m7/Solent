<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class abutmentDeliveryRecord extends Model
{
    use SoftDeletes;

    protected $table = 'abutments_delivery';
    protected $fillable =['case_id'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function abutment()
    {
        return $this->belongsTo('App\abutment', 'abutment_id', 'id');
    }
    public function job()
    {
        return $this->belongsTo('App\job', 'job_id', 'id');
    }
    public function implant()
    {
        return $this->belongsTo('App\implant', 'implant_id', 'id');
    }
    public function orderedBy()
    {
        return $this->belongsTo('App\User', 'ordered_by', 'id');
    }

    public function case()
    {
        return $this->belongsTo('App\sCase', 'case_id', 'id');
    }
    public function logs()
    {
        return $this->hasMany('App\abutmentReceiveLogs', 'abut_delivery_id', 'id');
    }
}
