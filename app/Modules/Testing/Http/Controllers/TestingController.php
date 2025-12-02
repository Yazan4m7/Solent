<?php

namespace App\Modules\Testing\Http\Controllers;

use App\Http\Controllers\Controller;

use App\abutment;
use App\implant;
use Illuminate\Http\Request;
use DB;
use App\discount;
use App\client;
use App\material;
use App\JobType;
use App\sCase;
use App\caseTag;
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

use Illuminate\Support\Facades\Config;


class TestingController extends Controller
{
    public function createAndSendCaseTo(Request $request)
    {
      //  dd($request->stageToSendTo);
        $completedCase = false;
        if ($request->stageToSendTo == 10){

            $completedCase = true;
            $request->stageToSendTo = -1;
        }
        $case = $this->createCase($request);
        foreach($case->jobs as $job){
            $job->stage=$request->stageToSendTo;
            $job->save();
        }

        if($request->stageToSendTo>6)
            $this->issueInvoice($case->jobs[0]);
        if ($completedCase){
            $this->issueInvoice($case->jobs[0]);
            $this->applyInvoice($case->jobs[0]);
            $case->delivered_to_client = 1;
            $case->actual_delivery_date=now();
            $case->save();
        }
        return back()->with('success', "Case Created");
    }

    public function createCase(Request $request)
    {
        DB::beginTransaction();

        /*
        *     CASE BASIC INFO
        */
        $case = new sCase();
        $case->case_id = $request->caseId1 . $request->caseId2 .$request->caseId3 . '_' . $request->caseId4;
        $case->patient_name = $request->patient_name;
        $case->doctor_id = $request->doctor;
        $case->impression_type = $request->impression_type;
        $case->initial_delivery_date = $request->delivery_date;
        $case->created_by = Auth()->user()->id;
        $case->save();


        /*
        *     SAVING TAGS
        */
        if ($request->tags)
            foreach($request->tags as $tag){
                $newTag = new caseTag(['case_id' => $case->id, 'tag_id' => $tag , 'added_by' => Auth()->user()->id]);
                $newTag->save();
            }

        /*
        *     STORING JOBS
        */
        if ($request->repeat)
            foreach($request->repeat as $job){
                try {
                    if(!isset($job["units"])) continue;
                    $newJob = new job(['unit_num' => $job["units"],'type' =>$job["jobType"] ,'color'=>$job["color"],'style'=>$job["style"] ?? 'None','abutment'=>$job["abutment"] ?? 'None','implant'=>$job["implant"] ?? 'None','material_id'=> $job["material_id"],'case_id' => $case->id , 'doctor_id' =>$request->doctor, 'stage'=>1]);
                    $newJob->save();
                    $newJob->unit_price = material::FindOrFail($job["material_id"])->price   - ($this->getDiscount($newJob,$case)/count(explode(',',$newJob->unit_num)));

                    $newJob->save();
                } catch (\Exception $e) {
                    $request->flash();
                    return back()->with('error',"One of the jobs is missing 1 or more properties!" );
                }
                if($newJob->material->id != 6)
                {
                    $newJob->implant =null;
                    $newJob->abutment =null;
                    $newJob->save();
                }
            }


        /*
        *     STORING IMAGES
        */

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

        if(isset($request->discountCB)){
            $discount = new discount();
            $discount->discount = $request->discount_amount;
            $discount->case_id = $case->id;
            $discount->reason = $request->discount_reason;
            $discount->save();

            if ($discount->discount ==0) $discount->delete();
        }

        /*
        *     SAVING THE NOTE
        */

        if($request->note){
            $note = new note();
            $note->case_id = $case->id;
            $note->note = $request->note;
            $note->written_by =  Auth()->user()->id;
            $note->save();}

        //DB::rollBack();
        DB::commit();

        return $case;
    }
    public function getDiscount($job,$case){
        $discounts = $case->client->discounts;
        $discountOfMaterial = $discounts->where('material_id', $job->material_id)->first();
        // No Discount
        if(!$discountOfMaterial) return 0;

        //Fixed Discount
        if ($discountOfMaterial && $discountOfMaterial->type === 0) {
            return count(explode(',',$job->unit_num)) *$discountOfMaterial->discount;

            // Percentage Discount
        } else if ($discountOfMaterial->type) {
            return  (count(explode(',',$job->unit_num))) * ($job->material->price * ($discountOfMaterial->discount / 100));
        }
    }

    public function issueInvoice($job){
        $case = sCase::findOrFail($job->case_id);
        $invoiceApplicable = true;
        $invoiceAmount = 0;
        foreach($case->jobs as $job) {

            $jobPrice = (count(explode(',',$job->unit_num)) * $job->material->price) - $this->getDiscount($job,$case);
            $invoiceAmount += $jobPrice;
        }
        if ($invoiceApplicable){
            $invoice = new invoice();
            $invoice->status =1;
            $invoice->case_id =$case->id;
            $invoice->doctor_id =$case->client->id;
            if(isset($case->discount))
            {
                $invoice->amount_before_discount =$invoiceAmount;
                $invoice->amount =$invoiceAmount - $case->discount->discount;

            }
            else
            {
                $invoice->amount =$invoiceAmount;
                $invoice->amount_before_discount =$invoiceAmount;
            }
            $invoice->save();
        }
    }

    public function applyInvoice($job){
        $case = sCase::with('invoice')->where('id',$job->case_id)->get();
        $allJobsCompleted = true;
        foreach($case[0]->jobs as $job)
            if($job->stage != -1)
                $allJobsCompleted = false;

        if ($allJobsCompleted)
        {
            $client = $case[0]->client;
            $invoice = $case[0]->invoice;
            $client->balance =  $client->balance + $invoice->amount;
            $invoice->status = 1;
            $invoice->date_applied = now();
            $invoice->save();
            $client->save();
        }
    }

}
