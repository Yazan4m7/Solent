<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\abutment;
use App\client;
use App\failureCause;
use App\failureLog;
use App\Http\Traits\helperTrait;
use App\implant;
use App\job;
use App\JobType;
use App\material;
use App\payment;
use App\sCase;
use App\Support\Tenancy\TenantDataCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DB;


class ReportsController extends Controller
{
    use helperTrait;

    public function implantsReport(Request $request){


        $clients = client::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $clients= $clients->keyBy('id');
        $perUnitTrigger= $request->perToggle ?  false : true;
        $implants = implant::all();
        $allImplantsSelected=true;



        if ($request->implantsInput && !in_array("all" ,$request->implantsInput)){
            $selectedImplants =  implant::whereIn('id',$request->implantsInput)->get();
            $allImplantsSelected=false;
        }
        else
        $selectedImplants =  $implants;

        $abutments = abutment::all();
        $allAbutmentsSelected=true;
        if ($request->abutmentsInput && !in_array("all" ,$request->abutmentsInput)){
            $selectedAbutments =  abutment::whereIn('id',$request->abutmentsInput)->get();
            $allAbutmentsSelected=false;
        }
        else
          $selectedAbutments =  $abutments;


        $selectedMonths = $request->dateRange;
        if(str_contains($selectedMonths, 'Month')){
            $numOfMonths  = explode(' Month',$selectedMonths)[0];
            $selectedMonths= $this->lastMonthsAsYYYYMM($numOfMonths);
            $dateRangeValue = $numOfMonths . 'm';
        }
        else if ($request->dateRange)
        { $selectedMonths=$this->parseMonthsRange($request->dateRange);
            $dateRangeValue = $request->dateRange;}

        else{
            $selectedMonths=$this->lastMonthsAsYYYYMM(1);
            $dateRangeValue = $request->dateRange ?? "1m";
        }

        foreach($selectedMonths as $month)
            foreach($selectedAbutments as $abutment){
                $clientLevelTotal[$month][$abutment->id] = 0;
                $labLevelTotal[$month][$abutment->id] = 0;

            }

        foreach($clients as $client)
        {
            foreach($selectedAbutments as $abutment)
                $totals[$client->id][$abutment->id] = 0;
        }
        foreach($selectedAbutments as $abutment)
            $totals2[$abutment->id] = 0;

        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.implants',compact('totals','totals2','clients','selectedClients',
            'implants','selectedImplants','allImplantsSelected',
            'abutments', 'selectedAbutments', 'allAbutmentsSelected',
            'selectedMonths','dateRangeValue','clientLevelTotal',
            'perUnitTrigger','labLevelTotal'));


    }
    public function QCReport(Request $request)
    {
        $clients = client::all();
        $selectedClients = $request->doctor ?? ["all"];
        $clients = $clients->keyBy('id');
        $allFailureCauses = failureCause::all();
        $allCausesSelected = true;
        $typesSelected = array();
        $selectedCauses = $request->causesInput ?? ["all"];

        // Set the time range
        $selectedMonths = $request->dateRange;
        if(str_contains($selectedMonths, 'Month')){
            $numOfMonths  = explode(' Month',$selectedMonths)[0];
            $selectedMonths= $this->lastMonthsAsYYYYMM($numOfMonths);
            $dateRangeValue = $numOfMonths.'m';
        }
        else if ($request->dateRange)
        { $selectedMonths=$this->parseMonthsRange($request->dateRange);
            $dateRangeValue = $request->dateRange;}
        else{
            $selectedMonths=$this->lastMonthsAsYYYYMM(1);
            $dateRangeValue = $request->dateRange ?? "1m";
        }
        // reverse array to make new to old
        $selectedMonths=array_reverse($selectedMonths);


        // get failure logs
        $failureLogs = array();
        $query = failureLog::query();

        // Filter the logs by user inputs
        if(isset($request->failureTypeInput) && !in_array('all',$request->failureTypeInput)) {
            $query->whereIn('failure_type', $request->failureTypeInput);
            $typesSelected = $request->failureTypeInput;

            }
        if (isset($request->causesInput) && !in_array('all',$request->causesInput)) {
            $query->whereIn('cause_id', $request->causesInput);
            $selectedFailureCauses = failureCause::whereIn("id",$request->causesInput )->get();
            $allCausesSelected=false;
            }
            else
            $selectedFailureCauses = $allFailureCauses;


            // Get the FILTERED RESULTS
            $results = $query->whereBetween('created_at', [ end($selectedMonths)  . '-01 00:00:00',array_values($selectedMonths)[0] . '-31 23:59:59'])->get();
             //dd($results);
             // SEPARATE THEM BY MONTH
            foreach($selectedMonths as $month){
                $failureLogs[$month] = $results->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-31 23:59:59']);

                // Total cases and units of every month
                $amountOfCases[$month] = $failureLogs[$month]->groupBy("case_id")->pluck("case_id")->count();
                //if(count($amountOfCases[$month])!= 0)
                //dd($amountOfCases[$month]);
                $amountOfUnits[$month] = 0;
                $labLevelTotal[$month] = 0;
            }


            //Get Total Counts Of All failed Units
                $amountOfUnitsFailed = 0;
            $failedJobs = job::whereIn('case_id' , $results->pluck('case_id')->toArray())->where("is_rejection", 1)->orWhere("is_repeat",1)->orWhere("is_modification",1)->orWhere("is_redo",1)->get() ;
            //dd($failedJobs);
            foreach($failedJobs as $job)
                $amountOfUnitsFailed+= count(explode(',',$job->unit_num));

        return view('reports.QC',compact('clients',
            'failureLogs','selectedMonths','selectedClients','dateRangeValue', 'allCausesSelected',
            'allFailureCauses','selectedFailureCauses','typesSelected','amountOfCases','labLevelTotal','amountOfUnitsFailed'));
    }
    public function jobTypeReport(Request $request)
    {

        $allJobTypesSelected = true;

        $clients = client::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $clients= $clients->keyBy('id');
        $perUnitTrigger= $request->perToggle ?  false : true;
        $jobTypes = JobType::all();
        if ($request->jobTypesInput && !in_array("all" ,$request->jobTypesInput)){
        $selectedJobTypes =  JobType::whereIn('id',$request->jobTypesInput)->get();
            $allJobTypesSelected=false;
        }
        else{
        $selectedJobTypes =  JobType::whereIn('id',[1,2,3,4])->get();
            $allJobTypesSelected=false;}

        $selectedMonths = $request->dateRange;
        if(str_contains($selectedMonths, 'Month')){
            $numOfMonths  = explode(' Month',$selectedMonths)[0];
            $selectedMonths= $this->lastMonthsAsYYYYMM($numOfMonths);
            $dateRangeValue = $numOfMonths . 'm';
        }
        else if ($request->dateRange)
        { $selectedMonths=$this->parseMonthsRange($request->dateRange);
            $dateRangeValue = $request->dateRange;}

        else{
            $selectedMonths=$this->lastMonthsAsYYYYMM(1);
            $dateRangeValue = $request->dateRange ?? "1m";
        }
        foreach($selectedMonths as $month)
            foreach($selectedJobTypes as $jobType){
            $clientLevelTotal[$month][$jobType->id] = 0;
            $labLevelTotal[$month][$jobType->id] = 0;
        }
        foreach($clients as $client)
        {
            foreach($selectedJobTypes as $type)
                $totals[$client->id][$type->id] = 0;
        }
        foreach($selectedJobTypes as $type)
            $totals2[$type->id] = 0;
        $totals2[99] = 0;
        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.jobTypes',compact('clients','totals','totals2',
            'jobTypes','selectedJobTypes','allJobTypesSelected','labLevelTotal',
         'selectedClients','selectedMonths','dateRangeValue','clientLevelTotal',
            'allJobTypesSelected','perUnitTrigger'));

    }
    public function numOfUnitsReport(Request $request)
    {

        $clients = client::all();
        $materials = material::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $selectedMaterials =  $request->material ?? [1,2,3];//material::all()->pluck('id')->toArray();
        $selectedMonths = $request->dateRange;

        if(str_contains($selectedMonths, 'Month')){

            $numOfMonths  = explode(' Month',$selectedMonths)[0];
            $selectedMonths= $this->lastMonthsAsYYYYMM($numOfMonths);
            $dateRangeValue = $numOfMonths . 'm';
            // yields to 1m 3m 6m 12m and so on...
        }
        else if ($request->dateRange)
        { $selectedMonths=$this->parseMonthsRange($request->dateRange);
            $dateRangeValue = $request->dateRange;}
        else{
        $selectedMonths=$this->lastMonthsAsYYYYMM(1);
        $dateRangeValue = $request->dateRange ?? "1m";}
       // dd($selectedMonths);

        /*
         * SelectedMonths is yyyy-mm or an array of yyyyy-mm
         */


        foreach($selectedMonths as $month)
            {
                foreach($selectedMaterials as $matId)
                $totalsArray[$month][$matId] = 0;

            // for totals column
            $totalsArray[$month][99] = 0;
            }
        foreach($clients as $client)
        {
            foreach($selectedMaterials as $matId)
                $totals[$client->id][$matId] = 0;
        }
        foreach($selectedMaterials as $matId)
            $totals2[$matId] = 0;
        $totals2[99] = 0;

        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.numOfUnits',compact('clients','totals','totals2',
            'materials','selectedMaterials','selectedClients','selectedMonths','dateRangeValue','totalsArray'));
    }
    public function repeatsReport(Request $request)
    {
        $clients = client::all();
        $materials = material::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $allFailureTypes = [0 => "Rejection",1 => "Repeat", 2 => "Modification" , 3=> "Redo", 4=>"Successful"];
        $selectedFailureTypes =  [0 => "Rejection",1 => "Repeat", 2 => "Modification" , 3=> "Redo", 4=>"Successful"];
        $allFailureTypesSelected = true;
        $clientsWithFailures = array();

        if(isset($request->failureTypeInput) && !in_array('all',$request->failureTypeInput)) {
            $selectedFailureTypes=$request->failureTypeInput;
            $allFailureTypesSelected = false;
        }
        // Set the time range
        $selectedMonths = $request->dateRange;
        if(str_contains($selectedMonths, 'Month')){
            $numOfMonths  = explode(' Month',$selectedMonths)[0];
            $selectedMonths= $this->lastMonthsAsYYYYMM($numOfMonths);
            $dateRangeValue = $numOfMonths.'m';
        }
        else if ($request->dateRange)
        { $selectedMonths=$this->parseMonthsRange($request->dateRange);
            $dateRangeValue = $request->dateRange;}
        else {
            $selectedMonths=$this->lastMonthsAsYYYYMM(1);
            $dateRangeValue = $request->dateRange ?? "1m";
        }

        // reverse array to make new to old
        $selectedMonths=array_reverse($selectedMonths);
        //dd($request->perToggle);
        $perUnitTrigger = $request->perToggle ;
        $countOrPercentage =$request->countOrPercentageToggle ?  false : true;;
        return view('reports.repeats',compact('clients',
            'materials','selectedMonths','selectedClients','dateRangeValue','selectedFailureTypes',
            'clientsWithFailures','perUnitTrigger','countOrPercentage','allFailureTypesSelected','allFailureTypes'));
    }
    public function homeScreen(){


        $permissions = Cache::get('user'.Auth()->user()->id);



         if(Auth()->user()->is_admin == 1 ||($permissions && $permissions->contains('permission_id', 123)))
            return $this->adminHomeScreen();
         else
             return redirect('/operations-dashboard');

    }
    public function adminHomeScreen(Request $request = null){

        $dashboardSampleDataMode = (bool) config('features.dashboard.sample_data', true);

        $dashboardData = app(TenantDataCache::class)->remember('dashboard.home', (int) config('tenancy.cache_ttls.dashboard', 60), function () {

            $last7DaysLabels = $this->getLastNDays(7,'Y-m-d');
            $last30DaysLabels = $this->getLastNDays(30,'Y-m-d');

            $compCasesObjectsIn30Days = $this->getCompletedCasesInLastNDays($last30DaysLabels);
            $collectionsInLast30Days = $this->getCollectionsInLastNDays($last30DaysLabels);
            $compCasesObjectsIn7Days = [
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[6] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[5] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[4] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[3] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[2] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[1] . '%')->get(),
                sCase::where('actual_delivery_date', 'like', '%' . $last7DaysLabels[0] . '%')->get(),
            ];


            // *** COMPLETED UNITS COUNT IN THE LAST 7 DAYS :: *** //
            // Index 6 of $compUnitsCount7Days and 0 of $compCasesObjectsIn7Days is today.
            $compUnitsCount7Days = [
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[6]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[5]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[4]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[3]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[2]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[1]),
                $this->getUnitsCountOfCasesObjects($compCasesObjectsIn7Days[0]),
            ];

            // *** COMPLETED CASES COUNT IN THE LAST 7 DAYS :: *** //
            $compCasesCount7Days = [];
            $compCasesCount30Days = [];
            $compUnitsCount30Days = [];
            $sales30Days = [];
            //dd($compCasesObjectsIn7Days);
            // Counting..
            foreach($compCasesObjectsIn7Days as $bunchOfCases)
                array_push ($compCasesCount7Days,count($bunchOfCases));
            foreach($compCasesObjectsIn30Days as $bunchOfCases){
                array_push ($compCasesCount30Days,count($bunchOfCases));
                array_push($compUnitsCount30Days,$this->getUnitsCountOfCasesObjects($bunchOfCases));
                array_push ($sales30Days,$this->getValueOfCasesObjects($bunchOfCases));
            }

            $startOfToday = now() . '00:00:00';
            $endOfToday = now()->subDays(1) . '23:59:59';

            // **  Doughnut Chart Counts ** //
            $waitingJobsToday = $this->getUnitsCountOfJobsObjects(job::whereNull('assignee')->where('stage','!=',-1)->get());

            $CompletedJobsToday = $compUnitsCount7Days[6];
            $ActiveJobsToday = $this->getUnitsCountOfJobsObjects(job::whereNotNull('assignee')->where('stage','!=',-1)->get());
            //dd(job::whereNotNull('assignee')->get());
            $DeliveriesToday = sCase::where('initial_delivery_date','like', '%' . $last7DaysLabels[6] . '%')->where('delivered_to_client',0)->orderBy('initial_delivery_date')->get();
            $paymentsReceivedToday = payment::where('created_at','like', '%' . $last7DaysLabels[6] . '%')->orderBy('created_at')->get();
            $newCustomers = $DeliveriesToday->pluck('client_id')->unique()->count();

            $totalUnits = ($CompletedJobsToday ?? 0) + ($ActiveJobsToday ?? 0) + ($waitingJobsToday ?? 0);
            $conversionRate = $totalUnits ? round((($CompletedJobsToday ?? 0) / $totalUnits) * 100, 2) : 0;

            $labelToLookFor = substr($last30DaysLabels[29],0,8) . "01";
            $key = array_search($labelToLookFor, $last30DaysLabels);
            $last30DaysLabels[$key] = "** ".  $last30DaysLabels[$key] . " **";
//        dd($labelToLookFor);
            $compCasesCount7Days= array_reverse($compCasesCount7Days);

            return compact('compUnitsCount7Days','compCasesCount7Days',
            'waitingJobsToday','CompletedJobsToday','ActiveJobsToday','DeliveriesToday',
            'paymentsReceivedToday','last7DaysLabels','compCasesObjectsIn30Days','compUnitsCount30Days',
            'collectionsInLast30Days','last30DaysLabels','compCasesCount30Days','sales30Days','newCustomers','conversionRate');
        }, [
            'user_id' => Auth()->id(),
            'date' => now()->toDateString(),
        ]);

        $dashboardData['dashboardSampleDataMode'] = $dashboardSampleDataMode;

        return view('dashboard', $dashboardData);
    }
    public function handleEmployeeRedirection(){
        return redirect('/operations-dashboard');
    }
    public function blankPage(){

        return view('blank');
    }
    public function materialReport(Request $request){

        // Time Filtration
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('-30 days'));
            $to = now()->toDateString();
        }
        $cases = sCase::where(function ($cases) use($from,$to): void{
            $cases->whereBetween('actual_delivery_date', [ $from. ' 00:00', $to . ' 23:59'])
                // ->orWhereNull('actual_delivery_date')
            ;});

        // Client Filtration
        if ($request->doctor && !in_array( "all",$request->doctor)){
            $cases=$cases->whereIn('doctor_id',$request->doctor);
        }

        $cases = $cases->orderByRaw('-`actual_delivery_date` ASC')->get();
        //$cases = $cases->filter->hasMaterial([1,20,2,3,4,6,7,9,10])->values();
        $totalAmount = 0;

        foreach($cases as $case) {
           // if (!isset($case->invoice))
             //   print_r($case->id);
            $totalAmount += isset($case->invoice) ? $case->invoice->amount : 0;
        }
        $selectedClients = $request->doctor;
        $clients = client::without(['discounts','cases'])->get();
        return view ('reports.case-materials-report',compact('totalAmount','cases','from','to','selectedClients','clients'))->with('patientName',$request->patient_name);

    }}
