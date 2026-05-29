<?php

namespace App\Http\Traits;

use App\caseTag;
use App\failureCause;
use App\invoice;
use App\job;
use App\note;
use App\payment;
use App\sCase;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;

/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
trait helperTrait
{

    function countUnitsInBuild($buildId, $buildType = 'milling')
    {
        return job::where('is_active', 1)
            ->where("{$buildType}_build_id", $buildId)
            ->selectRaw('SUM(CHAR_LENGTH(unit_num) - CHAR_LENGTH(REPLACE(unit_num, ",", "")) + 1) as total_units')
            ->value('total_units') ?? 0;
    }
    public function lowestJobStageApplicable($job, $caseId)
    {
        $case = sCase::findOrFail($caseId);
        if ($this->isCaseFinished($caseId))
            return -1;
        // doesn't has jobs other than the one being created
        else if (!$this->caseHasNoJobsOtherThan($case, $job))
            return 1;
        else if (!$case->hasModels() && $job->jobType->id == 4)
            return 1;
        else {

            $lowestStage = $this->lowestStageInACase($job->case, $job->jobType);
            $nextStage = 1;
            $lastSuccessStage = 1;
            while (true) {
                $nextStage = $this->getJobNextStageHelper($job->material, $nextStage);
                if ($nextStage > $lowestStage) break;
                $lastSuccessStage = $nextStage;
            }
            $job->stage = $lastSuccessStage;
            $job->save();
            return $lastSuccessStage;
        }
    }
    public function lowestStageInACase($case, $jobType)
    {
        $stage = 1;


        foreach ($case->jobs as $job)
            if ($job->stage)
                if ($job->stage > $stage)
                    if ($jobType->teeth_or_jaw == $job->jobType->teeth_or_jaw)
                        $stage = $job->stage;
        return $stage;
    }
    public function getJobNextStageHelper($material, $currentStage)
    {



        if ($material->design && $currentStage < 1)  return 1;
        if ($material->mill && $currentStage < 2) return 2;
        if ($material->print_3d && $currentStage < 3) return 3;
        if ($material->sinter_furnace && $currentStage < 4) return 4;
        if ($material->press_furnace && $currentStage < 5) return 5;
        if ($material->metal_work && $currentStage < 9) return 9;

        // Furnace stages (4,5,9) are mutually exclusive and all come BEFORE finish/qc/delivery
        // Must check if currentStage is a furnace stage to allow progression to 6/7/8
        $furnaceStages = [4, 5, 9];
        if ($material->finish && ($currentStage < 6 || in_array($currentStage, $furnaceStages))) return 6;
        if ($material->qc && ($currentStage < 7 || in_array($currentStage, $furnaceStages))) return 7;
        if ($material->delivery && ($currentStage < 8 || in_array($currentStage, $furnaceStages))) return 8;

        // if all jobs in delivery: return this
        return 100;
        //abort('403', "Error in getting job next stage in helper trait");
    }
    public function stageToText($stage)
    {

        switch ($stage) {
            case "1":
                return "Design";
                break;
            case "2":
                return "Milling";
                break;
            case "3":
                return "3D Printing";
                break;
            case "4":
                return "Sintering Furnace";
                break;
            case "5":
                return "Pressing Furnace";
                break;
            case "9":
                return "Metal Work";
                break;
            case "6":
                return "Finish & Build up";
                break;
            case "7":
                return "Quality Control";
                break;
            case "8":
                return  "Delivery";
            case "-1":
                return  "Completed";
                break;
            case "0":
                return  "Completed";
                break;
        }
        return "STT Error";
    }
    public function getLastNDays($days, $format = 'd/m')
    {
        $m = date("m");
        $de = date("d");
        $y = date("Y");
        $dateArray = array();
        for ($i = 0; $i <= $days - 1; $i++) {
            $dateArray[] =  date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        }
        return array_reverse($dateArray);
    }
    public function isCaseFinished($caseId)
    {
        $case = sCase::findOrFail($caseId);
        if ($case->jobs->count() == 0) return false;
        else if (isset($case->actual_delivery_date) || $case->delivered_to_client == 1) return true;
        else return false;
    }
    public function caseHasNoJobs($caseId)
    {
        $case = sCase::findOrFail($caseId);
        if ($case->jobs->count() == 0)
            return true;
        else
            return false;
    }
    public function caseHasNoJobsOtherThan($case, $job)
    {
        foreach ($case->jobs as $jobI)
            if ($jobI->id != $job->id)
                return true;
        return false;
    }

