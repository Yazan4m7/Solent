<?php

namespace App\Observers;

use App\abutmentDeliveryRecord;
use App\job;


class AbutmentsObserver
{
    /**
     * Handle the job "created" event.
     *
     * Table columns:
     * case_id
     * job_id
     * abutment_id
     * implant_id
     * received_by
     * received_on
     * notes
     * marked_received_by
     *
     * @return void
     */
    public function created(job $job)
    {
 //       dd("hi");
//        if ($job->hasAbutment())
//        $record = new abutmentDeliveryRecord();
//        $record->case_id = $job->case->id;
//        $record->job_id = $job->id;
//       // dd($record);
//        $record->save();



    }

    /**
     * Handle the job "updated" event.
     *
     * @return void
     */
    public function updated(job $job)
    {
//        if ($job->hasAbutment()) {
//            if (abutmentDeliveryRecord::where("job_id", $job->id)->first() == null) {
//                $record = new abutmentDeliveryRecord();
//                $record->case_id = $job->case->id;
//                $record->job_id = $job->id;
//                $record->save();
//            }
//        }
    }

    /**
     * Handle the job "deleted" event.
     *
     * @return void
     */
    public function deleted(job $job)
    {
        //
    }

    /**
     * Handle the job "restored" event.
     *

     * @return void
     */
    public function restored(job $job)
    {
        //
    }

    /**
     * Handle the job "force deleted" event.
     *

     * @return void
     */
    public function forceDeleted(job $job)
    {
        //
    }
}
