<?php
/**
 * User: Yazan
 * Date: 24/1/2022
 */
namespace App\Http\Controllers;

use App\abutment;
use App\caseTag;
use App\client;
use App\failureCause;
use App\failureLog;
use App\Http\Traits\helperTrait;
use App\implant;
use App\impressionType;
use App\invoice;
use App\job;
use App\JobType;
use App\material;
use App\materialJobtype;
use App\note;
use App\sCase;
use App\tag;
use Illuminate\Http\Request;
use App\lab;
use Illuminate\Support\Facades\DB;


class FailuresController extends Controller
{
    use helperTrait;

    public function rejectionView($id){
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active','!=',0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden',0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        $stage = -2;
        $implants = implant::all();
        $abutments = abutment::all();
        $failureCauses = failureCause::all();
        return view('failures.reject-case',compact('case','clients','implants',
            'abutments', 'materials', 'types','impressionTypes','tags','tagsAsArray',
            'jobTypeMaterials','stage','failureCauses'));

    }
    public function repeatView($id){
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active','!=',0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden',0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        $stage = -2;
        $implants = implant::all();
        $abutments = abutment::all();
        $failureCauses = failureCause::all();
        return view('failures.repeat-case',compact('case','clients','implants',
            'abutments', 'materials', 'types','impressionTypes','tags','tagsAsArray',
            'jobTypeMaterials','stage','failureCauses'));

    }
    public function modifyView($id){
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active','!=',0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden',0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        $stage = -2;
        $implants = implant::all();
        $abutments = abutment::all();
        $failureCauses = failureCause::all();
        return view('failures.modify-case',compact('case','clients','implants','abutments', 'materials',
            'types','impressionTypes','tags','tagsAsArray',
            'jobTypeMaterials','stage','failureCauses'));
    }
    public function redoView($id){
        $case = sCase::findOrFail($id);
        $materials = material::all();
        $clients = client::where('active','!=',0)->get();
        $types = JobType::all();
        $impressionTypes = impressionType::all();
        $jobTypeMaterials = materialJobtype::all();
        $tags = tag::where('hidden',0)->get();
        $tagsAsArray = $case->tags->pluck('tag_id')->toArray();
        $stage = -2;
        $implants = implant::all();
        $abutments = abutment::all();
        $failureCauses = failureCause::all();
        return view('failures.redo-case',compact('case','clients','implants','abutments', 'materials',
            'types','impressionTypes','tags','tagsAsArray',
            'jobTypeMaterials','stage','failureCauses'));
    }

    public function rejectCase(Request $request){
        $case = sCase::where('id', $request->id)->first();
        if ($case->locked == 1) back()->with('error',"Case is locked");
        DB::beginTransaction();
        if ($request->repeat)
            foreach($request->repeat as $job) {
                $jobId = $job["job_id"];
                if (isset($job["units" . $jobId])) {


                    $originalJob = job::findOrFail($jobId);

                    $rejectionJob = new job(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId],
                        'color' => $job["color" . $jobId] ?? 'None', 'style' => $job["style" . $jobId] ?? 'None',
                        'abutment' => $job["abutment" . $jobId] ?? 'None', 'implant' => $job["implant" . $jobId] ?? 'None',
                        'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor,
                    ]);
                    $rejectionJob->color = $originalJob->color;
                    $rejectionJob->type = $originalJob->type;
                    $rejectionJob->doctor_id = $originalJob->doctor_id;
                    $rejectionJob->case_id = $case->id;
                    $rejectionJob->is_rejection = 1;
                    $rejectionJob->stage = -1;
                    $rejectionJob->unit_price = $originalJob->unit_price *-1;

                    $rejectionJob->save();
                    $originalJob->update(['has_been_rejected' => 1,'rejected_job_id'=>$rejectionJob->id]);
                }
            }

            $case->update(['locked' => 1],[['is_rejected' => 1]]);
            $this->createTag($case,10);
            $failureLog = new failureLog(['case_id'=>$case->id,'failure_type' =>0,'cause_id' =>$request->failure_cause_id , 'explanation' =>$request->failure_explanation,
                'done_by' =>Auth()->user()->id]);
            $failureLog->save();

            $this->createRejectionNote($case,$failureLog);
            $this->issueRejectionInvoice($case);
            $this->applyRejectionInvoice($case);
        DB::commit();
        return redirect()->route( 'cases-index' )->with('success', 'Rejection completed successfully');

    }
    public function repeatCase(Request $request){
        $oldCase = sCase::where('id', $request->id)->first();
        if ($oldCase->locked == 1) back()->with('error',"Case is locked");
        DB::beginTransaction();
        $case = new sCase();
        $case->case_id =$oldCase->case_id . '_REP';
        $case->patient_name = str_replace(' / إعادة', '',$oldCase->patient_name) . ' / إعادة';
        $case->doctor_id = $oldCase->doctor_id;
        $case->impression_type = $oldCase->impression_type;
        $case->initial_delivery_date = $request->delivery_date;
        $case->created_by = Auth()->user()->id;
        $case->save();


        if ($request->repeat)
            foreach($request->repeat as $job) {
                $jobId = $job["job_id"];
                if (isset($job["units". $jobId])) {

                    $originalJob = job::findOrFail($jobId);
                    $repeatedJob = new job(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId],
                        'color' => $job["color" . $jobId] ?? 'None', 'style' => $job["style" . $jobId] ?? 'None',
                        'abutment' => $job["abutment" . $jobId] ?? 'None', 'implant' => $job["implant" . $jobId] ?? 'None',
                        'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor, 'stage' => $job["repeatedJobStage" . $jobId]
                    ]);
                    $repeatedJob->case_id = $case->id;
                    $repeatedJob->is_repeat = 1;
                    $repeatedJob->unit_price = $originalJob->unit_price;
                    $repeatedJob->original_job_id = $originalJob->id;
                    $repeatedJob->save();
                    $originalJob->repeated_job_id = $repeatedJob->id;
                    $originalJob->save();
                }
            }


