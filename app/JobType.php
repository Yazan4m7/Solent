<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobType extends Model
{
    use SoftDeletes;

    public function materials(){
        return $this->hasMany('App\materialJobtype', 'jobtype_id', 'id');
    }
}
