<?php
/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
namespace App\Http\Controllers;

use App\Http\Traits\helperTrait;


class Helpers{
    use helperTrait;

    public function filterByStage($cases,$stage){
        $filteredList =collect([]);
        $jobFound=false;
        foreach ($cases as $case) {
            $jobFound=false;
            foreach ($case->jobs as $job)
                if ($job->stage == $stage){
                    $jobFound=true;
                    break;
            }
            if ($stage == 6)
            { if ($case->shouldShowForFinishing() &&$jobFound)
                    $filteredList->push($case);}
            else{ if($jobFound)
            $filteredList->push($case);}
        }
        return $filteredList;
    }
}