//        if ($request->repeat2)
//            foreach($request->repeat2 as $job){
//                if (isset($job["units"])) {
//                    $newJob = new job();
//                    $newJob->unit_num = $job["units"];
//                    $newJob->type = $job["jobType"];
//                    $newJob->color = $job["color"]?? 'None';
//                    $newJob->style = $job["style"] ?? 'None';
//                    $newJob->abutment = $job["abutment"] ?? 'None';
//                    $newJob->implant = $job["implant"] ?? 'None';
//                    $newJob->material_id = $job["material_id"];
//                    $newJob->case_id = $case->id;
//                    $newJob->stage = 1;
//
//                    $newJob->unit_price = material::FindOrFail($job["material_id"])->price - ($this->clientDiscount4rejection($newJob, $case) / count(explode(',', $newJob->unit_num)));
//                    $newJob->save();
//
//
//
//
//                    if($newJob->material->teeth_or_jaw == 1)
//                    {
//                        $newJob->implant =null;
//                        $newJob->abutment =null;
//                        $newJob->save();
//                    }
//                }}


        /*
  *     SAVING TAGS
  */
        if($request->tags)
            foreach($request->tags as $tag){
                $this->createTag($case,$tag);
        }
        if($files=$request->file('images')){
            foreach($files as $file){
                $name=$file->getClientOriginalName();
                $file->move('caseImages/'.$case->id .'/',$name);


                $newFile = new file();
                $newFile->path = 'caseImages/'.$case->id .'/'.$name;
                $newFile->case_id = $case->id;
                $newFile->added_by = Auth()->user()->id;
                $newFile->save();
            }
        }


        $failureLog = new failureLog(['case_id'=>$case->id,'failure_type' =>1,'cause_id' =>$request->failure_cause_id , 'explanation' =>$request->failure_explanation,
            'done_by' =>Auth()->user()->id]);
        $failureLog->save();

        $this->createRepeatNote($oldCase,$case,$failureLog);
        $this->createTag($case,11);
        $case->update(['locked' => 1,'first_case_if_repeated'=> $oldCase->id,'actual_delivery_date' => null,'delivered_to_client' => 0,'voucher_recieved_by'=> null]);
        foreach($oldCase->notes as $note){
            new note(['case_id' => $case->id, 'note' => $note->note , 'written_by' =>$note->written_by]);
        }
        $oldCase->update(['locked' => 1]);
        DB::commit();
        return redirect()->route( 'cases-index' )->with('success', 'Repeat case has been created successfully');

    }
    public function modifyCase(Request $request){
        $case = sCase::where('id', $request->id)->first();
        if ($case->locked == 1) back()->with('error',"Case is locked");
        $case->patient_name = str_replace(' / تعديل','',$case->patient_name) . ' / تعديل';
        DB::beginTransaction();
        if ($request->repeat)
            foreach($request->repeat as $job) {
                $jobId = $job["job_id"];
                if (isset($job["units". $jobId])) {

                    $originalJob = job::findOrFail($jobId);
                    $modifiedJob = new job(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId],
                        'color' => $job["color" . $jobId] ?? 'None', 'style' => $job["style" . $jobId] ?? 'None',
                        'abutment' => $job["abutment" . $jobId] ?? null, 'implant' => $job["implant" . $jobId] ?? null,
                        'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor,
                    ]);
                    $modifiedJob->case_id = $case->id;
                    $modifiedJob->is_modification = 1;
                    $modifiedJob->stage = 6;
                    $modifiedJob->unit_price = $originalJob->unit_price;
                    $modifiedJob->save();
                    $originalJob->modified_job_id = $modifiedJob->id;
                    $originalJob->save();
                }
            }

