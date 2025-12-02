<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MobileNotificationToken extends Model{
   // use SoftDeletes;
protected $fillable = ['device_id','client_id','token' , 'is_clinic'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    protected $table = 'mobile_notifications_tokens';
    public $timestamps = false;
    public function client()
    {
        return $this->belongsTo('App\client', 'doctor_id', 'id');
    }


}
