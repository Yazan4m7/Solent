<?php

namespace App\Modules\Cases\Http\Controllers;

use App\Http\Controllers\Controller;

//require 'vendor/autoload.php';

use App\abutment;
use App\abutmentDeliveryRecord;
use App\Build;
use App\discount;
use App\failureLog;
use App\implant;
use App\MobileNotificationToken;
use App\permission;
use App\UserPermission;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Cache;
use App\client;
use App\material;
use App\JobType;
use App\sCase;
use App\file;
use App\note;
use App\job;
use App\invoice;
use App\impressionType;
use App\materialJobtype;
use App\tag;
use App\caseLog;
use App\User;
use App\lab;
use App\editLog;
use App\Http\Traits\helperTrait;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;
use Log;


class CaseController extends Controller
{
    public function __construct()
    {

    }




    use helperTrait;

    // Sub-stage action mapping for main manufacturing stages
    private array $stageActions = [
        // Milling
        'MILLING_SET'      => 2.1,
        'MILLING_START'    => 2.2,
        'MILLING_COMPLETE' => 2.3,
        // 3D Printing
        'PRINTING_SET'      => 3.1,
        'PRINTING_START'    => 3.2,
        'PRINTING_COMPLETE' => 3.3,
        // Sintering
        'SINTERING_SET'      => 4.1,
        'SINTERING_START'    => 4.2,
        'SINTERING_COMPLETE' => 4.3,
        // Pressing
        'PRESSING_START'    => 5.1,
        'PRESSING_COMPLETE' => 5.2,
        // Delivery
        'DELIVERY_ASSIGN'   => 8.1,
        'DELIVERY_ACCEPT'   => 8.2,
        'DELIVERY_COMPLETE' => 8.3,
    ];

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        // Set date range
        if ($request->from && $request->to) {
            $from = $request->from;
            $to = $request->to;
        } else {
            $from = date('Y-m-d', strtotime('first day of this month'));
            $to = now()->toDateString();
        }

        // Determine which date column to filter by
        $dateColumn = 'created_at'; // Default
        if ($request->date_column) {
            if (in_array($request->date_column, ['initial_delivery_date', 'actual_delivery_date', 'created_at'])) {
                $dateColumn = $request->date_column;
            }
        }

        // Build query based on selected doctors
        $query = sCase::select(['id', 'patient_name', 'initial_delivery_date', 'actual_delivery_date', 'doctor_id', 'created_at'])
            ->whereBetween($dateColumn, [$from . ' 00:00', $to . ' 23:59']);

        // Filter by doctor if specified
        if (isset($request->doctor) && !(isset($request->doctor[0]) && $request->doctor[0] === 'all')) {
            $query->whereIn('doctor_id', $request->doctor);
        }

        // Order results and limit to prevent excessive memory usage
        $cases = $query->orderByRaw('CASE WHEN actual_delivery_date IS NULL THEN 0 ELSE 1 END, COALESCE(actual_delivery_date, initial_delivery_date)')
            ->limit(500)
            ->get();

        // Load relationships using eager loading with specific columns to reduce memory usage
        $cases->load([
            'notes:id,case_id,note,created_at,written_by',
            'tags:id,case_id,tag_id'
        ]);

        $selectedClients = $request->doctor;
        $clients = client::select(['id', 'name'])->get();

