<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class signinLog extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'mobile_signin_logs';
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
}