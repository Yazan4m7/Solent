<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class client extends Authenticatable
{

    use SoftDeletes, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public function discounts()
    {
        return $this->hasMany('App\clientDiscount', 'client_id', 'id');
    }
    public function cases()
    {
        return $this->hasMany('App\sCase', 'doctor_id', 'id');
    }

    public function balanceAt($date)
    {
        $invoices = invoice::where(['doctor_id' => $this->id, 'status' => 1])->where('date_applied', '<=', $date)->get();
        $payments = payment::where('doctor_id', $this->id)->where('created_at', '<=', $date)->get();

        return $invoices->sum('amount') - $payments->sum('amount');
    }


    // REPORTS


    public function numOfUnitsByMaterial($materialId, $month)
    {
        $monthYear = explode('-', $month);


        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59


        $jobs = job::whereHas('case', function ($q) use ($start,$end): void {
            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
        })->where(["material_id" => $materialId,
                    "is_rejection" =>0 ,
                    "is_repeat" =>0 ,
                    "is_modification" => 0 ,
                    "is_redo" => 0])->get();

        $numOfUnits=0;
        foreach($jobs as $job )
            if($job->material->count_in_units_counts_report)
            $numOfUnits += count(explode(',', $job->unit_num));

        return $numOfUnits;
    }

    public function numOfUnitsByJobType($jobTypeId, $month, $isFiltered = false)
    {
        $monthYear = explode('-', $month);


        $date = Carbon::parse($monthYear[0] . "-" . $monthYear[1] . "-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59

        $jobs = job::whereHas('case', function ($q) use ($start, $end): void {
            $q->where('doctor_id', $this->id)->whereBetween('actual_delivery_date', [$start, $end]);
        })->where(["type" => $jobTypeId,
            "is_rejection" => 0,
            "is_repeat" => 0,
            "is_modification" => 0,
            "is_redo" => 0])
            ->get();

        $numOfUnits = 0;
        if ($isFiltered) {
        foreach ($jobs as $job)
            if ($job->material->count_in_job_types_report)
                $numOfUnits += count(explode(',', $job->unit_num));
    }else
        {
            foreach ($jobs as $job)
                    $numOfUnits += count(explode(',', $job->unit_num));
        }
        return $numOfUnits;
    }
    public function numOfCasesByJobType($jobTypeId, $month)
    {
        $monthYear = explode('-', $month);


        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59


        $jobs = job::whereHas('case', function ($q) use ($start,$end): void {
            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
        })->where(["type" =>$jobTypeId,
            "is_rejection" => 0 ,
            "is_repeat" => 0 ,
            "is_modification" => 0 ,
            "is_redo" => 0])->groupBy('case_id')->pluck("case_id")->count();


        return $jobs;
    }

    public function numOfUnitsBy_abutment_implants($abutmentId,$selectedImplants, $month,$useFilter = false){

        $monthYear = explode('-', $month);
        $numOfUnits=0;

        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59

        // dd($casesIds);
//        $abutmentsTypeJobs = job::whereHas('case', function ($q) use ($start,$end) {
//            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
//        })->where([
//            "is_rejection" => 0 ,
//            "is_repeat" => 0 ,
//            "is_modification" => 0 ,
//            "is_redo" => 0])->where("type" ,11)->pluck("id")
//        ; //

        $clientJobs = job::whereHas('case', function ($q) use ($start,$end) {
            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
        })->where([
            "is_rejection" => 0 ,
            "is_repeat" => 0 ,
            "is_modification" => 0 ,
            "is_redo" => 0])->pluck("id");
        $abutmentsRecords = abutmentDeliveryRecord::whereIn("job_id",$clientJobs)
            ->where("abutment_id",$abutmentId)
            ->get();

        //dd($abutmentsRecords);
        if($useFilter){
            foreach ($abutmentsRecords as $record){
               if($record->job->material != null)
                    if($record->job->material->count_in_implants_report)
                $numOfUnits += count(explode(',', $record->units));}

        }
        else{
            foreach ($abutmentsRecords as $record)
                $numOfUnits += count(explode(',', $record->units));
        }
        return $numOfUnits;
    }
    public function numOfCasesBy_abutment_implants($abutmentId,$selectedImplants, $month){

        $monthYear = explode('-', $month);
        $numOfUnits=0;

        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59

        $casesIds = sCase::where("doctor_id",$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck("id")->toArray();
       // dd($casesIds);
        $abutmentsRecords = abutmentDeliveryRecord::whereIn("case_id",$casesIds)
            ->where("abutment_id",$abutmentId)
            ->whereIn("implant_id",$selectedImplants)
            ->distinct('case_id')->count();


//        $jobs = job::whereHas('case', function ($q) use ($start,$end) {
//            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
//        })->where(["abutment" =>$abutmentId,
//            "is_rejection" => 0 ,
//            "is_repeat" => 0 ,
//            "is_modification" => 0 ,
//            "is_redo" => 0])
//        ;

       // $sql_with_bindings =Str::replaceArray('?', $jobs->getBindings(), $jobs->toSql());
       // dd($sql_with_bindings);
//        if (isset($selectedImplants) && !in_array('all',$selectedImplants))
//            return  $jobs->whereIn("implant",$selectedImplants)->groupBy('case_id')->pluck("case_id")->count();
//        else
//            return  $jobs->groupBy('case_id')->pluck("case_id")->count();

//        $numOfUnits=0;
//        foreach($jobs as $job )
//            $numOfUnits += count(explode(',', $job->unit_num));
//        if($numOfUnits >0 )
//        dd($numOfUnits);
        return $abutmentsRecords;
    }

    public function getFailedUnitsCount($month, $failureType, $isFiltered= false){


        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        switch($failureType){
            //REJECT
            case 0:{ $jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_rejection",1)->get();break;}

            //REPEAT
            case 1:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_repeat",1)->get();break;}

            //Modification
            case 2:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_modification",1)->get();break;}

            //REDO
            case 3:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_redo",1)->get();break;}

            //Successfull
            case 4:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where(["is_rejection"=>0,"is_redo"=>0,"is_modification"=>0,"is_repeat"=>0])->get();break;}
        }

        $numOfUnits = 0;
        if($isFiltered){
        foreach($jobs as $job){
            if($job->material->count_in_qc_report == 1)
                $numOfUnits += count(explode(',', $job->unit_num));
       }}else{
            foreach($jobs as $job){
                    if( $job->material->count_as_unit  == 1)
                        $numOfUnits += count(explode(',', $job->unit_num));
            }
        }
        return $numOfUnits;
    }
    public function getFailedCasesCount($month,$failureType){


        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        switch($failureType){
            //REJECT
            case 0:{ $jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_rejection",1)->get();break;}
            //REPEAT
            case 1:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_repeat",1)->get();break;}
            //Modification
            case 2:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_modification",1)->get();break;}
            //REDO
            case 3:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_redo",1)->get();break;}
            //Successful
            case 4:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where(["is_rejection"=>0,"is_redo"=>0,"is_modification"=>0,"is_repeat"=>0])->get();break;}
        }


        return count($jobs->groupBy("case_id")->toArray());
    }

    public function getFailedUnitsPercentage($month,$failureType){
        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        switch($failureType){
            //REJECT
            case 0:{ $jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_rejection",1)->get();break;}
            //REPEAT
            case 1:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_repeat",1)->get();break;}
            //Modification
            case 2:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_modification",1)->get();break;}
            //REDO
            case 3:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_redo",1)->get();break;}
            //Successful
            case 4:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where(["is_rejection"=>0,"is_redo"=>0,"is_modification"=>0,"is_repeat"=>0])->get();break;}
        }

        $allJobs = job::whereIn('case_id',$idsOfClientCases)->get();
        $numOfFailedUnits = 0;
        $numOfAllUnits = 0;
        foreach($jobs as $job )
            $numOfFailedUnits += count(explode(',', $job->unit_num));
        foreach($allJobs as $job )
            $numOfAllUnits += count(explode(',', $job->unit_num));
        if($numOfFailedUnits ==0 || $numOfAllUnits == 0  ) return "0";
        return number_format((float)$numOfFailedUnits/$numOfAllUnits*100, 2, '.', '');
    }
    public function getFailedCasesPercentage($month,$failureType){
        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        switch($failureType){
            //REJECT
            case 0:{ $jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_rejection",1)->get();break;}
            //REPEAT
            case 1:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_repeat",1)->get();break;}
            //Modification
            case 2:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_modification",1)->get();break;}
            //REDO
            case 3:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where("is_redo",1)->get();break;}
            //Successful
            case 4:{$jobs = job::whereIn('case_id',$idsOfClientCases)->where(["is_rejection"=>0,"is_redo"=>0,"is_modification"=>0,"is_repeat"=>0])->get();break;}
        }

        // might be successful cases depends on failure type
        $failedCases = sCase::whereIn('id',$jobs->pluck("case_id")->toArray())->pluck("id")->toArray();

        // prevents divide by 0
        if(count($failedCases) ==0 || count($idsOfClientCases) == 0  ) return "0";
        return '('.count($failedCases).') ' .number_format((float)count($failedCases)/count($idsOfClientCases)*100, 2, '.', '') ;
    }
    public function successfulUnitsPercentage($month){
        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        $successfulJobs = job::whereIn('case_id',$idsOfClientCases)->where([
            "is_rejection" => 0 ,
            "is_repeat" => 0 ,
            "is_modification" => 0 ,
            "is_redo" => 0,
            "has_been_rejected" => 0,
        ])->whereNull("repeated_job_id")->whereNull("modified_job_id")
            ->whereNull("redone_job_id")->get();
        $allJobs = job::whereIn('case_id',$idsOfClientCases)->get();

        $numOfSuccessfulUnits = 0;
        $numOfAllUnits = 0;
        foreach($successfulJobs as $job )
            $numOfSuccessfulUnits += count(explode(',', $job->unit_num));
        foreach($allJobs as $job )
            $numOfAllUnits += count(explode(',', $job->unit_num));

        return $numOfSuccessfulUnits/$numOfAllUnits*100;
    }
    public function successfulCasesPercentage($month){
        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59
        $idsOfClientCases = sCase::where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end])->pluck('id')->toArray();

        $failedCases  = failureLog::whereIn('case_id',$idsOfClientCases)->pluck('case_id')->toArray();
        $successfulCases = array_diff($failedCases,$idsOfClientCases);
        return count($successfulCases)/100*count($idsOfClientCases);
    }

    public function successfulJobsByMonth($month){
        $monthYear = explode('-', $month);
        $date = Carbon::parse($monthYear[0]."-".$monthYear[1]."-01"); // universal truth month's first day is 1
        $start = $date->startOfMonth()->format('Y-m-d H:i:s'); // 2000-02-01 00:00:00
        $end = $date->endOfMonth()->format('Y-m-d H:i:s'); // 2000-02-29 23:59:59

        $jobs = job::whereHas('case', function ($q) use ($start,$end): void {
            $q->where('doctor_id',$this->id)->whereBetween('actual_delivery_date', [$start,$end]);
        })->where([
            "is_rejection" => 0 ,
            "is_repeat" => 0 ,
            "is_modification" => 0 ,
            "is_redo" => 0,
            "has_been_rejected" => 0,
            ])->whereNull("repeated_job_id")->whereNull("modified_job_id")
            ->whereNull("redone_job_id")->get();

        return $jobs;
    }
    public function successfulJobsCountByMonth($month){

        $jobs = $this->successfulJobsByMonth($month);
        $numOfUnits = 0;
        foreach($jobs as $job )
            $numOfUnits += count(explode(',', $job->unit_num));
        return $numOfUnits;
    }


    // DOCTOR MOBILE ACCESS DATA
    public function loginInLast30Days(){
        $logsCount = signInLog::where("client_id",$this->id)->where('date','>',now()->subMonth())->where('is_clinic',0)->count();
        return !$logsCount ? "0": $logsCount;
    }
    public function lastSignIn(){
        $log= signInLog::where("client_id",$this->id)->where('is_clinic',0)->orderBy("date","desc")->first();
        return !$log ? "Never": $log->date;
    }
    // REMOVED: Device tracking feature
    // public function docDevice(){
    //      $log= signInLog::where("client_id",$this->id)->where('device',"!=","0")->where('is_clinic',0)->orderBy("date","desc")->first();;
    //      return !$log ? "-": $log->device;
    // }

    // CLINIC MOBILE ACCESS DATA
    public function clinicLoginInLast30Days(){
        $logsCount = signInLog::where("client_id",$this->id)->where('date','>',now()->subMonth())->where('is_clinic',1)->count();
        return !$logsCount ? "0": $logsCount;
    }
    public function clinicLastSignIn(){
        $log= signInLog::where("client_id",$this->id)->where('is_clinic',1)->orderBy("date","desc")->first();
        return !$log ? "Never": $log->date;
    }
    // REMOVED: Device tracking feature
    // public function clinicDevice(){
    //     $log= signInLog::where("client_id",$this->id)->where('device',"!=","0")->where('is_clinic',1)->orderBy("date","desc")->first();;
    //     return !$log ? "-": $log->device;
    // }

}
