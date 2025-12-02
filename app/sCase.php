<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\AbutmentsObserver;
use function PHPSTORM_META\elementType;

class sCase extends Model
{

    use SoftDeletes;
    protected $hidden = ['created_by', 'created_at', 'updated_at', 'created_at','current_status'];

    protected $guarded = ['id'];

    protected $table = 'cases';
    /**
     * Scope a query to only exclude specific Columns.
     *
     * @author Manojkiran.A <manojkiran10031998@gmail.com>
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclude($query, ...$columns)
    {
        if ($columns !== []) {
            if (count($columns) !== count($columns, COUNT_RECURSIVE)) {
                $columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns)));
            }

            return $query->select(array_diff($this->getTableColumns(), $columns));
        }
        return $query;
    }

    /**
     * Shows All the columns of the Corresponding Table of Model
     *
     * @author Manojkiran.A <manojkiran10031998@gmail.com>
     * If You need to get all the Columns of the Model Table.
     * Useful while including the columns in search
     * @return array
     **/
    public function getTableColumns()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('MigrMod:' . filemtime(database_path('migrations')), function () {
            return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        });
    }

    public function jobs()
    {
        return $this->hasMany('App\job', 'case_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany('App\note', 'case_id', 'id');
    }

    public function photos()
    {
        return $this->hasMany('App\file', 'case_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\caseTag', 'case_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo('App\client', 'doctor_id', 'id');
    }
    public function discount()
    {
        return $this->hasOne('App\discount', 'case_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne('App\invoice', 'case_id', 'id');
    }
    public function abutmentsDeliveries()
    {
        return $this->hasMany('App\abutmentDeliveryRecord', 'case_id', 'id');
    }
    public function logs()
    {
        return $this->hasMany('App\caseLog', 'case_id', 'id');
    }
    public function getInitialDeliveryDateAttribute( $value ) {
        return substr($this->attributes['initial_delivery_date'],0,10) .'T' .  substr($this->attributes['initial_delivery_date'],11,5);
    }
    public function initDeliveryDate() {
        return substr($this->attributes['initial_delivery_date'],0,10);
    }
    public function initDeliveryTime() {
        return substr($this->attributes['initial_delivery_date'],11,5);
    }
    public function actualDeliveryDate() {
        return substr($this->attributes['actual_delivery_date'],0,10);
    }
    public function actualDeliveryTime() {
        return substr($this->attributes['actual_delivery_date'],11,5);
    }
    public function createdAtDate() {
        return substr($this->attributes['created_at'],0,10);
    }
    public function createdAtTime() {
        return substr($this->attributes['created_at'],11,5);
    }
    public function getCreatedAtAttribute( $value ) {
        return substr($this->attributes['created_at'],0,10) .'T' .  substr($this->attributes['created_at'],11,5);
    }
    public function jobsByStage($stage)
    {
        return $this->jobs->where("stage",$stage);
    }




    public function deliveryDriver(){

    $user =   User::where('id',$this->jobs[0]->delivery_accepted)->first();
    if (!$user)
        return  User::where('id',$this->jobs[0]->assignee)->first();
    else
        return $user;
    }

    public function unitsAmount($stage = -2)
    {
        // -2 means no stage is specified
        // if no stage is specified and job is still before finishing count all jobs
        if($stage == -2 )$jobs = $this->jobs;
        else $jobs = $this->jobs->where("stage",$stage);

        $amountOfUnits =0;
        foreach($jobs as $job)
            if($stage != 3)
            {if($job->material->count_as_unit)
            $amountOfUnits += count(explode(',',$job->unit_num));
            }
            else
            $amountOfUnits += count(explode(',',$job->unit_num));

        return $amountOfUnits;
    }

    public function voucherStatusAsText()
    {

        return "Deprecated";
    }

    public function status()
    {
        if (isset($this->actual_delivery_date)) return "Completed";
        if (!isset($this->jobs[0])) return "Waiting [No Jobs]";
        if($this->contains_modification && $this->jobs->where('stage','!=',-1)->first() != null){
            $status = $this->jobs->where('stage','!=',-1)->first()->stage;

            foreach ($this->jobs->where('stage','!=',-1) as $job) {
              //  dd("1 , " . $status);
                if ($job->stage != -1) continue;
                if ($job->stage != $status) {
                    $status = "In-Progress";
                    return $status;
                    break;
                }
            }
          //  return("2 , " . $status);
            return $this->jobs->where('stage','!=',-1)->first()->assignee == null ? "" . $this->stageToText($status) : "Active in " . $this->stageToText($status) ;

        }
        else {

            $status = $this->jobs[0]->stage;
            foreach ($this->jobs as $job) {
               // if ($job->stage != -1) continue;
                if ($job->stage != $status) {
                  //  return("6 , " );
                    $status = $job->stage;
                   // return $status;
                    break;
                }
            }
        }
        // in delivery
        if ($status == 8) {
          //  return("4 , " . $status);
            if ($this->jobs[0]->assignee != null) {
                if ($this->jobs[0]->delivery_accepted == null)
                    return "Assigned To Driver";
                else
                    return "Active in Delivery";
            }
            return "Waiting in Delivery";
        }
      //  return ("5 , " . $status);
        if($this->stageToText($status) == "Completed") return "Completed";
        return $this->jobs[0]->assignee == null ? "Waiting in " . $this->stageToText($status) : "Active in " . $this->stageToText($status) ;
    }
    public function statusWOStage()
    {
        if (isset($this->actual_delivery_date)) return "Completed";
        $status = $this->jobs[0]->stage;
        foreach ($this->jobs as $job) {
            if ($job->stage != -1) continue;
            if ($job->stage != $status) {
                $status = "In-Progress";
                return $status;
                break;
            }
        }
        // in delivery
        if ($status == 8) {
            if ($this->jobs[0]->assignee != null) {
                if ($this->jobs[0]->delivery_accepted == null)
                    return "Assigned";
                else
                    return "Active";
            }
            return "Waiting";
        }
        return $this->jobs[0]->assignee == null ? "Waiting": "Active";
    }
    public function getStatusToolTipHTML(){
        $toolTip="";
        $newLine ="";
        $sts = array();

        $activeColor = config('site_vars.activeColor');
        $waitingColor = config('site_vars.waitingColor');
        foreach($this->jobs()->get() as $job) {

            if ($job->assignee == null) {
                $newLine =  "<span style= 'color:" . $waitingColor . "'> Waiting in " . $this->stageToText($job->stage) . "</span>". "<br/>";

            } else {
                if ($job->stage ==8)
                {
                    if ($job->delivery_accepted == null)
                        $newLine =  "<span style='color:" . $activeColor . "'> Assigned to " . $job->assignedTo->first_name . "</span>". "<br/>";
                        else
                        $newLine =  "<span style='color:" . $activeColor . "'> Active in " . $this->stageToText($job->stage) . " w/ " . $job->assignedTo->first_name . "</span>". "<br/>";
                }
                else
                $newLine =  "<span style='color:" . $activeColor . "'> Active in " . $this->stageToText($job->stage) . " w/ " . $job->assignedTo->first_name . "</span>". "<br/>";
            }
            if(!in_array($newLine,$sts)){
            $toolTip = $toolTip. $newLine;
            array_push($sts,$newLine);
            }
        }

        return $toolTip;
    }

    // Helper
    public function stageToText($stage){

        switch ($stage) {
            case "1":
                return "Design";
                break;
            case "2":
                return "Milling";
                break;
            case "3":
                return "3D Printing";
                break;
            case "4":
                return "Sintering";
                break;
            case "5":
                return "Pressing";
                break;
            case "6":
                return "Finishing";
                break;
            case "7":
                return "QC";
                break;
            case "8":
                return  "Delivery";
            case "-1":
                return  "Completed";
                break;
        }
        return "STT Error";
    }

    public function allJobsInSameStage($stage){
        foreach ($this->jobs as $job)
            if($job->stage != $stage)
                return false;
        return true;
    }
    public function shouldShowForFinishing(){
        if ($this->hasOnlyModels()) return true;

        foreach ($this->jobs as $job)
            if($job->stage == 6 && $job->jobType->id != 4)
                return true;
        return false;
    }
    public function modelNotReady(){
        foreach ($this->jobs as $job)
            if($job->jobType->id == 4 && $job->stage != 6)
                return true;
        return false;
    }
    public function allUnitsAtFinishing(){
        foreach ($this->jobs as $job)
            if($job->stage < 6 && $job->stage != -1 )
                return false;
        return true;
    }
    public function abutmentsReceived(){
       foreach($this->abutmentsDeliveries as $abt)
           if($abt->status != 3) return false;
       return true;

    }
    public function hasModels(){
        foreach ($this->jobs as $job)
            if($job->jobType->id == 4)
                return true;
        return false;
    }
    public function statusAt($stage)
    {
        if (isset($this->actual_delivery_date)) return "Completed";
            $jobs = $this->jobs()->where('stage',$stage)->get();
         if (!isset($jobs[0]) ) {return "No Jobs";}

        $firstJobAssignee = $jobs->first()->assignee ;
        foreach ($jobs as $job) {
            if ($job->assignee != $firstJobAssignee) {
                $status = "Mixed";
                return $status;
                break;
            }
        }
        // in delivery
        if ($stage == 8) {
            if ($jobs && $jobs[0]->assignee != null) {
                if ($jobs[0]->delivery_accepted != null)
                    return "Active";
                else
                    return "Assigned to Driver";
            }
            return "Waiting";
        }
        return $jobs->first()->assignee == null ?"Waiting": "Active";
    }

    public function hasOnlyModels(){

        foreach ($this->jobs as $job){
            if($job->jobType == null)
                dd("FATAL : Following job has no type: " . $job->id . ' case: ' . $this->id . ' job: ' .  $job);
            if($job->jobType->a_secondary_item != 1)
                return false;}
        return true;
    }

    public function failedUnitsAmount($typeOfFailure){
        $failuresDesc = [0 => "is_rejection",1 => "is_repeat", 2 => "is_modification" , 3=> "is_redo"];
        $amountOfUnits= 0;
        foreach ($this->jobs->where($failuresDesc[$typeOfFailure],1) as $job)
            if($job->material->count_as_unit == 1)
            $amountOfUnits += count(explode(',',$job->unit_num));

        return $amountOfUnits;
    }
    public function materialUsed($materialId){
        $count =0;
        foreach ($this->jobs->whereIn("material_id",$materialId) as $job)
                $count += count(explode(',',$job->unit_num));
        return $count;
    }
    public function hasMaterial($materialId){

        if ($this->jobs->whereIn("material_id",$materialId)->count() > 0)
           return true;
        return false;
    }

    public function activeJobs(){
        return $this->jobs->where('is_active',1);
    }
    public function setJobs(){
        return $this->jobs->where('is_set',1);
    }

    public function helperTogetActiveJobsIds($typeId)
    {
        return $this->jobs
            ->where('stage', $typeId)
            ->filter(function ($job) {
                return $job->is_active !== null && $job->is_active != 0;
            })
            ->pluck('id')
            ->toArray();
    }

    public function helperTogetSetJobsIds($typeId): array
    {
        return $this->jobs
            ->where('stage', $typeId)
            ->filter(function ($job) {
                return $job->is_set !== null && $job->is_set != 0;
            })
            ->pluck('id')
            ->toArray();
    }

    public function countUnitsSet($typeId): int
    {
        return array_reduce(
            $this->jobs
                ->where('stage', $typeId)
                ->filter(function ($job) {
                    return $job->is_set !== null && $job->is_set != 0
                        && ($job->is_active === null || $job->is_active == 0);
                })
                ->toArray(),
            function ($carry, $job) {
                return $carry + count(explode(',', $job['unit_num']));
            },
            0
        );
    }

    public function countUnitsActive($typeId): int
    {
        return array_reduce(
            $this->jobs
                ->where('stage', $typeId)
                ->filter(function ($job) {
                    return $job->is_active !== null && $job->is_active != 0;
                })
                ->toArray(),
            function ($carry, $job) {
                return $carry + count(explode(',', $job['unit_num']));
            },
            0
        );
    }

}
