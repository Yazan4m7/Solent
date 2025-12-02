<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class tag extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);


    }


}
