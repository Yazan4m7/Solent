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
        $perUnitTrigger = $request->boolean('perToggle');
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

        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.implants',compact('clients','selectedClients',
            'implants','selectedImplants','allImplantsSelected',
            'abutments', 'selectedAbutments', 'allAbutmentsSelected',
            'selectedMonths','dateRangeValue','perUnitTrigger'));


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


        // Get failure logs for the complete user-selected range. The previous
        // month-by-month query reversed the range bounds when more than one
        // month was selected, which left multi-month reports empty.
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

        if (!in_array('all', $selectedClients, true)) {
            $query->whereHas('case', function ($caseQuery) use ($selectedClients): void {
                $caseQuery->whereIn('doctor_id', $selectedClients);
            });
        }

        $chronologicalMonths = collect($selectedMonths)->sort()->values();
        $rangeStart = $chronologicalMonths->first() . '-01 00:00:00';
        $rangeEnd = \Carbon\Carbon::parse($chronologicalMonths->last() . '-01')->endOfMonth()->format('Y-m-d H:i:s');

        $failureLogs = $query
            ->with(['case.client', 'causeObject'])
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->orderByDesc('created_at')
            ->get();
        $amountOfCases = $failureLogs->pluck('case_id')->filter()->unique()->count();

        return view('reports.QC',compact('clients',
            'failureLogs','selectedMonths','selectedClients','dateRangeValue', 'allCausesSelected',
            'allFailureCauses','selectedFailureCauses','typesSelected','amountOfCases'));
    }
    public function jobTypeReport(Request $request)
    {

        $allJobTypesSelected = true;

        $clients = client::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $clients= $clients->keyBy('id');
        $perUnitTrigger = $request->boolean('perToggle');
        $jobTypes = JobType::all();
        $requestedJobTypes = (array) $request->input('jobTypesInput', []);
        if (in_array('all', $requestedJobTypes, true)) {
            $selectedJobTypes = $jobTypes;
        } elseif ($requestedJobTypes !== []) {
            $selectedJobTypes = JobType::whereIn('id', $requestedJobTypes)->get();
            $allJobTypesSelected = false;
        } else {
            $selectedJobTypes = JobType::whereIn('id', [1, 2, 3, 4])->get();
            $allJobTypesSelected = false;
        }

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
        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.jobTypes',compact('clients','jobTypes','selectedJobTypes',
            'selectedClients','selectedMonths','dateRangeValue','allJobTypesSelected','perUnitTrigger'));

    }
    public function numOfUnitsReport(Request $request)
    {

        $clients = client::all();
        $materials = material::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $requestedMaterials = (array) $request->input('material', []);
        if (in_array('all', $requestedMaterials, true)) {
            $selectedMaterials = $materials->pluck('id')->all();
        } elseif ($request->has('material')) {
            $selectedMaterials = $materials->whereIn('id', $requestedMaterials)->pluck('id')->all();
        } else {
            $selectedMaterials = $materials->whereIn('id', [1, 2, 3])->pluck('id')->all();
        }
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
        $selectedMonths=array_reverse($selectedMonths);

        return view('reports.numOfUnits',compact('clients','materials','selectedMaterials',
            'selectedClients','selectedMonths','dateRangeValue'));
    }
    public function repeatsReport(Request $request)
    {
        $clients = client::all();
        $selectedClients =  $request->doctor ?? ["all"];
        $allFailureTypes = [0 => "Rejection",1 => "Repeat", 2 => "Modification" , 3=> "Redo", 4=>"Successful"];
        $selectedFailureTypes = $allFailureTypes;
        $allFailureTypesSelected = true;

        if(isset($request->failureTypeInput) && !in_array('all',$request->failureTypeInput)) {
            $selectedFailureTypeIds = collect($request->failureTypeInput)
                ->map(fn ($failureType) => (int) $failureType)
                ->filter(fn ($failureType) => array_key_exists($failureType, $allFailureTypes))
                ->unique()
                ->all();
            $selectedFailureTypes = array_intersect_key($allFailureTypes, array_flip($selectedFailureTypeIds));
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

        $chronologicalMonths = collect($selectedMonths)->sort()->values();
        $rangeStart = \Carbon\Carbon::parse($chronologicalMonths->first() . '-01')->startOfMonth();
        $rangeEnd = \Carbon\Carbon::parse($chronologicalMonths->last() . '-01')->endOfMonth();

        // reverse array to make new to old
        $selectedMonths=array_reverse($selectedMonths);
        //dd($request->perToggle);
        $perUnitTrigger = $request->boolean('perToggle');
        $showPercentage = $request->boolean('countOrPercentageToggle');
        return view('reports.repeats',compact('clients',
            'selectedMonths','selectedClients','dateRangeValue','selectedFailureTypes',
            'perUnitTrigger','showPercentage','allFailureTypesSelected','allFailureTypes',
            'rangeStart','rangeEnd'));
    }
    public function homeScreen(){


        $permissions = Cache::get('user'.Auth()->user()->id);



         if(Auth()->user()->is_admin == 1 ||($permissions && $permissions->contains('permission_id', 123)))
            return $this->adminHomeScreen();
         else
             return redirect('/operations-dashboard');

    }
    public function adminHomeScreen(Request $request = null){

        $request = $request ?: request();
        if ($request->has('sample_data')) {
            $dashboardSampleDataMode = $request->boolean('sample_data');
            $request->session()->put('dashboard.sample_data', $dashboardSampleDataMode);
        } else {
            $dashboardSampleDataMode = (bool) $request->session()->get(
                'dashboard.sample_data',
                config('features.dashboard.sample_data', false)
            );
        }

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
            $deliveryWindowStart = now()->copy()->startOfDay();
            $deliveryWindowEnd = now()->copy()->addDays(2)->endOfDay();
            $deliveryCases = sCase::with(['client:id,name'])
                ->whereBetween('initial_delivery_date', [
                    $deliveryWindowStart->toDateTimeString(),
                    $deliveryWindowEnd->toDateTimeString(),
                ])
                ->where('delivered_to_client', 0)
                ->orderBy('initial_delivery_date')
                ->get();
            $deliveryDayLabels = ['Today', 'Tomorrow', 'Day after tomorrow'];
            $deliveryScheduleDays = collect(range(0, 2))->map(function (int $offset) use ($deliveryCases, $deliveryDayLabels, $deliveryWindowStart) {
                $date = $deliveryWindowStart->copy()->addDays($offset)->toDateString();

                return [
                    'key' => ['today', 'tomorrow', 'following'][$offset],
                    'label' => $deliveryDayLabels[$offset],
                    'date' => $date,
                    'cases' => $deliveryCases->filter(function (sCase $case) use ($date): bool {
                        $rawDeliveryDate = $case->getRawOriginal('initial_delivery_date') ?: $case->initial_delivery_date;

                        return substr((string) $rawDeliveryDate, 0, 10) === $date;
                    })->values(),
                ];
            });
            $DeliveriesToday = $deliveryScheduleDays->first()['cases'];
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
            'collectionsInLast30Days','last30DaysLabels','compCasesCount30Days','sales30Days','newCustomers','conversionRate',
            'deliveryScheduleDays');
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

    private function getDashboardProductionLoadRows(): array
    {
        $stageConfigs = collect([
            ['stage' => 1, 'label' => 'Design'],
            ['stage' => 2, 'label' => 'Milling'],
            ['stage' => 3, 'label' => '3D Printing'],
            ['stage' => 4, 'label' => 'Sintering'],
        ]);

        $jobsByStage = job::query()
            ->select('stage', 'case_id', 'assignee', 'is_active', 'is_set', 'milling_build_id', 'printing_build_id', 'pressing_build_id')
            ->whereIn('stage', $stageConfigs->pluck('stage')->all())
            ->whereNotNull('case_id')
            ->get()
            ->groupBy('stage');

        return $stageConfigs->map(function (array $stageConfig) use ($jobsByStage) {
            $stageId = (int) $stageConfig['stage'];
            $stageJobs = $jobsByStage->get($stageId, collect());
            $activeCaseIds = $stageJobs
                ->filter(fn ($stageJob) => $this->dashboardStageJobIsActive($stageJob, $stageId))
                ->pluck('case_id')
                ->unique()
                ->values();
            $waitingCaseIds = $stageJobs
                ->reject(fn ($stageJob) => $this->dashboardStageJobIsActive($stageJob, $stageId))
                ->pluck('case_id')
                ->unique()
                ->diff($activeCaseIds)
                ->values();
            $activeCount = $activeCaseIds->count();
            $waitingCount = $waitingCaseIds->count();
            $casesCount = $activeCount + $waitingCount;

            return [
                'label' => $stageConfig['label'],
                'jobs' => $casesCount,
                'cases' => $casesCount,
                'active' => $activeCount,
                'waiting' => $waitingCount,
                'utilization' => $casesCount > 0 ? (int) round(($activeCount / $casesCount) * 100) : 0,
                'jobsScaled' => 0,
            ];
        })->all();
    }

    private function dashboardStageJobIsActive(job $job, int $stageId): bool
    {
        if (in_array($stageId, [2, 4, 5], true)) {
            return (int) $job->is_set === 1 || (int) $job->is_active === 1;
        }

        if ($stageId === 3) {
            return (int) $job->is_set === 1
                || (int) $job->is_active === 1
                || !empty($job->printing_build_id);
        }

        if ($stageId === 1) {
            return !empty($job->assignee) || (int) $job->is_active === 1;
        }

        return (int) $job->is_active === 1;
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
