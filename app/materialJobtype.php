<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class materialJobtype extends Model
{
    use SoftDeletes;
    public function jobTypes(){
        return $this->hasMany('App\materialJobType', 'jobtype_id', 'material_id');
    }
}
