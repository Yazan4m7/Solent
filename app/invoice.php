<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class invoice extends Model
{
    use SoftDeletes;

    public function case(){
        return $this->belongsTo('App\sCase', 'case_id', 'id');
    }

    public function client(){
        return $this->belongsTo('App\client', 'doctor_id', 'id');
    }
    public function discount()
    {
        return $this->belongsTo('App\discount', 'case_id', 'case_id');
    }
}
