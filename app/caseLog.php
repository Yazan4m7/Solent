<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class caseLog extends Model
{
    protected $guarded = ['id'];
    use SoftDeletes;
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }



}