        // Pass all necessary data to the view
        return view('cases.index', compact('cases', 'from', 'to', 'selectedClients', 'clients', 'dateColumn'));
    }

    public function view($id, $stage = -2)
    {
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active', '!=', 0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $implants = implant::all();
        $abutments = abutment::all();
        $tags = tag::where('hidden', 0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        return view('cases.viewOnly', compact('case', 'clients',
            'implants', 'abutments', 'materials', 'types', 'impressionTypes', 'tags', 'tagsAsArray', 'jobTypeMaterials', 'stage'));
    }

    // returns the view to create the case
    public function create()
    {
        $doctors = client::where('active', '!=', 0)->get();
        $materials = material::all();
        $types = JobType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden', 0)->get();
        $impressionTypes = impressionType::all();
        $implants = implant::all();
        $abutments = abutment::all();

        $year = now()->year;
        $month = now()->month;

        if (!\Illuminate\Support\Facades\Schema::hasTable('monthly_case_counters')) {
            DB::statement('CREATE TABLE monthly_case_counters (id INT AUTO_INCREMENT PRIMARY KEY, year INT NOT NULL, month INT NOT NULL, counter INT NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
        }

        $counter = DB::table('monthly_case_counters')
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($counter) {
            $counterValue = $counter->counter + 1;
        } else {
            $counterValue = 1;
        }

        $tempCaseId = now()->format('M-d') . '-' . $counterValue;

        return view("cases.create", compact('doctors', 'materials', 'implants', 'abutments', 'types', 'jobTypeMaterials', 'tags', 'impressionTypes', 'tempCaseId'));
    }

    // takes inputs and creates a new case
    public function returnCreate(Request $request)
    {
        DB::beginTransaction();

        /*
        *     CASE BASIC INFO
        */
        $case = new sCase();
        $latestCase = sCase::whereYear('created_at', '=', now()->year)
            ->whereMonth('created_at', '=', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $nextId = 1;
        if($latestCase) {
            $parts = explode('-', $latestCase->case_id);
            if (count($parts) === 3) {
                $nextId = (int)$parts[2] + 1;
            }
        }

        $case->case_id = now()->format('M-d') . '-' . $nextId;
        $case->patient_name = $request->patient_name;
        $case->doctor_id = $request->doctor;
        $case->impression_type = $request->impression_type;
        $case->initial_delivery_date = $request->delivery_date;
        $case->created_by = Auth()->user()->id;
        $case->save();


        /*  SAVING TAGS  */

        if ($request->tags)
            foreach ($request->tags as $tag) {
                $this->createTag($case, $tag);

            }
        //dd($request->repeat);
        /*  STORING JOBS */
        if ($request->repeat)
            foreach ($request->repeat as $job) {
                try {

                    //if(!isset($job["units"])) continue;
                    if (isset($job["units"])) {
                        $newJob = new job(['unit_num' => $job["units"],
                            'type' => $job["jobType"],
                            'color' => $job["color"],
                            'style' => $job["style"] ?? 'None',
                            'abutment' => $job["abutment"] ?? '0',
                            'implant' => $job["implant"] ?? '0',
                            'material_id' => $job["material_id"],
                            'case_id' => $case->id,
                            'doctor_id' => $request->doctor,
                            'stage' => 1]);
                        $newJob->save();
                        $newJob->unit_price = material::FindOrFail($job["material_id"])->price - ($this->getDiscount($newJob, $case) / count(explode(',', $newJob->unit_num)));
                        $newJob->save();
                    }
                    if (isset($job['abutments'])) { // dd($job);
                        foreach ($job['abutments'] as $abut) {
                            $record = new abutmentDeliveryRecord();
                            $record->case_id = $case->id;
                            $record->job_id = $newJob->id;
                            $record->abutment_id = $abut["abutment"] ?? '0';
                            $record->implant_id = $abut["implant"] ?? '0';
                            $record->code = $abut["abutmentCode"] ?? '0';
                            $units = implode(',', $abut["abutmentUnits"] ?? [0]);
                            $record->units = $units;
                            $record->qty = count($abut["abutmentUnits"] ?? []);
                            $record->remaining_qty = count($abut["abutmentUnits"] ?? []);
                            if ($record->abutment_id != '0')
                                $record->save();
                        }
                    }

                } catch (\Exception $e) {
                    $request->flash();
                    return back()->with('error', "Something went Wrong :( ");

                }
                if (isset($newJob))
                    if ($newJob->material->id != 6) {
                        $newJob->implant = null;
                        $newJob->abutment = null;
                        $newJob->save();
                    }
            }


        /*
        *     STORING IMAGES
        */

        if ($files = $request->file('images')) {

            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $file->move('caseImages/' . $case->id . '/', $name);


                $newFile = new file();
                $newFile->path = 'caseImages/' . $case->id . '/' . $name;
                $newFile->case_id = $case->id;
                $newFile->added_by = Auth()->user()->id;
                $newFile->save();

            }
            $this->createTag($case, 2);
        }

        if (isset($request->discountCB)) {
            $discount = new discount();
            $discount->discount = $request->discount_amount;
            $discount->case_id = $case->id;
            $discount->reason = $request->discount_reason;
            $discount->save();

            if ($discount->discount == 0) $discount->delete();
        }

        /*
        *     SAVING THE NOTE
        */

        if ($request->note) {
            $note = new note();
            $note->case_id = $case->id;
            $note->note = $request->note;
            $note->written_by = Auth()->user()->id;
            $note->save();
            $this->createTag($case, 5);
        }

        //DB::rollBack();
        DB::commit();
        return redirect('operations-dashboard')->with('success', "Case Saved Successfully!");
//        return redirect('operations-dashboard');
//        return back()->with('success', "Case Saved Successfully!");
    }

    public function returnEdit($id)
    {
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active', '!=', 0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden', 0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        $stage = -2;
        $implants = implant::all();
        $abutments = abutment::all();
        return view('cases.edit-case', compact('case', 'clients', 'implants', 'abutments', 'materials', 'types', 'impressionTypes', 'tags', 'tagsAsArray', 'jobTypeMaterials', 'stage'));
    }

    public function edit(Request $request)
    {
        //dd($request);

        $debug = "";
        $doctor = client::where('id', $request->doctor)->first();
        $case = sCase::where('id', $request->id)->first();
        $permissions = Cache::get('user' . Auth()->user()->id);

        // if ($this->isCaseFinished($case->id))
        // abort(403, "Editing completed cases currently disabled - 12012022");

        if (isset($case->actual_delivery_date))
            if (!Auth()->user()->is_admin && !$permissions->contains('permission_id', 115))
                return back()->with('error', "You're not authorized to edit completed cases");

        if (!$doctor) {
            return back()->with('error', "Doctor not found");
        }
        $transaction = DB::transaction(function () use ($request, $doctor) {
            $case = sCase::where('id', $request->id)->first();
            if (!$case) {
                return false;
            }
            $case->case_id = $request->caseId1 . $request->caseId2 . $request->caseId3 . '_' . $request->caseId4;
            $case->patient_name = $request->patient_name;
            $case->doctor_id = $request->doctor;
            $case->impression_type = $request->impression_type;
            $case->initial_delivery_date = $request->delivery_date;

            $jobsLeftInTheForm = array();
            if ($request->repeat)
                foreach ($request->repeat as $job) {
                    $jobId = $job["job_id"];

                    array_push($jobsLeftInTheForm, $jobId);
                    if (isset($job["material_id" . $jobId])) {
                        $job2 = job::where('id', $jobId)->first();
                        $job2->update(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId],
                            'color' => $job["color" . $jobId] ?? 'None', 'style' => $job["style" . $jobId] ?? 'None',
                            'abutment' => $job["abutment" . $jobId] ?? '0', 'implant' => $job["implant" . $jobId] ?? '0',
                            'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor,
                        ]);
                        $job2->unit_price = material::FindOrFail($job["material_id" . $jobId])->price - ($this->getDiscount($job2, $case) / count(explode(',', $job2->unit_num)));
                        $job2->save();
                    } else {
                        $job2 = job::where('id', $jobId)->first();
                        $job2->update(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId], 'color' => $job["color" . $jobId], 'style' => $job["style" . $jobId] ?? 'None', 'abutment' => null, 'implant' => null, 'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor]);
                        $job2->unit_price = material::FindOrFail($job["material_id" . $jobId])->price - ($this->getDiscount($job2, $case) / count(explode(',', $job2->unit_num)));
                        $job2->save();
                    }
                }
            //dd( $case->jobs()->whereNotIn('id',$jobsLeftInTheForm)->get());
            // if no jobs left in the repeater, delete all jobs
            // if(!$request->repeat)
            //$case->jobs()->delete();
            // else
            // delete jobs and that was deleted using the "delete" repeater btn
            foreach ($case->jobs()->whereNotIn('id', $jobsLeftInTheForm)->get() as $job) {
                abutmentDeliveryRecord::where('job_id', $job->id)->delete();
                //$job->abutmentDelivery->each->delete();
                $job->delete();
            }


            /*
            *   NEW JOBS
            */
            $i = 0;
            // dd($request->repeat2);
            if ($request->repeat2)
                foreach ($request->repeat2 as $job) {
                    if (isset($job["units"])) {
                        $i++;

                        $newJob = new job();
                        $newJob->unit_num = $job["units"];
                        $newJob->type = $job["jobType"];
                        $newJob->color = $job["color"] ?? 'None';
                        $newJob->style = $job["style"] ?? 'None';
                        $newJob->abutment = $job["abutment"] ?? '0';
                        $newJob->implant = $job["implant"] ?? '0';
                        $newJob->material_id = $job["material_id"];
                        $newJob->case_id = $case->id;

                        $newJob->save();
                        $newJob->unit_price = material::FindOrFail($job["material_id"])->price - ($this->getDiscount($newJob, $case) / count(explode(',', $newJob->unit_num)));
                        if ($this->isCaseFinished($case->id)) {
                            $newJob->stage = -1;
                            $newJob->save();
                        }
//                    else if (!isset($job['autoStageDetect']) && isset($job["newJobStage"]) && $job["newJobStage"] != 0) {
//                        $newJob->stage = $job["newJobStage"];
//                       // dd($job["newJobStage"]);
//
//
                        else {
                            $newJob->stage = $this->lowestJobStageApplicable($newJob, $case->id);
                            $newJob->save();
                        }

                        if (isset($job['abutments'])) {
                            foreach ($job['abutments'] as $abut) {
                                $record = new abutmentDeliveryRecord();
                                $record->case_id = $case->id;
                                $record->job_id = $newJob->id;
                                $record->abutment_id = $abut["abutment"] ?? '0';
                                $record->implant_id = $abut["implant"] ?? '0';
                                $record->code = $abut["abutmentCode"] ?? '0';
                                $units = implode(',', $abut["abutmentUnits"] ?? [0]);
                                $record->units = $units;
                                $record->qty = count($abut["abutmentUnits"] ?? []);
                                $record->remaining_qty = count($abut["abutmentUnits"] ?? []);
                                if ($record->abutment_id != '0')
                                    $record->save();
                            }
                        }
                        if ($newJob->material->teeth_or_jaw == 1) {
                            $newJob->implant = null;
                            $newJob->abutment = null;
                            $newJob->save();
                        }
                        foreach ($case->jobs as $jobItem)
                            if ($jobItem->stage == $newJob->stage) {
                                $jobItem->assignee = null;
                                $jobItem->delivery_accepted = null;
                                // $jobItem->save();
                            }
                    }


                }


            if (isset($request->discountCB)) {
                $discount = discount::where('case_id', $case->id)->first() ?? new discount();
                $discount->discount = $request->discount_amount;
                $discount->case_id = $case->id;
                $discount->reason = $request->discount_reason;
                $discount->save();
                if ($discount->discount == 0) $discount->delete();
            } else if (isset($case->discount))
                discount::where('case_id', $case->discount->case_id)->forceDelete();

            if ($request->newNote) {
                $note = new note();
                $note->case_id = $case->id;
                $note->note = $request->newNote;
                $note->written_by = Auth()->user()->id;
                $note->save();
                $this->createTag($case, 5);
            }


            if ($request->tags) {
                //caseTag::where('case_id', $request->id)->delete();
                foreach ($request->tags as $tag) {
                    $this->createTag($case, $tag);
                }
            }
            if ($files = $request->file('images')) {

                foreach ($files as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move('caseImages/' . $case->id . '/', $name);


                    $newFile = new file();
                    $newFile->path = 'caseImages/' . $case->id . '/' . $name;
                    $newFile->case_id = $case->id;
                    $newFile->added_by = Auth()->user()->id;
                    $newFile->save();
                }
                $this->createTag($case, 2);
            }
            $case->save();
            return true;
        });
        if ($transaction) {
            if (isset($case->invoice)) {
                $this->reflectCaseChanges($request->id);
                $debug = "invoice updated";
            }
            return back()->with('success', 'Case has been updated successfully ');
        } else {
            return back()->with('error', 'Something went wrong');
        }
    }

    public function reflectCaseChanges($caseId)
    {
        $case = sCase::where('id', $caseId)->first();
        if (!$case) return back()->with('error', 'Case was not found while reflecting changes, err004');

        $oldInvoiceAmount = $case->invoice->amount;
        $invoiceAmount = 0;
        foreach ($case->jobs as $job) {
            if ($job->is_repeat == 1 || $job->is_repeat == '1' || $job->is_modification == 1 || $job->is_modification == '1')
                continue;
            $jobPrice = (count(explode(',', $job->unit_num)) * $job->material->price) - $this->getDiscount($job, $case);
            $invoiceAmount += $jobPrice;
        }
        $invoice = invoice::where('case_id', $caseId)->first();
        if (isset($case->discount)) {
            $invoice->amount_before_discount = $invoiceAmount;
            $invoice->amount = $invoiceAmount - $case->discount->discount;
        } else {
            $invoice->amount = $invoiceAmount;
            $invoice->amount_before_discount = $invoiceAmount;
        }
        $invoice->doctor_id = $case->doctor_id;
        $invoice->save();

        // if case is delivered, adjust doctor balance accordingly
        if ($invoice->status == 1) {
            $doctor = client::findOrFail($case->doctor_id);
            $doctor->balance -= $oldInvoiceAmount;
            $doctor->balance += $invoice->amount;
            $doctor->save();
        }
    }

    public function addNote(Request $request)
    {
        $note = new note();
        $note->case_id = $request->case_id_for_note;
        $note->note = $request->newNote;
        $note->written_by = Auth()->user()->id;
        $note->save();
        $case = sCase::findOrFail($request->case_id_for_note);
        $this->createTag($case, 5);
        return back()->with('success', 'Note added successfully ');
    }

    public function moveJobsToNextStage(Request $request)
    {
        $case = sCase::where('id', $request->case_id)->first();
        if (!$case) {
            return back()->with('Case Not found');
        }
    }

    public function employeeDashboard($stage)
    {

        $drivers = User::whereHas('permissions', function ($q) {
            $q->whereIn('permission_id', array(8));
        })->orWhere("is_admin", 1)->get();

        // Delivery dashboard
        if ($stage == 8) {
            $jobs = job::select('case_id', 'assignee', 'delivery_accepted', 'stage')->whereIn("stage", [8, -1])->distinct()->get();
            //->where('voucher_status','!=',4)
            $activeCases = sCase::whereIn('id', $jobs->where("delivery_accepted", Auth()->user()->id)->where('stage', 8)->pluck("case_id")->toArray())->whereNull("voucher_recieved_by")->get();

            // Query waiting cases - get both cases where user is assigned via assignee field OR where jobs have this assignee
            $jobsAssignedToMe = $jobs->where('assignee', Auth()->user()->id)->whereNull("delivery_accepted")->where('stage', 8)->pluck("case_id")->toArray();

            // Add cases where a job has delivery_assignee equal to current user
            $jobsWithDeliveryAssignee = job::where('delivery_assignee', Auth()->user()->id)
                                          ->whereNull('delivery_accepted')
                                          ->where('stage', 8)
                                          ->pluck('case_id')
                                          ->toArray();

            // Combine both arrays and remove duplicates
            $assignedCaseIds = array_unique(array_merge($jobsAssignedToMe, $jobsWithDeliveryAssignee));

            $waitingCases = sCase::whereIn('id', $assignedCaseIds)->whereNull("voucher_recieved_by")->get();
            $deliveredCases = sCase::whereIn('id', $jobs->whereNull('assignee')->where("delivery_accepted", Auth()->user()->id)->where('stage', -1)->pluck("case_id")->toArray())->whereNull("voucher_recieved_by")->get();

            \Log::info('Driver dashboard - waiting cases query', [
                'driverId' => Auth()->user()->id,
                'jobsAssignedToMe' => $jobsAssignedToMe,
                'jobsWithDeliveryAssignee' => $jobsWithDeliveryAssignee,
                'waitingCasesCount' => $waitingCases->count()
            ]);

            return view('delivery.driver-dashboard', compact('activeCases', 'waitingCases', 'jobs', 'stage', 'drivers', 'deliveredCases'));
        }

        $currentUserId = Auth()->user()->id;
        $permissions = Cache::get('user' . $currentUserId);
        $isAdmin = Auth()->user()->is_admin == 1 || ($permissions && $permissions->contains('permission_id', 200));


        // Get all jobs for the current stage
        $jobs = job::where('stage', $stage)->get();

        // Get active cases assigned to the current user
        $activeCases = sCase::whereIn('id', $jobs->where('assignee', $currentUserId)->where('is_active', 1)->pluck('case_id'))->get();

        // Get waiting cases assigned to the current user
        $waitingCases = sCase::whereIn('id', $jobs->where('assignee', $currentUserId)->where('is_active', '!=', 1)->pluck('case_id'))->get();

        if($isAdmin){
            // Get unassigned cases
            if ($stage == 1) {
                $unassignedCases = sCase::doesntHave('jobs')->orWhereIn('id', $jobs->whereNull('assignee')->pluck('case_id'))->get();
            } else {
                $unassignedCases = sCase::whereIn('id', $jobs->whereNull('assignee')->pluck('case_id'))->get();
            }
            $waitingCases = $waitingCases->merge($unassignedCases);
        }


        if (!$activeCases && !$waitingCases) {
            return back()->with('No Cases found for you :)\
');
        }

        if ($stage == 7) {
            $drivers = User::whereHas('permissions', function ($q) {
                $q->whereIn('permission_id', array(8));
            })->orWhere("is_admin", 1)->get();
            return view('generic.emp-cases', compact('activeCases', 'waitingCases', 'stage', 'jobs', 'drivers'));
        }
        if ($stage == 2) {
            $labs = lab::all();
            return view('generic.emp-cases', compact('activeCases', 'waitingCases', 'stage', 'jobs', 'drivers', 'labs'));
        }

        return view('generic.emp-cases', compact('activeCases', 'waitingCases', 'stage', 'jobs', 'drivers'));
    }

    function setUserPermissions()
    {

        if (!Cache::has('user' . Auth::user()->id)) {
            $permissions =  UserPermission::where('user_id', Auth::user()->id)->get();
            Cache::forever('user' . Auth::user()->id, $permissions);
        }
    }

    public function adminDashboard_v2()
    {
        /////////////////////////////////////////////
        //////// MAIN ENTRY POINT OF APPLICATION !!!!
        /// /////////////////////////////////////////

        $this->setUserPermissions();
        $permissions = Cache::get('user' . Auth::user()->id);
        $currentUserId = Auth()->user()->id;
        $isAdmin = Auth()->user()->is_admin == 1 || ($permissions && $permissions->contains('permission_id', 122));

        // Start a timer to measure execution time
        $startTime = microtime(true);

        // Cache key for dashboard data (5-minute cache)
        $cacheVersion = Cache::get('dashboard_cache_version', 1);
        $cacheKey = 'dashboard_data_v' . $cacheVersion . '_' . $currentUserId . '_' . ($isAdmin ? 'admin' : 'user');

        // Try to get data from cache first
        if (Cache::has($cacheKey)) {
            $dashboardData = Cache::get($cacheKey);

            // Extract variables from cached data
            extract($dashboardData);

            // Log cache hit
            \Log::info("Dashboard loaded from cache in " . (microtime(true) - $startTime) . " seconds");
        } else {

            // Optimized: Get all cases with all necessary relationships in one query
            $allCases = sCase::with([
                'client:id,name',
                'jobs' => function ($q) {
                    $q->select('id', 'unit_num', 'case_id', 'stage', 'assignee', 'is_active', 'is_set',  'type', 'material_id', 'color', 'style', 'printing_build_id', 'delivery_accepted');
                },
                'jobs.material:id,name,count_as_unit',
                'jobs.jobType:id,name,a_secondary_item',
                'jobs.assignedTo:id,name_initials',
                'jobs.implantR:id,name',
                'jobs.abutmentR:id,name',
                'abutmentsDeliveries:id,case_id,status',
                'tags.originalTagRecord:id,text,color,icon',
                'notes.writtenBy:id,name_initials'
            ])
            ->whereHas('jobs', function ($q) {
                $q->whereIn('stage', [1, 2, 3, 4, 5, 6, 7, 8]);
            })
            ->get();

            // Apply different queries for admin vs normal users
            if ($isAdmin) {
                $aDesign = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 1)->whereNotNull('assignee')->isNotEmpty();
                });
                $aMilling = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 2)->whereNotNull('assignee')->isNotEmpty();
                });
                $aPrinting = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 3)->whereNotNull('assignee')->isNotEmpty();
                });
                $aSintering = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 4)->whereNotNull('assignee')->isNotEmpty();
                });
                $aPressing = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 5)->whereNotNull('assignee')->isNotEmpty();
                });
                $aFinishing = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 6)->whereNotNull('assignee')->isNotEmpty();
                });
                $aQC = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 7)->whereNotNull('assignee')->isNotEmpty();
                });
                $aDelivery = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 8)->whereNotNull('delivery_accepted')->isNotEmpty();
                });

                $wDesign = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 1)->whereNull('assignee')->isNotEmpty();
                });
                $wMilling = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 2)->whereNull('assignee')->isNotEmpty();
                });
                $wPrinting = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 3)->whereNull('assignee')->isNotEmpty();
                });
                $wSintering = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 4)->whereNull('assignee')->isNotEmpty();
                });
                $wPressing = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 5)->whereNull('assignee')->isNotEmpty();
                });
                $wFinishing = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 6)->whereNull('assignee')->isNotEmpty();
                });
                $wQC = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 7)->whereNull('assignee')->isNotEmpty();
                });
            } else {
                // User-specific queries using collection filtering
                $aDesign = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 1)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aMilling = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 2)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aPrinting = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 3)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aSintering = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 4)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aPressing = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 5)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aFinishing = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 6)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aQC = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 7)->where('assignee', $currentUserId)->isNotEmpty();
                });
                $aDelivery = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 8)->where('assignee', $currentUserId)->whereNotNull('delivery_accepted')->isNotEmpty();
                });

                // For non-admins, waiting tabs are for cases assigned to them but not yet active.
                // With the new logic, there are no waiting cases for non-admins.
                $wDesign = collect();
                $wMilling = collect();
                $wPrinting = collect();
                $wSintering = collect();
                $wPressing = collect();
                $wFinishing = collect();
                $wQC = collect();
            }

            // Optimized: Delivery cases using collection filtering
            if ($isAdmin || $permissions->contains('permission_id', 129)) {
                $wDelivery = $allCases->filter(function ($case) {
                    return $case->jobs->where('stage', 8)->whereNull('delivery_accepted')->isNotEmpty();
                });
            } else {
                $wDelivery = $allCases->filter(function ($case) use ($currentUserId) {
                    return $case->jobs->where('stage', 8)->where('assignee', $currentUserId)->whereNull('delivery_accepted')->isNotEmpty();
                });
            }

            // REMOVED: Device feature - no longer loading device data
            // $devices = collect(device::select('id', 'name', 'type', 'img', 'sorting_order', 'hidden')->get()->toArray());
