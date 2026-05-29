<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class job extends Model
{

    use SoftDeletes;


    protected $guarded = ['id'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public function case()
    {
        return $this->belongsTo('App\sCase', 'case_id', 'id');
    }
    public function material()
{
    return $this->belongsTo('App\material', 'material_id', 'id');
}
    public function jobType()
    {
        return $this->belongsTo('App\JobType', 'type', 'id');
    }
    public function abutmentR()
    {
        return $this->belongsTo('App\abutment', 'abutment', 'id');
    }
    public function implantR()
    {
        return $this->belongsTo('App\implant', 'implant', 'id');
    }
    public function abutmentDelivery()
    {
        return $this->hasMany('App\abutmentDeliveryRecord', 'job_id', 'id');
    }
    public function assignedTo()
    {
        return $this->belongsTo('App\User', 'assignee', 'id');
    }
    public function originalJob()
    {
        return $this->belongsTo('App\Job', 'original_job_id', 'id');
    }
    public function status()
    {

        $stageName = "";

        switch ($this->stage) {
            case "1":
                $stageName =  "Design";
                break;
            case "2":
                $stageName =  "Milling";
                break;
            case "3":
                $stageName =  "3D Printing ";
                break;
            case "4":
                $stageName =   "Sintering Furnace";
                break;
            case "5":
                $stageName =   "Pressing Furnace";
                break;
            case "9":
                $stageName =   "Metal Work";
                break;
            case "6":
                $stageName =   "Finishing";
                break;
            case "7":
                $stageName =   "Quality Control";
                break;
            case "8":
                $stageName =   "Delivery";break;
            case "-1":
                return  "Completed";
                break;
        }

        $assignee = $this->assignedTo;
        if($assignee)
        {
            if($this->stage == 8){
            if($this->delivery_accepted)
                return "Active in ".$stageName . " w/ ". $assignee->name_initials;
                else
            return "Assigned to ". $assignee->name_initials;

            }
            else
            return "Active in ".$stageName . " w/ ". $assignee->name_initials;

        }
        else
            return "Waiting in ".$stageName;
    }
    public function deliveryDriver(){
        return $this->belongsTo('App\User', 'delivery_accepted', 'id');
    }
    public function hasAbutment(){
        if($this->abutmentR != null)
            return true;
        else
            return false;
    }

    public static function countUnitsSet($deviceId, $stageId): int
    {
        return array_reduce(self::where('device_id', $deviceId)
            ->where('stage', $stageId)
            ->where('is_set', 1)
            ->where(function ($q) {
                $q->whereNull('is_active')
                    ->orWhere('is_active', 0);
            })
            ->get()
            ->toArray(), function ($carry, $job) {
            return $carry + count(explode(',', $job['unit_num']));
        }, 0);
    }

    public static function countUnitsActive($deviceId, $stageId): int
    {
        return array_reduce(self::where('device_id', $deviceId)
            ->where('stage', $stageId)
            ->where(function ($q) {
                $q->whereNotNull('is_active')
                    ->where('is_active', '!=', 0);
            })
            ->get()
            ->toArray(), function ($carry, $job) {
            return $carry + count(explode(',', $job['unit_num']));
        }, 0);
    }

    public function build()
    {
        return $this->printingBuild();
    }

    public function millingBuild()
    {
        return $this->belongsTo(Build::class, 'milling_build_id');
    }

    public function printingBuild()
    {
        return $this->belongsTo(Build::class, 'printing_build_id');
    }

    public function pressingBuild()
    {
        return $this->belongsTo(Build::class, 'pressing_build_id');
    }
}