//        if ($request->repeat2)
//            foreach($request->repeat2 as $job){
//                if (isset($job["units"])) {
//                    $newJob = new job();
//                    $newJob->unit_num = $job["units"];
//                    $newJob->type = $job["jobType"];
//                    $newJob->color = $job["color"]?? 'None';
//                    $newJob->style = $job["style"] ?? 'None';
//                    $newJob->abutment = $job["abutment"] ?? 'None';
//                    $newJob->implant = $job["implant"] ?? 'None';
//                    $newJob->material_id = $job["material_id"];
//                    $newJob->case_id = $case->id;
//                    $newJob->stage = 1;
//
//                    $newJob->unit_price = material::FindOrFail($job["material_id"])->price - ($this->clientDiscount4rejection($newJob, $case) / count(explode(',', $newJob->unit_num)));
//                    $newJob->save();
//
//
//
//
//                    if($newJob->material->teeth_or_jaw == 1)
//                    {
//                        $newJob->implant =null;
//                        $newJob->abutment =null;
//                        $newJob->save();
//                    }
//                }}


        /*
  *     SAVING TAGS
  */
//        if ($request->tags)
//            foreach($request->tags as $tag){
//                $newTag = new caseTag(['case_id' => $case->id, 'tag_id' => $tag , 'added_by' => Auth()->user()->id]);
//                $newTag->save();
//            }
        if($files=$request->file('images')){

            foreach($files as $file){
                $name=$file->getClientOriginalName();
                $file->move('caseImages/'.$case->id .'/',$name);


                $newFile = new file();
                $newFile->path = 'caseImages/'.$case->id .'/'.$name;
                $newFile->case_id = $case->id;
                $newFile->added_by = Auth()->user()->id;
                $newFile->save();
            }
        }


        $failureLog = new failureLog(['case_id'=>$case->id,'failure_type' =>2,'cause_id' =>$request->failure_cause_id , 'explanation' =>$request->failure_explanation,
            'done_by' =>Auth()->user()->id,'old_delivery_date' =>$case->actual_delivery_date ]);
        $failureLog->save();

        $this->createModificationNote($case,$failureLog);


        $this->createTag($case,12);
        $case->update(['locked' => 1,'actual_delivery_date' => null,'contains_modification' =>1, 'initial_delivery_date' => $request->delivery_date,'delivered_to_client' => 0,'voucher_recieved_by'=> null]);
        DB::commit();
        return redirect()->route( 'cases-index' )->with('success', 'Modification jobs have has been created successfully');

    }
    public function redoCase(Request $request){
        $case = sCase::where('id', $request->id)->first();
        if ($case->locked == 1) back()->with('error',"Case is locked");

        DB::beginTransaction();
        if ($request->repeat)
            foreach($request->repeat as $job) {
                $jobId = $job["job_id"];
                if (isset($job["units". $jobId])) {

                    $originalJob = job::findOrFail($jobId);
                    $redoJob = new job(['unit_num' => $job["units" . $jobId], 'type' => $job["jobType" . $jobId],
                        'color' => $job["color" . $jobId] ?? 'None', 'style' => $job["style" . $jobId] ?? 'None',
                        'abutment' => $job["abutment" . $jobId] ?? 0, 'implant' => $job["implant" . $jobId] ?? 0,
                        'material_id' => $job["material_id" . $jobId], 'doctor_id' => $request->doctor,'stage' => $job["redoJobStage" . $jobId]
                    ]);
                    $redoJob->case_id = $case->id;
                    $redoJob->is_redo = 1;
                    $redoJob->is_repeat = $originalJob->is_repeat;
                    $redoJob->unit_price = $originalJob->unit_price;
                    $redoJob->save();
                    $newOldJobUnits = array_diff(explode(',',$originalJob->unit_num), explode(',',$redoJob->unit_num));

                    // if no units left in original job, delete it and keep the redo
                    if (count($newOldJobUnits) < 1)
                        $originalJob->delete();
                    else{
                    $originalJob->unit_num =implode(',',$newOldJobUnits);
                    $originalJob->redone_job_id = $redoJob->id;
                    $originalJob->save();
                    }
                }
            }

//        if ($request->repeat2)
//            foreach($request->repeat2 as $job){
//                if (isset($job["units"])) {
//                    $newJob = new job();
//                    $newJob->unit_num = $job["units"];
//                    $newJob->type = $job["jobType"];
//                    $newJob->color = $job["color"]?? 'None';
//                    $newJob->style = $job["style"] ?? 'None';
//                    $newJob->abutment = $job["abutment"] ?? 'None';
//                    $newJob->implant = $job["implant"] ?? 'None';
//                    $newJob->material_id = $job["material_id"];
//                    $newJob->case_id = $case->id;
//                    $newJob->stage = 1;
//
//                    $newJob->unit_price = material::FindOrFail($job["material_id"])->price - ($this->clientDiscount4rejection($newJob, $case) / count(explode(',', $newJob->unit_num)));
//                    $newJob->save();
//
//
//
//
//                    if($newJob->material->teeth_or_jaw == 1)
//                    {
//                        $newJob->implant =null;
//                        $newJob->abutment =null;
//                        $newJob->save();
//                    }
//                }}

        /*
  *     SAVING TAGS
  */
        if ($request->tags)
            foreach($request->tags as $tag){
                $this->createTag($case,$tag);
            }
        if($files=$request->file('images')){

            foreach($files as $file){
                $name=$file->getClientOriginalName();
                $file->move('caseImages/'.$case->id .'/',$name);


                $newFile = new file();
                $newFile->path = 'caseImages/'.$case->id .'/'.$name;
                $newFile->case_id = $case->id;
                $newFile->added_by = Auth()->user()->id;
                $newFile->save();
            }
        }

        $failureLog = new failureLog(['case_id'=>$case->id,'failure_type' =>3,'cause_id' =>$request->failure_cause_id , 'explanation' =>$request->failure_explanation,
            'done_by' =>Auth()->user()->id]);
        $failureLog->save();
        $this->createRedoNote($case,$failureLog);

        $this->createTag($case,13);
        $case->update(['locked' => 1,'actual_delivery_date' => null,'delivered_to_client' => 0,'voucher_recieved_by'=> null] );
        DB::commit();
        return redirect()->route( 'cases-index' )->with('success', 'Re-do jobs have has been created successfully');

    }

    public function issueRejectionInvoice($case){
        DB::beginTransaction();
        $invoiceAmount = 0;
        foreach($case->jobs()->where('is_rejection',1)->get() as $job) {

            $jobPrice = (count(explode(',',$job->unit_num)) * $job->unit_price);
            $invoiceAmount += $jobPrice;
        }

            $invoice = new invoice();
            $invoice->status =0;
            $invoice->case_id =$case->id;
            $invoice->doctor_id =$case->client->id;
            $invoice->created_at = now();
           // if(isset($case->discount))
           // {
           //     $invoice->amount_before_discount =$invoiceAmount;
           //     $invoice->amount =$invoiceAmount - $case->discount->discount;
           // }
           // else
           // {
                $invoice->amount =$invoiceAmount;
                $invoice->amount_before_discount =$invoiceAmount;
          //  }
            $invoice->rejection_invoice = 1;
            $invoice->save();
        DB::commit();
    }
    public function applyRejectionInvoice($case){
        DB::beginTransaction();
        $invoice = invoice::where(['case_id' =>$case->id , 'rejection_invoice' =>1])->first();
            $client = client::where('id',$invoice->doctor_id)->first();
            $client->balance =  $client->balance + $invoice->amount;
            $invoice->status = 1;
            $invoice->date_applied = now();
            $invoice->save();
            $client->save();
        DB::commit();
    }
}