//            $devices = collect([]); // Empty collection

            // REMOVED: Device stats calculation
            // $deviceStats = job::selectRaw(...)->whereIn('device_id', $devices->pluck('id'))->groupBy('device_id')->get()->keyBy('device_id');

            // Initialize empty deviceUnitsCounts array
            $deviceUnitsCounts = [];

            // REMOVED: Device loop - no longer calculating device unit counts
            /* foreach ($devices as $device) {
                $deviceId = $device['id'];
                $deviceType = $device['type'];
                $deviceModel = new device();
                $deviceModel->fill($device);
                $deviceModel->exists = true;
                $deviceModel->id = $deviceId;
                $deviceModel->type = $deviceType;

                foreach ([ 2, 3, 4, 5,] as $stage) {
                    $deviceUnitsCounts[$deviceId][$stage]['waiting'] = $deviceModel->countOfUnits($stage, false);
                    $deviceUnitsCounts[$deviceId][$stage]['active'] = $deviceModel->countOfUnits($stage, true);
                }

                $deviceUnitsCounts[$deviceId]['waitingBuilds'] = Build::where('device_used', $deviceId)
                    ->whereNotNull('set_at')->whereNull('finished_at')->whereNull('started_at')->count() ;

                $deviceUnitsCounts[$deviceId]['activeBuilds'] =  Build::where('device_used', $deviceId)
                    ->whereNotNull('set_at')->whereNotNull('started_at')->whereNull('finished_at')->count() ;

                $stats = $deviceStats->get($deviceId);
                // Note: We can't set properties on arrays, so we'll skip these assignments
                // $device->jobsWaiting = $stats ? $stats->waiting_count : 0;
                // $device->jobsSet = $stats ? $stats->set_count : 0;
                // $device->jobsActive = $stats ? $stats->active_count : 0;
            } */
            // END REMOVED: Device loop

            $drivers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 8);
            })->whereHas('permissions', function ($q) {
                $q->where('permission_id', 131);
            })->get();

            // Get users for each stage assignment
            $designers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 1); // Designer permission
            })->get();

            $millers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 2); // Miller permission
            })->get();

            $printers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 3); // 3D Printer permission
            })->get();

            $sinteringUsers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 4); // Sintering permission
            })->get();

            $pressingUsers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 5); // Pressing permission
            })->get();

            $finishingUsers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 6); // Finishing permission
            })->get();

            $qcUsers = User::whereHas('permissions', function ($q) {
                $q->where('permission_id', 7); // QC permission
            })->get();

            $labs = lab::all();

            // Store all the dashboard data in the cache for 5 minutes
            $dashboardData = compact(
                'labs', 'wDesign', 'aDesign',
                'wMilling', 'aMilling', 'wPrinting', 'aPrinting',
                'wSintering', 'aSintering', 'wPressing', 'aPressing',
                'wFinishing', 'aFinishing', 'wQC', 'aQC', 'wDelivery',
                'aDelivery', 'drivers',  'deviceUnitsCounts',
                'designers', 'millers', 'printers', 'sinteringUsers',
                'pressingUsers', 'finishingUsers', 'qcUsers'
            );

            Cache::put($cacheKey, $dashboardData, now()->addMinutes(5));
        }

        $activeOuterTab = $_COOKIE['activeOuterTab'] ?? "";

        // Log execution time - can be removed in production
        $executionTime = microtime(true) - $startTime;
        \Log::info("Dashboard loaded in {$executionTime} seconds");

        return view('cases.admin-dashboardv2', compact(
            'labs', 'wDesign', 'aDesign',
            'wMilling', 'aMilling', 'wPrinting', 'aPrinting',
            'wSintering', 'aSintering', 'wPressing', 'aPressing',
            'wFinishing', 'aFinishing', 'wQC', 'aQC', 'wDelivery',
            'aDelivery', 'drivers', 'designers', 'millers', 'printers',
            'sinteringUsers', 'pressingUsers', 'finishingUsers', 'qcUsers',
            'activeOuterTab', 'deviceUnitsCounts', 'permissions'
        ));
    }

    public function numOfCasesBefore($dayToSubtract, $cases)
    {
        return $cases->whereBetween('created_at', [today()->subDays($dayToSubtract)->toDateString() . ' 00:00', today()->subDay($dayToSubtract - 1)->toDateString() . ' 23:59'])->count();

    }

    public function getCasesCompletedIn7Dys($casesLogs)
    {

        // dd($casesLogs->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00' ,today()->subDay(0)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count());
        $ar = array(
            array("Design",
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(6)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(5)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(4)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(3)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(2)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(1)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),
                $casesLogs->where('stage', 1)->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(0)->toDateString() . ' 23:59'])->distinct('case_id')->get()->count(),

            ), array("Milling", array(
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(6)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(5)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(4)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(3)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(2)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(1)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 2)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->toDateString() . ' 23:59'])->get()->count(),

            )), array(
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(6)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(5)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(4)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(3)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(2)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->subDay(1)->toDateString() . ' 23:59'])->get()->count(),
                $casesLogs->where('stage', 3)->distinct('case_id')->whereBetween('created_at', [today()->subDay(7)->toDateString() . ' 00:00', today()->toDateString() . ' 23:59'])->get()->count(),

            ));

        return $ar;
    }

    public function assignToMe($caseId, $stage, $returnMessages = true)
    {
        $jobs = job::where("case_id", $caseId)->where("stage", $stage)->where(function ($query) {
            $query->whereNull('assignee')->orWhere('assignee', Auth()->user()->id);
        })->get();
        if (!$jobs) return back()->with('error', 'Case has no jobs, add jobs first.');
        foreach ($jobs as $job) {
            if ($stage != 2 && $stage != 3)
                $job->is_active = 1;
            $job->is_set = 1;
            $job->assignee = Auth()->user()->id;
            $job->save();
        }
        // Sub-stage logic for main manufacturing stages
        $logStage = $stage;
        $isCompletion = 0;
        if ($stage == 2) { $logStage = $this->stageActions['MILLING_SET']; }
        if ($stage == 3) { $logStage = $this->stageActions['PRINTING_SET']; }
        if ($stage == 4) { $logStage = $this->stageActions['SINTERING_SET']; }
        if ($stage == 5) { $logStage = $this->stageActions['PRESSING_START']; }
        if ($stage == 8) { $logStage = $this->stageActions['DELIVERY_ASSIGN']; }
        $log = new caseLog(['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $logStage, 'is_completion' => $isCompletion]);
        $log->save();
        if ($returnMessages)
            return back()->with('success', "Case has been assigned to you!");
    }

    public function assignAndFinish($caseId, $stage)
    {
        $this->assignToMe($caseId, $stage, false);
        $this->finishCaseStage($caseId, $stage, false);
        return back()->with('success', "Case completed & sent to the next stage!");
    }

    public function assignToStageEmployee(Request $request)
    {
        try {
            $case = sCase::findOrFail($request->case_id);
            $stage = $request->stage;
            $employeeId = $request->employee_id;
            $stageName = $request->stage_name;

            // Find the job for this case and stage
            $job = $case->jobs()->where('stage', $stage)->first();

            if (!$job) {
                return back()->with('error', "No job found for this case in {$stageName} stage!");
            }

            // Update the job assignee
            $job->update([
                'assignee' => $employeeId,
            ]);

            // Log the assignment
            $employee = User::find($employeeId);
            $employeeName = $employee ? $employee->name_initials : 'Unknown';

            caseLog::create([
                'case_id' => $case->id,
                'stage' => $stage,
                'action' => "Case assigned to {$employeeName} for {$stageName}",
                'user_id' => Auth::id(),
            ]);

            Cache::increment('dashboard_cache_version');

            return back()->with('success', "Case #{$case->id} has been assigned to {$employeeName} for {$stageName}!");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign case: ' . $e->getMessage());
        }
    }

    public function sendCaseToDelivery($caseId)
    {
        $case = sCase::findOrFail($caseId);
        foreach ($case->jobs as $job)
            $job->update([
                'stage' => '8',
                'assignee' => null,
            ]);

        return back()->with('success', "Case has been sent to Delivery Stage");
    }

    public function finishCaseStage($caseId, $stage, $returnMessages = true, $jobs = [])
    {$returnMessages = true;
        Log::info('[finishCaseStage] called with parameters', [
            'caseId' => $caseId,
            'stage' => $stage,
            'returnMessages' => $returnMessages,
            'jobs' => count($jobs)
        ]);
        $jobs= empty($jobs) ? job::where("case_id", $caseId)->where("stage", $stage)->get() : $jobs;

          Log::info('[finishCaseStage] firstJob case_id: ' . $caseId .' stage: ' . $stage .'  ', ['$jobs' => count($jobs)]);
      //  if($firstJob) return back()->with("Case's jobs are currently at different stage");

        $assignee = $jobs->first()?->assignee;
        Log::info( '[finishCaseStage] assignee', ['assignee' => $assignee]);
        if (!$assignee) return back()->with('error', "Case Already Completed");

//        if (empty($jobs))
          $jobs = job::where("case_id", $caseId)->where("stage", $stage)->where("assignee", $assignee ?? Auth()->user()->id)->get();
        $case = sCase::findOrFail($caseId);

        if (!$jobs) return back()->with('error', 'No Jobs found.');

        $nextStage = -3;
        foreach ($jobs as $job) {
            $nextStage = $this->getJobNextStage($job);
            Log::info('[finishCaseStage] job', ['job' => $job, 'nextStage' => $nextStage]);

            // before QC ( 7 = QC ) just send them to next stage

            if ($nextStage != 7) {
                    $job->assignee = null;
                    $job->stage = $nextStage;

                    $job->is_active =null;
                    $job->is_set = null;
                    $job->device_id = null;

                $job->save();
                //dd($job, $job->stage, $nextStage);

            } // If Next stage is QC check if all jobs are ready (in finishing) or not before sending them to QC
            else {
                if ($this->allJobsAreIn($case, 6)) {
                    $job->is_active =null;
                    $job->is_set = null;
                    $job->device_id = null;
                    $job->assignee = null;
                    $job->stage = $nextStage;
                    if ($nextStage != 8)
                    $this->issueInvoice($job);
                    $job->save();
                } else {
                    if ($returnMessages)
                        return back()->with('error', 'Not all jobs are in finishing stage');
                }
            }
        }


        // if next stage is Delivery, create invoice
        if ($nextStage == 8) $this->applyInvoice($job);

        // if all jobs are finished, apply invoice and set date delivered
        if ($nextStage == -1) {

            // if contains modification
            if ($case->contains_modification == 1) {
                $log = failureLog::where("case_id", $case->id)->first();

                // if the failure log is found
                if ($log) {
                    $case->actual_delivery_date = $log->old_delivery_date;
                    $note = new note();
                    $note->case_id = $case->id;
                    $note->note = "Modification Delivered On: " . now();
                    $note->written_by = Auth()->user()->id;
                    $note->save();

                    // if contains modification and the failure was not found
                } else {
                    $case->actual_delivery_date = now();
                    $note = new note();
                    $note->case_id = $case->id;
                    $note->note = "Failure Log was not found, no previous delivery date";
                    $note->written_by = Auth()->user()->id;
                    $note->save();
                }
            } else {
                $case->actual_delivery_date = now();
            }


            $case->delivered_to_client = 1;
            $case->save();
            $this->applyInvoice($job);
        }
        // TODO: Fix this later
        // FIXME: Bug here

        // Get the device ID from the job if available
        $deviceId = null;
        if (!empty($jobs) && count($jobs) > 0) {
            $deviceId = $jobs[0]->device_id;
        }

        // Substage logic for main manufacturing stages
        $logStage = $stage;
        $isCompletion = 1;
        if ($stage == 2) {
            $logStage = $this->stageActions['MILLING_COMPLETE'];
        }
        if ($stage == 3) {
            $logStage = $this->stageActions['PRINTING_COMPLETE'];
        }
        if ($stage == 4) {
            $logStage = $this->stageActions['SINTERING_COMPLETE'];
        }
        if ($stage == 5) {
            $logStage = $this->stageActions['PRESSING_COMPLETE'];
        }
        if ($stage == 8) {
            $logStage = $this->stageActions['DELIVERY_COMPLETE'];
        }
        $log = new caseLog([
            'user_id' => Auth()->user()->id,
            'case_id' =>  $caseId,
            'stage' => $logStage,
            'device_id' => $deviceId,
            'action_type' => 3, // 3 = complete
            'is_completion' => $isCompletion
        ]);
        $log->save();

        if ($returnMessages)
            return back()->with('success', "Case have been marked as finished.");
    }

    public function deliveredInBox($caseId)
    {
        //$assignee is the employee currently working on the jobs
        $assignee = job::where("case_id", $caseId)->where("stage", 8)->first()->assignee;
        if (!$assignee) return back()->with('error', "Case Already Completed");
       // $assignee = job::where("case_id", $caseId)->where("stage", 8)->first()->assignee;
        $jobs = job::where("case_id", $caseId)->where("stage", 8)->where("assignee", $assignee)->get();
        $case = sCase::findOrFail($caseId);

        if (!$jobs) return back()->with('error', 'No Jobs found.');

        $nextStage = -3;
        foreach ($jobs as $job) {

            $nextStage = $this->getJobNextStage($job);

            // before QC ( 7 = QC ) just send them to next stage
            if ($nextStage != 7) {
                    $job->assignee = null;

                $job->stage = $nextStage;
                $job->save();
            } // If Next stage is QC check if all jobs are ready (in finishing) or not before sending them to QC
            else {
                if ($this->allJobsAreIn($case, 6)) {

                    $job->assignee = null;
                    $job->stage = $nextStage;
                    $job->save();
                } else
                    return back()->with('error', 'Not all jobs are in finishing stage');
            }
        }


        // if next stage is Delivery, create invoice
        if ($nextStage == 8){ $this->applyInvoice($job);
        $job->is_set=null; $job->assignee=$assignee;$job->is_set=null;}

        // if all jobs are finished, apply invoice and set date delivered
        if ($nextStage == -1) {
            $case->delivered_in_box = 1;
            $this->createTag($case, 15);
            // if contains modification
            if ($case->contains_modification == 1) {
                $log = failureLog::where("case_id", $case->id)->first();

                // if the failure log is found
                if ($log) {
                    $case->actual_delivery_date = now();
                    $note = new note();
                    $note->case_id = $case->id;
                    $note->note = "Modification Delivered On: " . now();
                    $note->written_by = Auth()->user()->id;
                    $note->save();

                    // if contains modification and the failure was not found
                } else {
                    $case->actual_delivery_date = now();
                    $note = new note();
                    $note->case_id = $case->id;
                    $note->note = "Failure Log was not found, no previous delivery date";
                    $note->written_by = Auth()->user()->id;
                    $note->save();
                }
            } else {
                $case->actual_delivery_date = now();
            }
            $case->delivered_to_client = 1;
            $case->save();
            $this->applyInvoice($job);
        }


        $log = new caseLog(['user_id' => Auth()->user()->id , 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1]);
        $log->save();

        return back()->with('success', "Case have been marked as finished & delivered in box.");
    }


    public function getJobNextStage($job)
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

    public function issueInvoice($job)
    {
        $case = sCase::findOrFail($job->case_id);
        /*invoice::where('case_id',$case->id)->delete();*/
        if ($case->contains_modification) return;
        if ($job->is_repeat) {
            $invoice = new invoice();
            $invoice->status = 0;
            $invoice->amount = 0;
            $invoice->case_id = $case->id;
            $invoice->doctor_id = $case->client->id;
            $invoice->save();
            return;
        }


        $invoiceApplicable = true;
        $invoiceAmount = 0;
        foreach ($case->jobs as $job) {
            if ($job->is_repeat == 1 || $job->is_repeat == '1' || $job->is_modification == 1 || $job->is_modification == '1')
                continue;
            $jobPrice = (count(explode(',', $job->unit_num)) * $job->material->price) - $this->getDiscount($job, $case);
            $invoiceAmount += $jobPrice;
        }
        if ($invoiceApplicable) {
            $invoice = new invoice();
            $invoice->status = 0;
            $invoice->case_id = $case->id;
            $invoice->doctor_id = $case->client->id;
            if (isset($case->discount)) {
                $invoice->amount_before_discount = $invoiceAmount;
                $invoice->amount = $invoiceAmount - $case->discount->discount;
            } else {
                $invoice->amount = $invoiceAmount;
                $invoice->amount_before_discount = $invoiceAmount;
            }
            $invoice->save();
        }
    }

    public function applyInvoice($job)
    {
        $case = sCase::with('invoice')->where('id', $job->case_id)->get();
        $patientName = $case[0]->patient_name;
        $client = $case[0]->client;
        $clientTokens = MobileNotificationToken::where("client_id", $client->id)->get();
        foreach ($clientTokens as $token) {
            if ($case[0]->delivered_in_box)
                $this->sendCaseNotification($token->token, "Case has been delivered in box", "Case of $patientName has been delivered In-Box", "1");
            else
                $this->sendCaseNotification($token->token, "Case has been delivered", "Case of $patientName has been delivered", "1");
        }

//        if ($case[0]->delivered_in_box) {
//            $this->sendCaseNotification($client->doc_notification_token, "Case has been delivered in box", "Case of $patientName has been delivered In-Box", "1");
//            $this->sendCaseNotification($client->clinic_notification_token, "Case has been delivered in box", "Case of $patientName has been delivered In-Box", "1");
//        } else// 0 => open app, 1 => open case, 2 => open statement
//        {
//            $this->sendCaseNotification($client->doc_notification_token, "Case has been delivered", "Case of $patientName has been delivered", "1");
//            $this->sendCaseNotification($client->clinic_notification_token, "Case has been delivered", "Case of $patientName has been delivered", "1");
//        }
        if ($case[0]->contains_modification) return;
        $allJobsCompleted = true;
        foreach ($case[0]->jobs as $job)
            if ($job->stage != -1)
                $allJobsCompleted = false;

        if ($allJobsCompleted) {
            $client = $case[0]->client;
            $invoice = $case[0]->invoice;
            $client->balance = $client->balance + ($invoice->amount ?? 0);
            $invoice->status = 1;
            $invoice->date_applied = now();
            $invoice->save();
            $client->save();


        }
    }

    public function invoicesList(Request $request)
    {
        if ($request->from && $request->to) {
            $from = $request->from;
            $to = $request->to;
        } else {
            $from = date('Y-m-d', strtotime('first day of this month'));
            $to = now()->toDateString();
        }
        $invoices = invoice::whereBetween('date_applied', [$from . ' 00:00', $to . ' 23:59']);


        if ($request->doctor && !in_array("all", $request->doctor))
            $invoices = $invoices->whereIn('doctor_id', $request->doctor);


        if ($request->patient_name) {
            $invoices = $invoices->whereHas('case', function ($q) use ($request) {
                $q->where('patient_name', 'like', '%' . $request->patient_name . '%');
            });
        }

        //dd($this->getQuery($invoices));
        $invoices = $invoices->get();


        $selectedClients = $request->doctor;
        $clients = client::all();

        return view('generic.invoices-list', compact('invoices', 'clients', 'to', 'from', 'selectedClients', 'clients'))->with('patientName', $request->patient_name);
    }

    public function getDiscount($job, $case)
    {
        $discounts = $case->client->discounts;
        $discountOfMaterial = $discounts->where('material_id', $job->material_id)->first();
        // No Discount
        if (!$discountOfMaterial) return 0;

        //Fixed Discount
        if ($discountOfMaterial && $discountOfMaterial->type === 0) {
            return count(explode(',', $job->unit_num)) * $discountOfMaterial->discount;

            // Percentage Discount
        } else if ($discountOfMaterial->type) {
            return (count(explode(',', $job->unit_num))) * ($job->material->price * ($discountOfMaterial->discount / 100));
        }
    }

    public function allJobsAreIn($case, $stage)
    {
        foreach ($case->jobs->where('stage', '!=', -1) as $job)
            if ($job->stage < $stage)
                return false;
        return true;
    }


    public function acceptCaseByDelivery($id)
    {

        foreach (job::where("case_id", $id)->get() as $job) {
            $job->assignee = Auth()->user()->id;
            $job->delivery_accepted = Auth()->user()->id;
            $job->save();
        }

        $DeliLog = new caseLog(['user_id' => Auth()->user()->id, 'case_id' => $id, 'stage' => $this->stageActions['DELIVERY_ACCEPT'], 'is_completion' => 0]);
        $DeliLog->save();
        return back()->with('success', "Case has been assigned to you!");
    }

    public function viewVoucher($id)
    {
        $case = sCase::with(['jobs.jobType', 'jobs.material', 'jobs.deliveryDriver', 'invoice', 'client'])
            ->findOrFail($id);
        $voucher = 'voucher';
        return view('delivery.view-voucher', compact('case', 'voucher'));
    }

    public function externallyMilled(Request $request)
    {
        $jobs = job::where("case_id", $request->case_id)->where("stage", 2)->where("assignee", Auth()->user()->id)->get();

        if (!$jobs) return back()->with('error', 'No Jobs found.');

        foreach ($jobs as $job) {
            $nextStage = $this->getJobNextStage($job);
            $job->milling_lab = $request->lab_id;
            $job->assignee = null;
            $job->stage = $nextStage;
            $job->save();
        }

        $log = new caseLog(['user_id' => Auth()->user()->id, 'case_id' => $request->case_id, 'stage' => $this->stageActions['MILLING_COMPLETE'], 'is_completion' => 1]);
        $log->save();
        return back()->with('success', "Case have been marked as finished.");
    }

    public function deliverySchedule(Request $request)
    {
        if ($request->from && $request->to) {
            $data['from'] = $request->from;
            $data['to'] = $request->to;
            $cases = sCase::with('client')->whereBetween(
                'initial_delivery_date', array($data['from'], $data['to']))
                ->where('delivered_to_client', '=', 0)
                ->orderBy('cases.initial_delivery_date', 'ASC')->get();
        } else {
            $data['from'] = today()->subDays(356)->toDateString() . ' 00:00';
            $data['to'] = today()->addDays(1)->toDateString() . ' 23:59';
            $cases = sCase::with('client')->whereBetween(
                'initial_delivery_date', array($data['from'], $data['to']))
                ->where('delivered_to_client', '=', 0)
                ->orderBy('cases.initial_delivery_date', 'ASC')->get();
        }
        return view('delivery.delivery-schedule', compact('cases', 'data'));
    }

    public function updateDeliveryDate(Request $request)
    {
        $action = "";
        $transaction = DB::transaction(function () use ($request) {
            $case = sCase::where('id', $request->id)->first();
            if (!$case) {
                return false;
            }
            $action = "Updated delivery date from [" . str_replace('T', " ", $case->initial_delivery_date) . "] to  [" . $request->delivery_date . ']';
            $case->initial_delivery_date = $request->delivery_date;
            $case->save();
            $editLogRecord = new editLog();
            $editLogRecord->case_id = $request->id;
            $editLogRecord->user_id = Auth()->user()->id;
            $editLogRecord->action = $action;
            $editLogRecord->save();
            $note = new note();
            $note->case_id = $request->id;
            $note->note = $action;
            $note->written_by = Auth()->user()->id;
            $note->save();
            return true;
        });
        if ($transaction) {
            return back()->with('success', 'Case delivery date has been updated');
        } else {
            return back()->with('error', 'Something went wrong');
        }

    }

    public function viewSingleScreen()
    {
        $cases = sCase::whereNull('actual_delivery_date')->get();
        return view('generic.screen', compact('cases'));
    }

    public function deleteCase($id)
    {
        $case = sCase::where('id', $id)->first();
        DB::beginTransaction();
        $case->jobs()->delete();
        $case->notes()->delete();
        $case->photos()->delete();
        $case->tags()->delete();
        $case->discount()->delete();
        $case->invoice()->delete();
        $case->delete();
        caseLog::where('case_id', $id)->delete();

        DB::commit();

        return back()->with('success', 'Case and all its information deleted successfully.');
    }

    public function detectNewJobStage(Request $request)
    {
        $dummyJob = new job();
        $dummyJob->type = $request->jobType;
        $dummyJob->case_id = $request->case_id;
        $dummyJob->material_id = $request->materialId;
        $dummyJob->save();
        $stage = $this->lowestJobStageApplicable($dummyJob, $request->case_id);
        $case = sCase::findOrFail($request->case_id);
        job::where('id', $dummyJob->id)->forceDelete();

        if ($this->isCaseFinished($request->case_id))
            return response()->json(array('msg' => "Completed"), 200);


        else if ($this->caseHasNoJobs($request->case_id))
            return response()->json(array('msg' => "Design"), 200);
        else if (!$case->hasModels() && $request->jobType == 4)
            return response()->json(array('msg' => $this->stageToText(1)), 200);
        else
            return response()->json(array('msg' => $this->stageToText($stage)), 200);

    }

    public function createInvoice()
    {
        $clients = client::where('active', '!=', 0)->get();
        return view('generic.create-invoice', compact('clients'));
    }

    public function viewInvoice($caseId)
    {
        $case = sCase::with(['jobs.jobType', 'jobs.material', 'invoice', 'client'])
            ->findOrFail($caseId);
        return view('generic.invoice-view', compact('case'));
    }

    public function deletedCases()
    {
        $cases = sCase::onlyTrashed()->paginate(10);
        $trashedCases = true;
        return view('cases.index', compact('cases', 'trashedCases'));
    }

    public function lockCase($caseId)
    {
        $case = sCase::findOrFail($caseId);
        $case->update(['locked' => 1]);
        return back()->with('success', 'Case locked successfully.');

    }

    public function unlockCase($caseId)
    {
        $case = sCase::findOrFail($caseId);
        $case->update(['locked' => 0]);
        $this->createTag($case, 14);

        return back()->with('success', 'Case un-locked successfully.');

    }

    public function restoreDeletedCase($id)
    {
        $case = sCase::withTrashed()->where('id', $id)->first();
        DB::beginTransaction();
        $case->jobs()->withTrashed()->restore();
        $case->notes()->withTrashed()->restore();
        $case->photos()->withTrashed()->restore();
        $case->tags()->withTrashed()->restore();
        $case->discount()->withTrashed()->restore();
        $case->invoice()->withTrashed()->restore();
        $case->restore();
        caseLog::where('case_id', $id)->withTrashed()->restore();;

        DB::commit();
        return back()->with('success', 'Case restored successfully.');
    }

    public function globalSearch(Request $request)
    {
        $cases = sCase::query();

        $searchText = $request->searchText;

        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $searchText, -1, PREG_SPLIT_NO_EMPTY);
        $cases = $cases->where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('patient_name', 'like', "%{$value}%");
            }
        });

        $cases = $cases->orderByRaw('-`actual_delivery_date` ASC')->orderBy("initial_delivery_date", 'asc')->get();


        $isSearchResults = true;
        return view('cases.index', compact('cases', 'isSearchResults'));
    }

    public function rejectedCases(Request $request)
    {
        if ($request->from && $request->to) {
            $from = $request->from;
            $to = $request->to;
        } else {
            $from = date('Y-m-d', strtotime('-30 days'));
            $to = now()->toDateString();
        }

        if ($request->doctor && !in_array("all", $request->doctor))
            $cases = sCase::whereHas('jobs', function ($q) {
                $q->where('is_rejection', 1);
            })->whereBetween('created_at', [$from . ' 00:00', $to . ' 23:59'])->whereIn('doctor_id', $request->doctor);

        else
            $cases = sCase::whereHas('jobs', function ($q) {
                $q->where('is_rejection', 1);
            })->whereBetween('created_at', [$from . ' 00:00', $to . ' 23:59']);

        if ($request->patient_name)
            $cases = $cases->where('patient_name', 'like', '%' . $request->patient_name . '%')->get();
        else
            $cases = $cases->get();

        $selectedClients = $request->doctor;
        $clients = client::all();
        return view('cases.rejected-cases', compact('cases', 'from', 'to', 'selectedClients', 'clients'))->with('patientName', $request->patient_name);
    }

    public function resetCaseToWaiting($id, $stage)
    {
        $caseJobs = job::where('case_id', $id)->where("stage", $stage)->get();

        if (!$caseJobs)
            return back()->with('error', 'Case jobs not found.');

        foreach ($caseJobs as $job) {
            $job->assignee = null;
            $job->delivery_accepted = null;
            $job->save();
        }

        return back()->with('success', 'Case has been reset successfully.');
    }

    public function completeByAdmin($id, $stage)
    {
        $this->finishCaseStage($id, $stage, false);
        return back()->with('success', 'Case has been overridden & completed successfully.');
    }

    public function testNotification($type = 2)
    {
        //   $docClient = DB::select('SELECT * FROM clients WHERE phone LIKE ? LIMIT 1', ['%' . "0788160088" . '%']);
        //   $clinicAccount = DB::select('SELECT * FROM clients WHERE clinic_phone LIKE ? LIMIT 1', ['%' . "0788160088" . '%']);

//        $docClient = client::where('phone', 'like', '%' . "0788160088" . '%')->get()->first();
//        $clinicAccount= client::where('clinic_phone', 'like', '%' . "0788160088" . '%')->get()->first();
//        dd($docClient, $clinicAccount);
        // print_r($docClient[0] ?? "NO DOC **" );
        // print_r("--------------");
        //   print_r($clinicAccount[0]);
        $client = client::where("id", 1)->first();
        $patient_name = "يزن شريتح";
        // 1=> inbox  2=> case delivered  3=> new payment

        echo("test $type");
        echo(" doc not : " . $client->doc_notification_token);
        echo(" clinic not : " . $client->clinic_notification_token);
        switch ($type) {
            case 1:
                if ($client->doc_notification_token)
                    $this->sendCaseNotification($client->doc_notification_token, "Case Delivered In-Box",
                        "Case of $patient_name has been delivered in box "
                    );
                if ($client->clinic_notification_token)
                    $this->sendCaseNotification($client->clinic_notification_token, "Case Delivered In-Box",
                        "Case of $patient_name has been delivered in box ");
                break;
            case 2:
                if ($client->doc_notification_token)
                    $this->sendCaseNotification($client->doc_notification_token,
                        "Case Delivered", "Case of $patient_name has been delivered");
                if ($client->clinic_notification_token)
                    $this->sendCaseNotification($client->clinic_notification_token,
                        "Case Delivered", "Case of $patient_name has been delivered");
                break;
            case 3:
                if ($client->doc_notification_token)
                    $this->sendPaymentNotification($client->doc_notification_token,
                        "Payment Received",
                        "100 " . "JOD has been received",
                    );
                break;
            default:
                dd("Enter Notification Type [ 0 => ");
        }
        echo("end switch");
    }

    public function finishCaseCompletely($caseId)
    {
        $case = sCase::findOrFail($caseId);
        foreach ($case->jobs as $job) {
            $job->stage = 8;
            $job->assignee = Auth()->user()->id;
            $job->delivery_accepted = Auth()->user()->id;
            $job->save();

        }
        caseLog::insert([
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['MILLING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRINTING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['SINTERING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRESSING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 6, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 7, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['MILLING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRINTING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['SINTERING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRESSING_START'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 6, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 7, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_ASSIGN'], 'is_completion' => 0],
        ]);
        $this->issueInvoice($case->jobs[0]);
        return back()->with('success', 'Case is Active at Delivery Stage.');
    }

    public function sendCaseToStage($caseId)
    {
        $case = sCase::findOrFail($caseId);
        foreach ($case->jobs as $job) {
            $job->stage = 8;
            $job->assignee = Auth()->user()->id;
            $job->delivery_accepted = Auth()->user()->id;
            $job->save();
        }
        caseLog::insert([
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['MILLING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRINTING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['SINTERING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRESSING_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 6, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 7, 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_COMPLETE'], 'is_completion' => 1],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 1, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['MILLING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRINTING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['SINTERING_SET'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['PRESSING_START'], 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 6, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => 7, 'is_completion' => 0],
            ['user_id' => Auth()->user()->id, 'case_id' => $caseId, 'stage' => $this->stageActions['DELIVERY_ASSIGN'], 'is_completion' => 0],
        ]);
        $this->issueInvoice($case->jobs[0]);
        return back()->with('success', 'Case is Active at Delivery Stage.');
    }

    public function createDummyCase($stage = 1, $amount =1)
    {
        if ($stage > 8 || $stage < 1) { dd("-_-");}
        DB::beginTransaction();
        $faker = Faker::create();

        try {
            $faker = \Faker\Factory::create('ar_SA');
            while ($amount != -1) {
                $case = new sCase();
                $case->case_id = $faker->unique()->numerify('Y####');
                $case->patient_name = $faker->name;
                $case->doctor_id = $faker->numberBetween(1, 10);
                $case->impression_type = $faker->randomElement([1, 2, 3]);
                $case->initial_delivery_date = $faker->dateTimeBetween('now', '+1 month');
                $case->created_by = Auth::id() ?? 1;
                $case->save();// Generating random tags
               // $tags = range(1, 4);
              //  shuffle($tags);
//                foreach (array_slice($tags, 0, rand(1, 3)) as $tag) {
//                    $this->createTag($case, $tag);
//               // }// Creating random jobs
                $teeth = $faker->randomElement([0, 1]);
                $units = ($teeth == 0 ? $faker->randomElement(['1', '1,2', '1,2,3']) : null ?? $faker->randomElement(['upper', "lower,upper", "lower"]));
                $jobCount = rand(1, 2);
                for ($i = 0; $i < $jobCount; $i++) {

                    $newJob = new job([
                        'unit_num' => $units,
                        'type' => 1,
                        'color' => "A1",
                        'style' => "Single",
                        'abutment' => $faker->randomElement([0, 1]),
                        'implant' => $faker->randomElement([0, 1]),
                        'material_id' => $faker->randomElement(['1', '2', '6']),
                        'case_id' => $case->id,
                        'doctor_id' => $case->doctor_id,
                        'stage' => $stage,
                    ]);
                    $newJob->save();


//                    $material = Material::find(1,2,6)->first();
//                    if ($material) {
//                        $newJob->unit_price = $material->price;
//                        $newJob->save();
//                    }
                }// Adding a dummy note

                $amount--;
            }
            DB::commit();
            return "Dummy case(s) created successfully!";
        } catch (\Exception $e) {
            DB::rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Delete an attachment from a case
     */
    public function deleteAttachment($id)
    {
        try {
            $file = \App\File::findOrFail($id);

            // Check if user has permission to edit cases
            $permissions = Cache::get('user' . Auth::user()->id);
            if (!Auth::user()->is_admin && !$permissions->contains('permission_id', 114)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete attachments'
                ], 403);
            }

            // Delete the physical file
            $filePath = public_path($file->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the database record
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }}
    public function printInvoice($caseId)
    {
        $case = sCase::findOrFail($caseId);
        return view('cases.invoice', compact('case'));
    }
}