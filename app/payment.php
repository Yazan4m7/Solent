<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class payment extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function client()
    {
        return $this->belongsTo('App\client', 'doctor_id', 'id');
    }
    public function collectorUserRecord()
    {
        return $this->belongsTo('App\User', 'collector', 'id');
    }
    public function bank()
    {
        return $this->belongsTo('App\bank', 'from_bank', 'id');
    }
    public function receivedBy()
    {
        return $this->belongsTo('App\User', 'received_by', 'id');
    }
    public function collectorFullName()
    {
        $collector = $this->collectorUserRecord;
        if($collector)
        return $collector->first_name .' '. $collector->last_name;
        else
        return "None";
    }
    public function receiverFullName()
    {
        $receiver = $this->receivedBy;
        if($receiver)
            return $receiver->first_name .' '. $receiver->last_name;
        else
            return "None";
    }
    public function isCollected()
    {
        if($this->recieved_on != null)
            return true;
        else
            return false;
    }
}
