<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $cause_object
 * @property mixed $case
 */
class failureLog extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function case(){
        return $this->belongsTo('App\sCase', 'case_id', 'id');
    }
    public function causeObject(){
        return $this->belongsTo('App\failureCause', 'cause_id', 'id');
    }
}
