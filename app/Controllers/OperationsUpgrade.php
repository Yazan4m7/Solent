<?php

namespace App\Http\Controllers;

use App\Build;
use App\caseLog;
// REMOVED: Device feature
// use App\device;
use App\job;
use App\sCase;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\NoReturn;


class OperationsUpgrade extends Controller
{
    protected $caseController;


    /**
     * These are the attributes that move the object in the views (front end)
     *  Build's started at sends case from waiting tab to active tab
     *
     *
     * //     * is_active controls the color and btn text in active dialog
     *
     *
     */
    public const STAGE_CONFIG = [
        'milling' => [
            'number' => 2,
            'name' => 'Milling',
            'set_action' => 'nested',
            'start_action' => 'milling',
            'complete_action' => 'milling',
            'requires_build_name' => true,
            'device_type' => 'mill',
            'multiple-waiting' => true,
            'multiple-active' => false
        ],
        '3dprinting' => [
            'number' => 3,
            'name' => '3D Printing',
            'set_action' => 'set',
            'start_action' => 'printing',
            'complete_action' => 'printing',
            'requires_build_name' => true,
            'device_type' => 'printer',
            'multiple-waiting' => false,
            'multiple-active' => false
        ],
        'sintering' => [
            'number' => 4,
            'name' => 'Sintering',
            'set_action' => 'placed',
            'start_action' => 'sintering',
            'complete_action' => 'sintering',
            'requires_build_name' => false,
            'device_type' => 'furnace',
            'multiple-waiting' => true,
            'multiple-active' => false
        ],
        'pressing' => [
            'number' => 5,
            'name' => 'Pressing',
            'set_action' => 'placed',
            'start_action' => 'pressing',
            'complete_action' => 'pressing',
            'requires_build_name' => true,
            'device_type' => 'press',
            'multiple-waiting' => false,
            'multiple-active' => false
        ],
        'delivery' => [
            'number' => 8,
            'name' => 'Delivery',
            'set_action' => 'assigned',
            'start_action' => 'delivery',
            'complete_action' => 'delivery',
            'requires_build_name' => false,
            'device_type' => 'driver',
            'multiple-waiting' => true,
            'multiple-active' => false
        ]
    ];

    // Substage action mapping for main manufacturing stages
    private array $stageActions = [
        // Milling
        'MILLING_SET' => 2.1,
        'MILLING_START' => 2.2,
        'MILLING_COMPLETE' => 2.3,
        // 3D Printing
        'PRINTING_SET' => 3.1,
        'PRINTING_START' => 3.2,
        'PRINTING_COMPLETE' => 3.3,
        // Sintering
        'SINTERING_SET' => 4.1,
        'SINTERING_START' => 4.2,
        'SINTERING_COMPLETE' => 4.3,
        // Pressing
        'PRESSING_START' => 5.1,
        'PRESSING_COMPLETE' => 5.2,
        // Delivery
        'DELIVERY_ASSIGN' => 8.1,
        'DELIVERY_ACCEPT' => 8.2,
        'DELIVERY_COMPLETE' => 8.3,
    ];

    public function __construct(CaseController $caseController)
    {
        $this->caseController = $caseController;
    }

