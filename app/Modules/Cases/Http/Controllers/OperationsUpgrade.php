<?php

namespace App\Modules\Cases\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Build;
use App\caseLog;
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
        'metalwork' => [
            'number' => 9,
            'name' => 'Metal Work',
            'set_action' => 'placed',
            'start_action' => 'metalwork',
            'complete_action' => 'metalwork',
            'requires_build_name' => false,
            'device_type' => null,
            'multiple-waiting' => true,
            'multiple-active' => false
        ],
        'finishing' => [
            'number' => 6,
            'name' => 'Finishing',
            'set_action' => 'assigned',
            'start_action' => 'finishing',
            'complete_action' => 'finishing',
            'requires_build_name' => false,
            'device_type' => 'employee',
            'multiple-waiting' => true,
            'multiple-active' => false
        ],
        'qc' => [
            'number' => 7,
            'name' => 'Quality Control',
            'set_action' => 'assigned',
            'start_action' => 'qc',
            'complete_action' => 'qc',
            'requires_build_name' => false,
            'device_type' => 'employee',
            'multiple-waiting' => true,
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
        // Metal Work
        'METALWORK_START' => 9.1,
        'METALWORK_COMPLETE' => 9.2,
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
        Log::error('setMultipleCases is no longer supported after removal of the devices feature.');
        return back()->with('error', 'This feature is no longer supported.');
    }

    /**
     * Route alias for setMultipleCases - used by route: /set-multiple-cases
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
//    public function setOnDevice(Request $request)
//    {
//        return $this->setMultipleCases($request);
//    }

    /**
     * Router alias for setMultipleCases, specifically for setting cases on a printer
     * Used by route: /set-cases-on-printer
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


    /**
     * Generic method to activate multiple cases on a device (start processing)
     * Handles different device types with stage-specific configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


    /*******************************************************************************
     * Activate 3D builds - Specialized version of activateMultipleCases for builds
     */

    /**
     * Generic method to finish multiple cases
     * Handles different device types with stage-specific configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */



    /**
     * Finish 3D builds - Specialized version of finishMultipleCases for builds
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish3DBuilds(Request $request)
    {
        Log::error('finish3DBuilds is no longer supported after removal of the devices feature.');
        return back()->with('error', 'This feature is no longer supported.');
    }

    /**
     * Assign cases to a delivery driver
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignCasesToDelivery(Request $request)
    {


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

            // Invalidate dashboard cache to reflect changes immediately
            \Illuminate\Support\Facades\Cache::increment('dashboard_cache_version');

            return $this->successResponse(
                count($cases) . " case(s) have been assigned to " . $driver->first_name . " and are pending acceptance",
                ['driver_name' => $driver->first_name]
            );
        }, 'admin-dashboard-v2');
    }

    /**
     * Assign cases to an employee at any stage (2-7)
     * This allows authorized users to assign tasks to other employees
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignCasesToEmployee(Request $request)
    {
        return $this->executeTransaction(function () use ($request) {
            // Get stage type from request
            $type = $request->input('type');

            if (empty($type)) {
                return $this->errorResponse('No stage type specified');
            }

            // Get stage configuration
            if (!isset(self::STAGE_CONFIG[$type])) {
                return $this->errorResponse("Invalid stage type: {$type}");
            }

            $stageConfig = self::STAGE_CONFIG[$type];
            $stage = $stageConfig['number'];

            // Get case IDs to assign
            $casesIds = $this->parseCheckboxInput($request->input("WaitingPopupCheckBoxes{$type}"));

            if (empty($casesIds)) {
                return $this->errorResponse('No cases selected');
            }

            // Get employee ID
            $employeeId = $request->input("employeeId-{$type}") ??
                          $request->input("employeeId");

            if (empty($employeeId)) {
                return $this->errorResponse('No employee selected');
            }

            // Get employee info
            $employee = User::find($employeeId);
            if (!$employee) {
                return $this->errorResponse('Invalid employee selected');
            }

            // Verify employee has permission for this stage
            $hasPermission = $this->verifyEmployeeStagePermission($employee, $stage);
            if (!$hasPermission && !$employee->is_admin) {
                return $this->errorResponse(
                    "Employee {$employee->first_name} does not have permission for {$stageConfig['name']}"
                );
            }

            // Verify cases exist
            $cases = sCase::whereIn('id', $casesIds)->get();
            if ($cases->isEmpty()) {
                return $this->errorResponse('No valid cases found');
            }

            // Get device ID if required for this stage
//            $deviceId = $request->input("deviceId-{$type}") ?? $request->input("deviceId");

            // Assign cases to employee at this stage
            $jobCount = $this->assignJobsToEmployee($casesIds, $employeeId, $stage, $type);

            // Log assignment details for debugging
            \Log::info('Cases assigned to employee', [
                'employee_id' => $employeeId,
                'employee_name' => $employee->name_initials,
                'stage' => $stage,
                'case_ids' => $casesIds,
                'job_count' => $jobCount
            ]);

            // Verify jobs were updated by querying fresh from database
            $verifyJobs = job::whereIn('case_id', $casesIds)->where('stage', $stage)->get();
            \Log::info('Jobs after assignment (from database)', [
                'total_jobs' => $verifyJobs->count(),
                'jobs_with_assignee' => $verifyJobs->whereNotNull('assignee')->count(),
                'jobs_with_null_assignee' => $verifyJobs->whereNull('assignee')->count(),
                'jobs' => $verifyJobs->map(function($job) {
                    return [
                        'id' => $job->id,
                        'case_id' => $job->case_id,
                        'assignee' => $job->assignee,
                        'assignee_is_null' => is_null($job->assignee),
                        'is_set' => $job->is_set,
                        'is_active' => $job->is_active
                    ];
                })->toArray()
            ]);

            // Create logs for each case assignment
            foreach ($cases as $case) {
                $this->createEmployeeAssignmentLog($case->id, $employeeId, $employee, $stage);
            }

            // Invalidate dashboard cache to reflect changes immediately
            \Illuminate\Support\Facades\Cache::increment('dashboard_cache_version');

            return $this->successResponse(
                "{$jobCount} case(s) have been assigned to {$employee->first_name} for {$stageConfig['name']}",
                ['employee_name' => $employee->first_name, 'job_count' => $jobCount]
            );
        }, 'admin-dashboard-v2');
    }

    public function bulkAssignPrinting(Request $request)
    {
        return $this->executeTransaction(function () use ($request) {
            $caseIds = $this->parseCheckboxInput($request->input('case_ids'));

            if (empty($caseIds)) {
                return $this->errorResponse('No cases selected.');
            }

            foreach ($caseIds as $caseId) {
                $this->caseController->assignToMe($caseId, 3, false); // 3 = 3D Printing stage, false = no redirect back
            }

            // Invalidate dashboard cache to reflect changes immediately
            \Illuminate\Support\Facades\Cache::increment('dashboard_cache_version');

            return $this->successResponse(count($caseIds) . ' case(s) assigned for 3D Printing.');
        }, 'admin-dashboard-v2');
    }

    public function bulkCompletePrinting(Request $request)
    {
        return $this->executeTransaction(function () use ($request) {
            $caseIds = $this->parseCheckboxInput($request->input('case_ids'));

            if (empty($caseIds)) {
                return $this->errorResponse('No cases selected.');
            }

            foreach ($caseIds as $caseId) {
                $this->caseController->finishCaseStage($caseId, 3, false); // 3 = 3D Printing stage, false = no redirect back
            }

            // Invalidate dashboard cache to reflect changes immediately
            \Illuminate\Support\Facades\Cache::increment('dashboard_cache_version');

            return $this->successResponse(count($caseIds) . ' case(s) completed for 3D Printing.');
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
        try {
            $result = DB::transaction($callback);

            if (isset($result['success']) && !$result['success']) {
                return back()->with('error', $result['message']);
            }

            $message = $result['message'] ?? 'Operation completed successfully';

            if ($redirectRoute) {
                return redirect()->route($redirectRoute)->with('success', $message);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Transaction error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
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
            // REMOVED: Device feature
            // $job->device_id = $deviceId;

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
            if ($stage == 9) {
                $logStage = $this->stageActions['METALWORK_START'];
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

            // REMOVED: Device feature - build lookup by device
            // Ensure the job has the appropriate build ID for its stage
            if ($stage == 2 && empty($job->milling_build_id)) { // Milling
                // Find a build (not device-specific anymore)
                $build = Build::whereNotNull('set_at')->first();
                if ($build) {
                    $job->milling_build_id = $build->id;
                }
            } elseif ($stage == 3 && empty($job->printing_build_id)) { // 3D Printing
                // Find a build (not device-specific anymore)
                $build = Build::whereNotNull('set_at')->first();
                if ($build) {
                    $job->printing_build_id = $build->id;
                }
            } elseif ($stage == 3 && empty($job->sintering_build_id)) { // pressing
                // Find a build (not device-specific anymore)
                $build = Build::whereNotNull('set_at')->first();
                if ($build) {
                    $job->sintering_build_id = $build->id;
                }
            } elseif ($stage == 4 && empty($job->pressing_build_id)) { // pressing
                // Find a build (not device-specific anymore)
                $build = Build::whereNotNull('set_at')->first();
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
            if ($stage == 9) {
                $logStage = $this->stageActions['METALWORK_START'];
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
        if ($material->metal_work && $currentStage < 9) return 9;

        // Furnace stages (4,5,9) are mutually exclusive and all come BEFORE finish/qc/delivery
        $furnaceStages = [4, 5, 9];
        if ($material->finish && ($currentStage < 6 || in_array($currentStage, $furnaceStages))) return 6;
        if ($material->qc && ($currentStage < 7 || in_array($currentStage, $furnaceStages))) return 7;
        if ($material->delivery && ($currentStage < 8 || in_array($currentStage, $furnaceStages))) return 8;

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

    /**
     * Verify that an employee has permission for a specific stage
     *
     * @param User $employee
     * @param int $stage
     * @return bool
     */
    private function verifyEmployeeStagePermission(User $employee, int $stage): bool
    {
        // Admin always has permission
        if ($employee->is_admin) {
            return true;
        }

        // Map stages to permission IDs
        $stagePermissionMap = [
            2 => 2,  // Milling
            3 => 3,  // 3D Printing
            4 => 4,  // Sintering
            5 => 5,  // Pressing
            9 => 5,  // Metal Work (shares pressing permission)
            6 => 6,  // Finishing
            7 => 7,  // Quality Control
            8 => 8,  // Delivery
        ];

        if (!isset($stagePermissionMap[$stage])) {
            return false;
        }

        $requiredPermissionId = $stagePermissionMap[$stage];

        // Check user permissions from cache or database
        $permissions = \Illuminate\Support\Facades\Cache::get('user' . $employee->id);

        if (!$permissions) {
            // If not in cache, load from database
            $permissions = $employee->permissions;
        }

        return $permissions->contains('permission_id', $requiredPermissionId);
    }

    /**
     * Assign jobs to an employee for a specific stage
     *
     * @param array $casesIds
     * @param int $employeeId
     * @param int $stage
     * @param string $type
     * @param int|null $deviceId
     * @return int Number of jobs assigned
     */
    private function assignJobsToEmployee(array $casesIds, int $employeeId, int $stage, string $type, ?int $deviceId = null): int
    {
        $jobCount = 0;

        // Get existing jobs for these cases at this stage
        $jobs = job::whereIn('case_id', $casesIds)->where('stage', $stage)->get();

        // Create missing jobs if needed
        if ($jobs->count() < count($casesIds)) {
            foreach ($casesIds as $caseId) {
                if (!$jobs->where('case_id', $caseId)->first()) {
                    $newJob = job::create([
                        'case_id' => $caseId,
                        'stage' => $stage,
                        'assignee' => $employeeId,
                        'is_set' => 1,
                        'is_active' => 1, // Make cases active immediately
                    ]);

                    // Add device_id if provided
                    if ($deviceId) {
                        $newJob->device_id = $deviceId;
                        $newJob->save();
                    }

                    $jobCount++;
                }
            }

            // Reload jobs collection to include newly created ones
            $jobs = job::whereIn('case_id', $casesIds)->where('stage', $stage)->get();
        }

        // Update all jobs with the assignee
        foreach ($jobs as $job) {
            $job->assignee = $employeeId;
            $job->is_set = 1;
            $job->is_active = 1; // Make cases active immediately

            if ($deviceId) {
                $job->device_id = $deviceId;
            }

            $job->save();
            $jobCount++;
        }

        return $jobCount;
    }

    /**
     * Create a log entry for employee assignment at a specific stage
     *
     * @param int $caseId
     * @param int $employeeId
     * @param User $employee
     * @param int $stage
     * @return void
     */
    private function createEmployeeAssignmentLog(int $caseId, int $employeeId, User $employee, int $stage): void
    {
        // Determine the appropriate substage for logging
        $logStage = $stage;
        $isCompletion = 0;

        // Map to substages for manufacturing stages
        if ($stage == 2) {
            $logStage = $this->stageActions['MILLING_SET'];
        } elseif ($stage == 3) {
            $logStage = $this->stageActions['PRINTING_SET'];
        } elseif ($stage == 4) {
            $logStage = $this->stageActions['SINTERING_SET'];
        } elseif ($stage == 5) {
            $logStage = $this->stageActions['PRESSING_START'];
        } elseif ($stage == 9) {
            $logStage = $this->stageActions['METALWORK_START'];
        } elseif ($stage == 8) {
            $logStage = $this->stageActions['DELIVERY_ASSIGN'];
        }

        caseLog::create([
            'user_id' => Auth::id(),  // Who performed the assignment
            'case_id' => $caseId,
            'stage' => $logStage,
            'is_completion' => $isCompletion,
            'notes' => 'Case assigned to: ' . $employee->first_name . ' ' . $employee->last_name . ' (by ' . Auth::user()->first_name . ')'
        ]);
    }
}
