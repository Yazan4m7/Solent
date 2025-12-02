<?php
//
//namespace App\Models;
//
//use Illuminate\Database\Eloquent\Model;
//
//class Device extends Model
//{
//    /**
//     * Disable model caching to prevent incomplete object exceptions
//     */
//    protected $cacheable = false;
//
//    public function getActiveBuildsCount()
//    {
//        $activeCases = 0;
//        $builds = $this->builds()->whereNull('finished_at')->get();
//
//        foreach ($builds as $build) {
//            $jobs = $build->jobs;
//            foreach ($jobs as $job) {
//                $units = $this->parseJobUnits($job->job_num);
//                $activeCases += count($units);
//            }
//        }
//
//        return $activeCases;
//    }
//
//    public function getSetBuildsCount()
//    {
//        $waitingCases = 0;
//        $builds = $this->builds()->whereNotNull('finished_at')->get();
//
//        foreach ($builds as $build) {
//            $jobs = $build->jobs;
//            foreach ($jobs as $job) {
//                $units = $this->parseJobUnits($job->job_num);
//                $waitingCases += count($units);
//            }
//        }
//
//        return $waitingCases;
//    }
//
//    private function parseJobUnits($jobNum)
//    {
//        $units = [];
//
//        if (str_contains($jobNum, ',')) {
//            $units = explode(',', $jobNum);
//        } elseif (str_contains($jobNum, ' ')) {
//            $units = explode(' ', $jobNum);
//        } else {
//            $units[] = $jobNum;
//        }
//
//        return $units;
//    }
//}