    /**
     * Generic method to handle setting cases on devices for all stages
     * This is the main implementation that handles different device types with stage-specific configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setMultipleCases(Request $request)
    {

        Log::info($request->all());

        return $this->executeTransaction(function () use ($request) {
            // Get device type and ID
            $type = $request->input('type');
            $deviceId = $request->input("deviceId-{$type}") ?? $request->input('deviceId');

            // Get stage configuration
            if (!isset($type)) {
                return $this->errorResponse("No valid stage type: {$type}");
            }

            $stageConfig = self::STAGE_CONFIG[$type];
            $stage = $stageConfig['number'];

            // Validate device ID
            if (empty($deviceId)) {
                return $this->errorResponse("No device selected for {$stageConfig['name']}");
            }

            // Get selected cases
            $casesIds = $this->parseCheckboxInput($request->input("WaitingPopupCheckBoxes{$type}"));

            if (empty($casesIds)) {
                return $this->errorResponse('No cases selected');
            }

            // Get device information
            $device = device::find($deviceId);
            $deviceName = $device ? $device->name : 'unknown device';

            // Get all jobs for the selected cases at the appropriate stage
            $jobs = job::whereIn('case_id', $casesIds)->where('stage', $stage)->get();

            if ($jobs->isEmpty()) {
                return $this->errorResponse('No jobs found for selected cases');
            }

            // Special handling for 3D printing builds
            $buildName = $request->input('buildName');


            // Create a new build
            $build = new Build();

            $build->set_at = now();
            $build->name = "";
            // REMOVED: Device feature
            // $build->device_used = $deviceId;
            $build->save();
            if ($type == 'sintering') {
                $build->name = 'Sintering-' . $build->id;
                $build->set_at = now();
                $build->started_at = now();
            } else {
                $build->name = $buildName;
            }
            $build->save();

            // Set up jobs for the build
            Log::info("Setting up jobs for build {$build->id} stage : {$stage} type: {$type}");
            $jobCount = $this->setupJobs($jobs, $deviceId, $stage, $type, [
                'milling_build_id' => $type == "milling" ? $build->id : null,
                'printing_build_id' => $type == "3dprinting" ? $build->id : null,
                'sintering_build_id' => $type == "sintering" ? $build->id : null,
                'pressing_build_id' => $type == "pressing" ? $build->id : null,
                'notes_suffix' => ", Build: {$buildName}",
                'is_active' => $type == "sintering" ? 1 : 0
            ]);

            // For sintering, start the build immediately
//                if ($type == "sintering") {
//                    $build->started_at = now();
//                    $build->save();
//}

            // Regular setup for other types
            //  $jobCount = $this->setupJobs($jobs, $deviceId, $stage, $type);

            return $this->successResponse(
                "{$jobCount} jobs have been {$stageConfig['set_action']} on {$deviceName}"
            );

        }, 'admin-dashboard-v2');
    }

    /**
     * Route alias for setMultipleCases - used by route: /set-multiple-cases
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setOnDevice(Request $request)
    {
        return $this->setMultipleCases($request);
    }

    /**
     * Router alias for setMultipleCases, specifically for setting cases on a printer
     * Used by route: /set-cases-on-printer
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setJobsOnDevice(Request $request)
    {
        // Add 'type' parameter to request data
        $request->merge(['type' => '3dprinting']);

        // Call the main setMultipleCases method
        return $this->setMultipleCases($request);
    }

    /**
     * Generic method to activate multiple cases on a device (start processing)
     * Handles different device types with stage-specific configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateMultipleCases(Request $request)
    {

        // dd(self::STAGE_CONFIG , $request->all());

        return $this->executeTransaction(function () use ($request) {
            $deviceId = $request->input('deviceId');
            $type = $request->input('type');
            // Get device ID

            $deviceId = $request->input('deviceId');
            if (empty($deviceId)) {
                return $this->errorResponse('No device selected');
            }

            if (empty($type)) {
                $type = $this->getTypeFromStage($stage);
            }


            // Get job IDs to activate
            $jobIds = explode(',', $request->input('items'));

            if (empty($jobIds)) {
                // For sintering, get all unstarted jobs

                $jobIds = job::where('stage', 4)
                    ->whereNull('is_active')
                    ->pluck('id')
                    ->toArray();
                if (empty($jobIds)) {
                    return $this->errorResponse('No jobs selected');
                }


            }


            $buildIdField = 'printing_build_id'; // default for 3D printing
            if ($type == 'milling') {
                $buildIdField = 'milling_build_id';
            } elseif ($type == 'pressing') {
                $buildIdField = 'pressing_build_id';
            } elseif ($type == 'sintering') {
                $buildIdField = 'sintering_build_id';
            }


            $buildIds = $request->input('buildsIdsHiddenInput' . $deviceId);// already an array
            $buildIds = explode(',', $buildIds);
            // Find jobs and validate
            $jobs = job::whereIn($buildIdField, $buildIds)
                ->where(function ($query) {
                    $query->where('is_active', 0)
                        ->orWhereNull('is_active');
                    // ->orWhere('is_set', 1);
                })->get();

            if ($jobs->isEmpty()) {
                return $this->errorResponse('No jobs found for selected builds');
            }

            // Update build status
            foreach ($buildIds as $buildId) {
                $build = Build::find($buildId);
                if ($build) {
                    $build->started_at = now();
                    $build->save();
                }
            }

            // Set up jobs for the build
//            $jobCount = $this->setupJobs($jobs, $deviceId, $stage, $type, [
//                'milling_build_id' => $type == "milling" ? $build->id : null,
//                'printing_build_id' => $type == "3dprinting" ? $build->id : null,
//                'pressing_build_id' => $type == "pressing" ? $build->id : null,
//                'notes_suffix' => ", Build: {$buildName}",
//                'is_active' => 1
//            ]);

            if ($jobs->isNotEmpty()) {
                Log::info("First job stage: " . $jobs->first()->stage);
            } else {
                Log::info("No active jobs found.");
            }
            if (isset($jobs[0])) {
                Log::info("{$jobs[0]->id} is at stage: {$jobs[0]->stage}");
            } else {
                Log::info("No jobs found.");
            }


            // Start the jobs
            $this->startJobs($jobs, $deviceId, self::STAGE_CONFIG[$type]['number'], $type);

            // Update build status and create logs
            foreach ($buildIds as $buildId) {
                $build = Build::findOrFail($buildId);
                if ($build) {
                    // Always set started_at when activating a build
                    $build->started_at = now();
                    $build->save();

                    // Create log entry for build start
                    caseLog::create([
                        'user_id' => Auth::id(),
                        'case_id' => $jobs->first()->case_id ?? $request->input('items'),
                        'stage' => self::STAGE_CONFIG[$type]['number'],
                        // REMOVED: Device feature
                        // 'device_id' => $deviceId,
                        'action_type' => 2, // 2 = start
                        'action' => 'started_build',
                        'notes' => "Started build: {$build->name}"
                    ]);
                }
            }

            // $deviceName = Device::find($deviceId)->name;SS

            return ($this->successResponse(
                "Started successfully "
            ));

        }, 'admin-dashboard-v2');
    }

    /*******************************************************************************
     * Activate 3D builds - Specialized version of activateMultipleCases for builds
     */
    #[NoReturn] public function activate3DBuilds(Request $request): \Illuminate\Http\RedirectResponse
    {

        // dd($request->attributes->all() , );

        return $this->executeTransaction(function () use ($request) {
            // Get device ID
            $action = $request->input('action');
            $deviceId = $request->input('deviceId');
            $buildsIdsHiddenInput = $request->input('buildsIdsHiddenInput' . $deviceId);

            Log::info("Activate 3D Builds: action = [{$request->input('action')}]");
            Log::info("Activate 3D Builds: deviceId = [{$request->input('deviceId')}]");
            Log::info("Activate 3D Builds: Variable = [{$request->input('buildsIdsHiddenInput'.$deviceId)}]");


            Log::info('Request All Bag:' . json_encode($request->all()));
            Log::info("Activate 3D Builds: action = {$action}, deviceId = {$deviceId}, buildsIdsHiddenInput = {$buildsIdsHiddenInput}");

            if (empty($deviceId)) {
                return $this->errorResponse('No device selected');
            }

            // Get build IDs to activate
            $buildIds = explode(',', $buildsIdsHiddenInput);
            Log::info("items sent to activatDBuie3lds " . json_encode($buildIds));
            if (empty($buildIds)) {
                return $this->errorResponse('No builds selected');
            }


            // Find builds
            $builds = Build::whereIn('id', $buildIds)->get();

            Log::write("WARNING", "builds sent to activate3DBuilds " . json_encode($builds));

            if ($builds->isEmpty()) {
                return $this->errorResponse('Specified builds not found');
            }

            // Get stage type from request or use default
            $type = $request->input('stage_type', '3dprinting');
            Log::info(`type in activate3DBuilds $type`);
            // Determine which build ID field to use based on stage type
            $buildIdField = 'printing_build_id'; // default for 3D printing
            if ($type == 'milling') {
                $buildIdField = 'milling_build_id';
            } elseif ($type == 'pressing') {
                $buildIdField = 'pressing_build_id';
            }
            // Find all jobs associated with these builds
            $jobs = job::whereIn($buildIdField, $buildIds)
                ->where(function ($query) {
                    $query->where('is_active', 0)  // Get explicitly inactive jobs
                    ->orWhereNull('is_active'); // Also get jobs where is_active is null (not yet started)
                })
                ->get();

            if ($jobs->isEmpty()) {
                // Fallback: Try to get all non-active jobs (for backward compatibility)
                $jobs = job::whereIn($buildIdField, $buildIds)
                    ->where(function ($query) {
                        $query->where('is_active', '!=', 1)
                            ->orWhereNull('is_active');
                    })
                    ->get();

                if ($jobs->isEmpty()) {
                    return $this->errorResponse('No inactive jobs found for selected builds');
                }
            }
            Log::info(" Jobs to active 3D builds " . json_encode($jobs));
            Log::info("Stage is : " . $jobs->first()->stage);
            // Determine stage from jobs
            $stage = $jobs->first()->stage;

            // Get device
            $device = device::find($deviceId);
            $deviceName = $device ? $device->name : 'unknown device';

            // Start jobs
            $jobCount = $this->startJobs($jobs, $deviceId, $stage, $type);

            // Mark builds as started
            foreach ($builds as $build) {
                $build->started_at = now();
                $build->save();

                // Additional log for build start
                if ($jobs->isNotEmpty()) {
                    caseLog::create([
                        'user_id' => Auth::id(),
                        'case_id' => $jobs->first()->case_id, // Log on first case
                        'stage' => $stage,
                        // REMOVED: Device feature
                        // 'device_id' => $deviceId,
                        'action_type' => 2, // 2 = start
                        'notes' => "Build {$build->name} started"
                    ]);
                }
            }

            return $this->successResponse("Started {$jobCount} jobs from builds", [
                'jobCount' => $jobCount,
                'buildCount' => $builds->count()
            ]);
        }, 'admin-dashboard-v2');
    }