    public function getQuery($sql)
    {
        $query = str_replace(array('?'), array('\'%s\''), $sql->toSql());
        $query = vsprintf($query, $sql->getBindings());
        return $query;
    }
    public function getActiveTab()
    {
        if (!isset($_COOKIE['activeOuterTab'])) {
            $_COOKIE['activeOuterTab'] = "#design";
            return 0;
        }
        switch ($_COOKIE['activeOuterTab']) {
            case "#design":
                return 0;
                break;
            case "#milling":
                return 1;
                break;
            case "#printing":
                return 2;
                break;
            case "#sintering":
                return 3;
                break;
            case "#pressing":
                return 4;
                break;
            case "#finishing":
                return 5;
                break;
            case "#QC":
                return 6;
                break;
            case "#delivery":
                return 7;
                break;
            default:
                return  0;
                break;
        }
    }
    public function clientDiscount4rejection($job, $case)
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
            return (count(explode(',', $job->unit_num))) * ($job->unit_price * ($discountOfMaterial->discount / 100));
        }
    }
    public function createTag($case, $tagId)
    {
        $caseTags = caseTag::where(['case_id' => $case->id, 'tag_id' => $tagId])->get();
        if ($caseTags->count() > 0) return false;
        else {

            $newTag = new caseTag(['case_id' => $case->id, 'tag_id' => $tagId, 'added_by' => Auth()->user()->id]);
            $newTag->save();
        }
    }
    public function filterByStage($cases, $stage)
    {
        $filteredList = collect([]);
        $jobFound = false;
        foreach ($cases as $case) {
            $jobFound = false;
            foreach ($case->jobs as $job)
                if ($job->stage == $stage) {
                    $jobFound = true;
                    break;
                }
            if ($jobFound)
                $filteredList->push($case);
        }
        return $filteredList;
    }
    public function createRejectionNote($case, $failureLog)
    {
        $note = new note();
        $note->case_id = $case->id;
        $note->note = "Rejected";
        if (isset($failureLog->cause_id))
            $note->note = $note->note . ', ' . failureCause::findOrFail($failureLog->cause_id)->text;
        if (isset($failureLog->explanation))
            $note->note = $note->note . '( ' . $failureLog->explanation . ')';

        $note->written_by =  Auth()->user()->id;
        $note->save();
    }
    public function createRepeatNote($old_case, $new_case, $failureLog)
    {
        $note = new note();
        $note->case_id = $new_case->id;
        $note->note = "Repeated";
        if (isset($failureLog->cause_id))
            $note->note = $note->note . ', ' . failureCause::findOrFail($failureLog->cause_id)->text;
        if (isset($failureLog->explanation))
            $note->note = $note->note . '( ' . $failureLog->explanation . ')';
        $note->note = $note->note . ' | Original Case ID:' . $old_case->id;
        $note->written_by =  Auth()->user()->id;
        $note->save();
    }
    public function createModificationNote($case, $failureLog)
    {
        $note = new note();
        $note->case_id = $case->id;
        $note->note = "Modification";
        if (isset($failureLog->cause_id))
            $note->note = $note->note . ', ' . failureCause::findOrFail($failureLog->cause_id)->text;
        if (isset($failureLog->explanation))
            $note->note = $note->note . '( ' . $failureLog->explanation . ')';

        $note->written_by =  Auth()->user()->id;
        $note->save();
    }
    public function createRedoNote($case, $failureLog)
    {
        $note = new note();
        $note->case_id = $case->id;
        $note->note = "Redo";
        if (isset($failureLog->cause_id))
            $note->note = $note->note . ', ' . failureCause::findOrFail($failureLog->cause_id)->text;
        if (isset($failureLog->explanation))
            $note->note = $note->note . '( ' . $failureLog->explanation . ')';

        $note->written_by =  Auth()->user()->id;
        $note->save();
    }
    public function lastMonthsAsYYYYMM($months)
    {
        $dates = array();

        for ($i = $months - 1; $i >= 0; $i--) {

            $month = today()->subMonth($i)->format("Y-m");
            array_push($dates, $month);
        }

        return $dates;
    }
    public function parseMonthsRange($rangeInputValue)
    {
        $date = explode(' - ', $rangeInputValue);
        $start =  Carbon::CreateFromFormat('M Y', $date[0]);
        $end = Carbon::CreateFromFormat('M Y', $date[1]);

        $months = [];
        while ($start <= $end) {
            $months[] = $start->format('Y-m');
            $start->modify('First day of +1 month');
        }

        //dd($months);
        return $months;
    }
    public function getUnitsCountOfCasesObjects($cases)
    {
        if (count($cases) < 1) return 0;
        $numOfUnits = 0;
        foreach ($cases as $case) {
            foreach ($case->jobs as $job)
                if ($job->material->count_as_unit == 1)
                    $numOfUnits += count(explode(',', $job->unit_num));
        }
        return $numOfUnits;
    }
    public function getUnitsCountOfJobsObjects($jobs)
    {
        if (count($jobs) < 1) return 0;
        $numOfUnits = 0;
        foreach ($jobs as $job)
            if ($job->material->count_as_unit == 1)
                $numOfUnits += count(explode(',', $job->unit_num));
        return $numOfUnits;
    }
    public function getCompletedCasesInLastNDays($days)
    {
        $arr = array();
        foreach ($days as $day) {
            array_push($arr, sCase::where('actual_delivery_date', 'like', '%' . $day . '%')->get());
        }
        return $arr;
    }
    public function getCollectionsInLastNDays($days)
    {
        $arr = array();
        foreach ($days as $day) {
            array_push($arr, payment::where('created_at', 'like', '%' . $day . '%')->sum('amount'));
        }
        return $arr;
    }
    public function getValueOfCasesObjects($cases)
    {
        $valueOfCases = 0;
        foreach ($cases as $case) {
            $valueOfCases += $case->invoice != null ? $case->invoice->amount : 0;
        }
        return $valueOfCases;
    }
    public function getCasesInLastNDays($days)
    {
        $arr = array();
    }
    public function generateAccessToken()
    {
        $accessTokenLink = 'https://oauth2.googleapis.com/token';


        $message = [
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion" => $this->generateJWT()
        ];


        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $ch = curl_init($accessTokenLink);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // Execute cURL session
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $json = json_decode($response, true);
        return $json["access_token"];
    }
    public function generateJWT()
    {

        $googleConfig = [
            'iss' => 'sigmalab@sigma-f8312.iam.gserviceaccount.com',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDVMjAPM6z/N0Ni\njHx1cdAMQN2nNjCLnRx5CZIy2mhT709U75EAEmrWrQjtlIVTsOfwBudim+YqDjLw\nrnxTHDjwBNsAogxVmu8fj5Di1z3mjfMlWajeyYGXaT0SVcblM53Lc5MhJxtCJk28\nWcGHmgVT1o0hewNuFf9qgDZyf2vbXbSsmYQVz1Rc/v0PIHd8rHreH5W5AmyTcZhh\nZSk5+MYxl1uEUlAnKZ16csjrT5AV8IyassK3Ru1QSP7loAmxi501QVxwZ9OvBJS8\nadcAL9H3YwFicg9eyetSleOiyfbTw6jH91PZS21jaUkD0oKGEFNQnvJywB81uvcH\nO9FZ7SWJAgMBAAECggEAL5qMu6A8xSHoVHVtBuZaX5oORBtn/Iygwm/+Koe1GuTJ\nEHyLonn6TCQH5dCvcpACQgiwmsaXvpU8D5zOWtpm5kUXR41ndqfpM+FhJx2Lj1Lr\n00+xUsmou4++mLz5c80yMy8Dz7fFMOCPo/pgqbAc92rlSXAHxIl55iRpw+gqw6jA\n6YB0P9vGFe8dGTQmamnGNu5jtRDWaNeGOYmBGON0gXsp+I2dGjNmMOwDYOWDR+CF\nKSqyikS+LFrvXXvmqVfBPAqV0bWCCytkAEE4ddGtyhW03VMaNS7UlMaNWTVkSsxb\nEA57dcRZmFsEmz9updR23OfripMmmmas2p0TGgmbuQKBgQDt+yFpmcvYttQm1QD/\nG065hwbcvoBwV+eBHV0lSRnRLeQ4OIC9WfLW/CI4Foe68OygVhh5DSGmdBvZIraJ\n2ooYTmOSxULGJZ8D/wUnLWjOymljG9EKjiLOt8+Eiv3LVginjb6B9QSNCQ8vxwD5\nS01yPu/hfARuoh9JWRT5MBrbFQKBgQDlVqRga58IZy0jAMyUwTIUN
            wcuq8HbUHRs\nWyMTkKTIRAFPpg99uXaNUSrniJZAToGOYfFaMDuk3yYBjMxViyllRqrP+FKwyeGT\n//sSNaJXxccEd5h2sBzyicSuXOmcydPHZLkc1QQ+ZczDrZE9AM0i1ycBvDrBTNA3\nr6zLAX5tpQKBgQCGJxsevGP9NpNBkLGPHYWzcDqeFYWxztviHPt1GVBEaupMBw4L\nr7kFF/zyQUEiUM4TVHVXR9/ARZOtQ7RC4b8XFJltE2Yg7PRG/GubOi3q5I+kHvoo\nSRe2EEgbH38SMN2QFodeGxEFsCWveS9DWP+/d1sicRbOhvW8E0uPbV62QQKBgC2M\nP6lGtpccpsJE7ly84g1RwINsaVv9ZqH+l8DTAWck2n3PJVR6+Sin7jV90xmCfgih\nOyYGXlIoX4v/QrXapaYPmu0jDIlADyUtuder/0ofZZ9lgUpRP+6LnhxjJ6KUExOO\n1ZT8WZNq9HgIiMfs2NEKmhymHaU2dEQbB95ptYphAoGAIyt1PWeDS0itBpdiU/Ex\nlFBJJThpFpgPeniEeLPHYwxmSyxZA+YGlaDL3FUKFFzXd6dXzVSXEYWCzCxXdCaD\nlIWsg9FEDR10izL7tWFMRrh4xI+LerkRwDh9tvCdA9qCasuhYKhlrXqNZdfEth5p\nObe4WbY0++PGApnSltuiTNc=\n-----END PRIVATE KEY-----\n\\",
            'aud' => 'https://www.googleapis.com/oauth2/v4/token',
            'exp' => time() + 3600,
            "scope" => "https://www.googleapis.com/auth/firebase.messaging", // Expiration time (1 hour from now)
            "iat" =>  time()
        ];

        // Create the JWT
        $jwt = JWT::encode($googleConfig, $googleConfig['private_key'], 'RS256');

        return $jwt;
    }

    function contains($needle, $haystack)
    {
        return strpos($haystack, $needle) !== false;
    }
    public function sendCaseNotification($token, $title, $body)
    {

        $fcmsendUrl = 'https://fcm.googleapis.com/v1/projects/sigma-f8312/messages:send';
        $notificationMessage = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title ?? "N/A",
                    "body" => $body ?? "N/A",

                ],
                "data" => [
                    "title" => $title ?? "N/A",
                    "body" => $body ?? "N/A",
                    "click_action" => "openCompletedCases",

                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "case.wav"
                        ]
                    ]
                ]
            ]
        ];


        $jsonMessage = json_encode($notificationMessage);
        $headers = [
            'Authorization: Bearer ' . $this->generateAccessToken(),
            'Content-Type: application/json; charset=utf-8',
        ];
        $ch = curl_init($fcmsendUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonMessage);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        curl_close($ch);

    }

    public function sendPaymentNotification($token, $title, $body)
    {


        $fcmsendUrl = 'https://fcm.googleapis.com/v1/projects/sigma-f8312/messages:send';
        //dd($token);
        // Create the notification message
        $notificationMessage = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title ?? "N/A",
                    "body" => $body ?? "N/A",
                    //"sound" => "default",
                    // "android_channel_id"=> "123"
                ],
                "data" => [
                    "title" => $title ?? "N/A",
                    "body" => $body ?? "N/A",
                    "click_action" => "OpenAccountStatement" // 0 => open app, 1 => open case, 2 => open payment
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "content-available" => 1,
                            "sound" => "payment.wav"
                        ]
                    ]
                ]
            ]
        ];

        $jsonMessage = json_encode($notificationMessage);
        $headers = [
            'Authorization: Bearer ' . $this->generateAccessToken(),
            'Content-Type: application/json; charset=utf-8',
        ];


        $ch = curl_init($fcmsendUrl);


        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonMessage);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);
    }


}
