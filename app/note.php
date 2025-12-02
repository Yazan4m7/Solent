<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class note extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function writtenBy(){
        return $this->belongsTo('App\User', 'written_by', 'id');
    }
}