    /**
     * Generic method to finish multiple cases
     * Handles different device types with stage-specific configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finishMultipleCases(Request $request)
    {
        Log::info('Finishing cases::');
        Log::info($request->all());

        return $this->executeTransaction(function () use ($request) {
            // Get device ID
            $deviceId = $request->input('deviceId');

            if (empty($deviceId)) {
                return $this->errorResponse('No device selected');
            }
            $type = $request->input('type');

            // Get stage type from request


            // Check if we're finishing a build (3D printing, milling, or pressing) or individual jobs


            if ($request->input('buildsIdsHiddenInput' . $deviceId) !== null) {
                $builds = Build::whereIn('id', explode(',', $request->input('buildsIdsHiddenInput' . $deviceId)))->get();
            } else {

                $builds = Build::where('device_used', $deviceId)
                    ->whereNull('finished_at')->where('device_used', $deviceId)
                    ->get();
//                }
            }

            if (empty($builds)) {
                return $this->errorResponse('No items selected to finish');
            }

            foreach ($builds as $build) {
                $build->finished_at = now();
                $build->save();
            }
            $builds = $builds->pluck('id')->toArray();
            // Determine which build ID field to use based on stage type


            $type = str_replace('3d', '', $type);
            $buildIdField = $type . '_build_id';
                // Process build completion
            foreach ($builds as $buildId) {

                    $jobs = job::where($buildIdField, $buildId)
                        ->where('is_active', 1)
                        ->get();
                //////////////////////////// START ///////////////////////////
                ////////////////////////  Finishing build's jobs  ////////////////////////
                ///////////////////////////////////////////////////////
               // dd($buildIdField,);
                    // Get stage from first job
                    $stage = $jobs->first()->stage;
                    // Complete each case's jobs
                    $caseIds = $jobs->pluck('case_id')->unique();
                    foreach ($caseIds as $caseId) {
                        $caseJobs = $jobs->where('case_id', $caseId);
                        $this->caseController->finishCaseStage($caseId, $stage, false, $caseJobs);
                    }
//////////////////////////// END ///////////////////////////
                ////////////////////////  Finishing build's jobs  ////////////////////////
                ///////////////////////////////////////////////////////

                    // Mark build as finished
                    $build->finished_at = now();
                    $build->save();


                //////////////////////////// START ///////////////////////////
                ////////////////////////  LOGGING  ////////////////////////
                ///////////////////////////////////////////////////////

                if ($jobs->isEmpty()) {
                    return $this->errorResponse('No jobs found to complete');
                }
                $stage = $jobs->first()->stage;
                if (empty($type)) {
                    $type = $this->getTypeFromStage($stage);
                }

                if (!$type ) {
                    return $this->errorResponse("No stage type: {$type}");
                }
                Log::alert("type: " . $type);

                // Group jobs by case
                $jobsByCase = $jobs->groupBy('case_id');
                Log::alert("jobsByCase size : " . count($jobsByCase));
                // Complete each case's jobs
                foreach ($jobsByCase as $caseId => $caseJobs) {
                    Log::alert("caseId: " . $caseId);

                    if ($type == 'milling') {
                        Log::info('Completing milling job for case:', [
                            'case_id' => $caseId,
                            'job_count' => $caseJobs->count(),
                            'job_ids' => $caseJobs->pluck('id')->toArray(),
                        ]);
                    }

                    Log::alert("finishing case  : " . $caseId . 'stage  ' . $stage . 'caseJobs  ');
                    $this->caseController->finishCaseStage($caseId, $stage, false, $caseJobs);
                }
                // Get device name for message
                // $device = device::find($deviceId)->get();


//////////////////////////// END ///////////////////////////
                //////////////////////// LOGGING  ////////////////////////
                ///////////////////////////////////////////////////////
                // Get device name for message
                $device = device::find($deviceId);
                $deviceName = $device ? $device->name : 'unknown device';
            }

            return $this->successResponse(
                "Completed builds and their jobs on {$deviceName}"
            );


        }, 'admin-dashboard-v2');
    }


    /**
     * Finish 3D builds - Specialized version of finishMultipleCases for builds
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish3DBuilds(Request $request)
    {
        Log::info('Finishing 3D builds:', $request->all());

        return $this->executeTransaction(function () use ($request) {

            $deviceId = $request->input('deviceId');

            if (empty($deviceId)) {
                return $this->errorResponse('No device selected');
            }

            // Get build IDs from request
            $buildIds = [];
            // if ($request->has('items')) {
            $buildIds = $request->input('buildsIdsHiddenInput' . $deviceId);
            //}

//            if (empty($buildIds)) {
//                $buildIds = explode(',', $request->input('buildsIdsHiddenInput' . $deviceId));
//
//                return $this->errorResponse('No builds selected or found');
//            }
            // ddd($buildIds);
            // Find builds with validation
            $builds = Build::where('id', $buildIds)->get();

            if ($builds->isEmpty()) {
                return $this->errorResponse('No active builds found to complete');
            }
            if (count($builds) > 1) {
                return $this->errorResponse('More than 1 build found');
            }

            $build = $builds->first();

            // GET BUILD'S CASES'JOBS
            $jobs = job::where('printing_build_id', $build->id)
                ->where(function ($query) {
                    $query->where('is_active', 1)
                        ->orWhere('is_set', 1);
                })
                ->get();
            if ($jobs->isEmpty()) {
                return $this->errorResponse('No jobs found for selected builds');
            }
            //   dd(device::find($deviceId));
            // COMPLETE
            $jobCount = $this->completeJobs($jobs, $deviceId, 3, '3dprinting');
            // dd(device::find($deviceId));
            // SET FINISHED AT DATE
            $build->finished_at = now();
            $build->save();


            $jobCount = count($jobs);

            $device = device::find($deviceId);
            $deviceName = $device ? $device->name : 'unknown device';


            // Additional log for build completion
            if ($jobs->isNotEmpty()) {
                caseLog::create([
                    'user_id' => Auth::id(),
                    'case_id' => $jobs->first()->case_id, // Log on first case
                    'stage' => 3,
                    'device_id' => $deviceId,
                    'action_type' => 3, // 3 = complete
                    'notes' => "Build {$build->name} completed on {$deviceName}"
                ]);
            }


            return $this->successResponse("Completed {$jobCount} jobs from builds", [
                'jobCount' => $jobCount,
                'buildCount' => 1
            ]);
        }, 'admin-dashboard-v2');
    }

    /**
     * Assign cases to a delivery driver
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignCasesToDelivery(Request $request)
    {
        Log::info($request->all());

        return $this->executeTransaction(function () use ($request) {
            // Get case IDs to assign
            $casesIds = $this->parseCheckboxInput(
                $request->input('WaitingPopupCheckBoxesdelivery') ??
                $request->input('WaitingPopupCheckBoxesDelivery')
            );

            if (empty($casesIds)) {
                return $this->errorResponse('No cases selected');
            }

            // Get driver ID
            $driverId = $request->input("deviceId-delivery") ??
                $request->input("deviceId-Delivery") ??
                $request->input("deviceId");

            if (empty($driverId)) {
                return $this->errorResponse('No driver selected');
            }

            // Get driver info
            $driver = User::find($driverId);
            if (!$driver) {
                return $this->errorResponse('Invalid driver selected');
            }

            // Verify cases exist
            $cases = sCase::whereIn('id', $casesIds)->get();
            if ($cases->isEmpty()) {
                return $this->errorResponse('No valid cases found');
            }

            // Find or create delivery jobs
            $this->assignCasesToDriver($casesIds, $driverId);

            // Create logs for each case assignment
            foreach ($cases as $case) {
                $this->createDriverAssignmentLog($case->id, $driverId, $driver);
            }

            return $this->successResponse(
                count($cases) . " case(s) have been assigned to " . $driver->first_name . " and are pending acceptance",
                ['driver_name' => $driver->first_name]
            );
        }, 'admin-dashboard-v2');
    }

    /**
     * Assign cases to employee for any stage
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignCasesToEmployee(Request $request)
    {
        Log::info($request->all());

        return $this->executeTransaction(function () use ($request) {
            // Get case IDs to assign
            $casesIds = $this->parseCheckboxInput($request->input('case_ids'));

            if (empty($casesIds)) {
                return $this->errorResponse('No cases selected');
            }

            // Get employee ID and stage
            $employeeId = $request->input('employee_id');
            $stage = $request->input('stage');

            if (empty($employeeId)) {
                return $this->errorResponse('No employee selected');
            }

            if (empty($stage)) {
                return $this->errorResponse('No stage specified');
            }

            // Get employee info
            $employee = User::find($employeeId);
            if (!$employee) {
                return $this->errorResponse('Invalid employee selected');
            }

            // Verify cases exist
            $cases = sCase::whereIn('id', $casesIds)->get();
            if ($cases->isEmpty()) {
                return $this->errorResponse('No valid cases found');
            }

            // Map stage names to numeric stages
            $stageMap = [
                'design' => 1,
                'milling' => 2,
                '3dprinting' => 3,
                'sintering' => 4,
                'pressing' => 5,
                'finishing' => 6,
                'qc' => 7
            ];

            $numericStage = $stageMap[$stage] ?? null;
            if (!$numericStage) {
                return $this->errorResponse('Invalid stage specified');
            }

            // Assign cases to employee
            $this->assignCasesToEmployeeForStage($casesIds, $employeeId, $numericStage);

            // Create logs for each case assignment
            foreach ($cases as $case) {
                $this->createEmployeeAssignmentLog($case->id, $employeeId, $employee, $numericStage);
            }

            return $this->successResponse(
                count($cases) . " case(s) have been assigned to " . $employee->first_name,
                ['employee_name' => $employee->first_name]
            );
        }, 'admin-dashboard-v2');
    }

    /**
     * Get stage type from stage number
     *
     * @param int $stageNumber
     * @return string|null
     */
    private function getTypeFromStage(int $stageNumber): ?string
    {
        foreach (self::STAGE_CONFIG as $type => $config) {
            if ($config['number'] === $stageNumber) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Execute a database transaction and handle response
     *
     * @param \Closure $callback
     * @param string|null $redirectRoute
     * @return \Illuminate\Http\RedirectResponse
     */
    private function executeTransaction(\Closure $callback, ?string $redirectRoute = null)
    {
//        try {
        $result = DB::transaction($callback);

        if (isset($result['success']) && !$result['success']) {
            return back()->with('error', $result['message']);
        }

        $message = $result['message'] ?? 'Operation completed successfully';

        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('success', $message);
        }

        return back()->with('success', $message);
//        } catch (\Exception $e) {
//            Log::error('Transaction error: ' . $e->getMessage(), [
//                'file' => $e->getFile(),
//                'line' => $e->getLine()
//            ]);
//            return back()->with('error', 'An error occurred: ' . $e->getMessage());
//        }
    }

    /**
     * Set up jobs for a device
     *
     * @param \Illuminate\Database\Eloquent\Collection $jobs
     * @param int $deviceId
     * @param int $stage
     * @param string $type
     * @param array $options
     * @return int
     */
    private function setupJobs($jobs, int $deviceId, int $stage, string $type, array $options = []): int
    {
        $jobCount = 0;
        $notesSuffix = $options['notes_suffix'] ?? '';
        $stageConfig = self::STAGE_CONFIG[$type];

        foreach ($jobs as $job) {
            // Update job
            $job->is_set = 1;
            $job->is_active = $options['is_active'] ?? 0;
            $job->device_id = $deviceId;

            // Set build ID based on stage

            $job->milling_build_id = $options['milling_build_id'] ?? null;
            $job->printing_build_id = $options['printing_build_id'] ?? null;
            $job->sintering_build_id = $options['sintering_build_id'] ?? null;
            $job->pressing_build_id = $options['pressing_build_id'] ?? null;

            $job->assignee = Auth::id();
            $job->save();
            $jobCount++;

            // Subst age logic for main manufacturing stages
            $logStage = $stage;
            $isCompletion = 0;
            if ($stage == 2) {
                $logStage = $this->stageActions['MILLING_SET'];
            }
            if ($stage == 3) {
                $logStage = $this->stageActions['PRINTING_SET'];
            }
            if ($stage == 4) {
                $logStage = $this->stageActions['SINTERING_SET'];
            }
            if ($stage == 5) {
                $logStage = $this->stageActions['PRESSING_START'];
            }
            if ($stage == 8) {
                $logStage = $this->stageActions['DELIVERY_ASSIGN'];
            }
            $logData = [
                'user_id' => Auth::id(),
                'case_id' => $job->case_id,
                'stage' => $logStage,
                'is_completion' => $isCompletion
            ];
            if (!empty($notesSuffix)) {
                $logData['notes'] = "Job {$stageConfig['set_action']} on {$stageConfig['device_type']}: {$deviceId}{$notesSuffix}";
            }
            caseLog::create($logData);
        }

        return $jobCount;
    }

    /**
     * Start jobs on a device
     *
     * @param \Illuminate\Database\Eloquent\Collection $jobs
     * @param int $deviceId
     * @param int $stage
     * @param string $type
     * @return int
     */
    private function startJobs($jobs, int $deviceId, int $stage, string $type): int
    {
        $jobCount = 0;
        $stageConfig = self::STAGE_CONFIG[$type];

        foreach ($jobs as $job) {
            // Ensure job is at the right stage
            if ($job->stage != $stage) {
                continue;
            }

            // Update job status
            $job->is_active = 1;

            // Ensure the job has the appropriate build ID for its stage
            if ($stage == 2 && empty($job->milling_build_id)) { // Milling
                // Find a build for this device
                $build = Build::where('device_used', $deviceId)->whereNotNull('set_at')->first();
                if ($build) {
                    $job->milling_build_id = $build->id;
                }
            } elseif ($stage == 3 && empty($job->printing_build_id)) { // 3D Printing
                // Find a build for this device
                $build = Build::where('device_used', $deviceId)->whereNotNull('set_at')->first();
                if ($build) {
                    $job->printing_build_id = $build->id;
                }
            } elseif ($stage == 3 && empty($job->sintering_build_id)) { // pressing
                // Find a build for this device
                $build = Build::where('device_used', $deviceId)->whereNotNull('set_at')->first();
                if ($build) {
                    $job->sintering_build_id = $build->id;
                }
            } elseif ($stage == 4 && empty($job->pressing_build_id)) { // pressing
                // Find a build for this device
                $build = Build::where('device_used', $deviceId)->whereNotNull('set_at')->first();
                if ($build) {
                    $job->pressing_build_id = $build->id;
                }
            }

            // Update job status
            $job->is_active = 1;
            $job->save();
            $jobCount++;

            // Sub-stage logic for main manufacturing stages
            $logStage = $stage;
            $isCompletion = 0;
            if ($stage == 2) {
                $logStage = $this->stageActions['MILLING_START'];
            }
            if ($stage == 3) {
                $logStage = $this->stageActions['PRINTING_START'];
            }
            if ($stage == 4) {
                $logStage = $this->stageActions['SINTERING_START'];
            }
            if ($stage == 5) {
                $logStage = $this->stageActions['PRESSING_START'];
            }
            if ($stage == 8) {
                $logStage = $this->stageActions['DELIVERY_ACCEPT'];
            }
            $logData = [
                'user_id' => Auth::id(),
                'case_id' => $job->case_id,
                'stage' => $logStage,
                'is_completion' => $isCompletion
            ];
            caseLog::create($logData);
        }

        return $jobCount;
    }

    /**
     * Complete jobs and move to next stage
     *
     * @param \Illuminate\Database\Eloquent\Collection $jobs
     * @param int $deviceId
     * @param int $stage
     * @param string $type
     * @param array $options
     * @return int
     */
    private function completeJobs($jobs, int $deviceId, int $stage, string $type, array $options = []): int
    {
        $jobCount = 0;
        $notesSuffix = $options['notes_suffix'] ?? '';
        $stageConfig = self::STAGE_CONFIG[$type]['number'];
        Log::info("completeJobs");
//
//        Log::info($stageConfig);
//        Log::info($notesSuffix);
//        Log::info($jobs);
        $this->caseController->finishCaseStage($jobs[0]->case_id, $stageConfig, false, $jobs);
        foreach ($jobs as $job) {
            $job->is_active = null;
            $job->is_set = null;
            $job->assignee = null;
        }
//        foreach ($jobs as $job) {
//            // Ensure job is at the right stage and is active
//            if ($job->stage != $stage || !$job->is_active) {
//                continue;
//            }
//
//            // Get next stage
//            $nextStage = $this->getJobNextStage($job);
//
//            // Log milling job state transitions for debugging
//            if ($stage == 2) {
//                Log::info('Milling job stage transition:', [
//                    'job_id' => $job->id,
//                    'case_id' => $job->case_id,
//                    'current_stage' => $job->stage,
//                    'next_stage' => $nextStage,
//                    'is_active' => $job->is_active,
//                    'type' => $type
//                ]);
//            }
//
//            // Update job status
//            $job->stage = $nextStage;
//            $job->is_active = null;
//            $job->is_set = null;
//            $job->assignee = null;
//
//            // Ensure build ID is cleared for milling jobs moving to next stage
//            if ($stage == 2) {
//                $job->milling_build_id = null;
//            }
//            // Note: jobs table doesn't have a finished_at column, using updated_at instead
//            // $job->finished_at = now();
//            $job->save();
//            $jobCount++;
//
//            // Sub-stage logic for main manufacturing stages
//            $logStage = $stage;
//            $isCompletion = 1;
//            if ($stage == 2) {
//                $logStage = $this->stageActions['MILLING_COMPLETE'];
//            }
//            if ($stage == 3) {
//                $logStage = $this->stageActions['PRINTING_COMPLETE'];
//            }
//            if ($stage == 4) {
//                $logStage = $this->stageActions['SINTERING_COMPLETE'];
//            }
//            if ($stage == 5) {
//                $logStage = $this->stageActions['PRESSING_COMPLETE'];
//            }
//            if ($stage == 8) {
//                $logStage = $this->stageActions['DELIVERY_COMPLETE'];
//            }
//            $logData = [
//                'user_id' => Auth::id(),
//                'case_id' => $job->case_id,
//                'stage' => $logStage,
//                'is_completion' => $isCompletion
//            ];
//            if (!empty($notesSuffix)) {
//                $logData['notes'] = "Completed {$stageConfig['complete_action']} on {$stageConfig['device_type']}: {$deviceId}{$notesSuffix}";
//            }
//            caseLog::create($logData);
//        }

        return $jobCount;
    }

    /**
     * Assign cases to an employee for any stage
     *
     * @param array $casesIds
     * @param int $employeeId
     * @param int $stage
     * @return void
     */
    private function assignCasesToEmployeeForStage(array $casesIds, int $employeeId, int $stage): void
    {
        // Get existing jobs for these cases at this stage
        $jobs = job::whereIn('case_id', $casesIds)->where('stage', $stage)->get();

        // Create missing jobs if needed
        if ($jobs->count() < count($casesIds)) {
            foreach ($casesIds as $caseId) {
                if (!$jobs->where('case_id', $caseId)->first()) {
                    job::create([
                        'case_id' => $caseId,
                        'stage' => $stage,
                        'assignee' => $employeeId,
                        'is_set' => 1,
                        'is_active' => 1
                    ]);
                }
            }
        }

        // Update existing jobs
        foreach ($jobs as $job) {
            $job->assignee = $employeeId;
            $job->is_set = 1;
            $job->is_active = 1;
            $job->save();
        }
    }

    /**
     * Create a log entry for employee assignment
     *
     * @param int $caseId
     * @param int $employeeId
     * @param User $employee
     * @param int $stage
     * @return void
     */
    private function createEmployeeAssignmentLog(int $caseId, int $employeeId, User $employee, int $stage): void
    {
        // Map stage numbers to stage actions
        $stageActionMap = [
            1 => $this->stageActions['DESIGN_ASSIGN'] ?? ($stage + 0.1),
            2 => $this->stageActions['MILLING_ASSIGN'] ?? ($stage + 0.1),
            3 => $this->stageActions['PRINTING_ASSIGN'] ?? ($stage + 0.1),
            4 => $this->stageActions['SINTERING_ASSIGN'] ?? ($stage + 0.1),
            5 => $this->stageActions['PRESSING_ASSIGN'] ?? ($stage + 0.1),
            6 => $this->stageActions['FINISHING_ASSIGN'] ?? ($stage + 0.1),
            7 => $this->stageActions['QC_ASSIGN'] ?? ($stage + 0.1)
        ];

        $logStage = $stageActionMap[$stage] ?? ($stage + 0.1);

        caseLog::create([
            'user_id' => Auth::id(),
            'case_id' => $caseId,
            'stage' => $logStage,
            'is_completion' => 0,
            'notes' => 'Case assigned to employee: ' . $employee->first_name . ' ' . $employee->last_name
        ]);
    }

    /**
     * Parse checkbox input from form
     *
     * @param mixed $input
     * @return array
     */
    private function parseCheckboxInput($input)
    {
        if (empty($input)) {
            return [];
        }

        // Handle both array and string inputs
        $rawIds = is_array($input) ? $input[0] : $input;
        $ids = explode(",", $rawIds);

        // Remove empty values
        return array_filter($ids);
    }

    /**
     * Assign cases to a delivery driver
     *
     * @param array $casesIds
     * @param int $driverId
     * @return void
     */
    private function assignCasesToDriver(array $casesIds, int $driverId): void
    {
        // Get existing delivery jobs for these cases
        $jobs = job::whereIn('case_id', $casesIds)->where('stage', 8)->get();

        // Create missing jobs if needed
        if ($jobs->count() < count($casesIds)) {
            foreach ($casesIds as $caseId) {
                if (!$jobs->where('case_id', $caseId)->first()) {
                    job::create([
                        'case_id' => $caseId,
                        'stage' => 8, // Delivery stage
                        'assignee' => $driverId,
                        'is_set' => 1,
                        'is_active' => null,
                        'delivery_accepted' => null
                    ]);
                }
            }
        }

        // Update existing jobs
        foreach ($jobs as $job) {
            $job->assignee = $driverId;
            $job->is_set = 1;
            $job->is_active = null;
            $job->delivery_accepted = null;
            $job->save();
        }
    }

    /**
     * Create a log entry for driver assignment
     *
     * @param int $caseId
     * @param int $driverId
     * @param User $driver
     * @return void
     */
    private function createDriverAssignmentLog(int $caseId, int $driverId, User $driver): void
    {
        caseLog::create([
            'user_id' => Auth::id(),
            'case_id' => $caseId,
            'stage' => $this->stageActions['DELIVERY_ASSIGN'],
            'is_completion' => 0,
            'notes' => 'Case assigned to driver: ' . $driver->first_name . ' ' . $driver->last_name
        ]);
    }

    /**
     * Get the next stage for a job
     *
     * @param job $job
     * @return int
     */
    private function getJobNextStage(job $job): int
    {
        $material = $job->material;
        $currentStage = $job->stage;

        /*
         * 1 => Design
         * 2 => Milling
         * 3 => 3D Printing
         * 4 => Sintering Furnace
         * 5 => Press Furnace
         * 6 => Finishing
         * 7 => Quality Control
         * 8 => Delivery
         * -1 => Finished
         */

        if ($material->design && $currentStage < 1) return 1;
        if ($material->mill && $currentStage < 2) return 2;
        if ($material->print_3d && $currentStage < 3) return 3;
        if ($material->sinter_furnace && $currentStage < 4) return 4;
        if ($material->press_furnace && $currentStage < 5) return 5;
        if ($material->finish && $currentStage < 6) return 6;
        if ($material->qc && $currentStage < 7) return 7;
        if ($material->delivery && $currentStage < 8) return 8;

        return -1;
    }

    /**
     * Create a standardized error response
     *
     * @param string $message
     * @return array
     */
    private function errorResponse(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }

    /**
     * Create a standardized success response
     *
     * @param string $message
     * @param array $additionalData
     * @return array
     */
    private function successResponse(string $message, array $additionalData = []): array
    {
        return array_merge(['success' => true, 'message' => $message], $additionalData);
    }
}
