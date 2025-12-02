<?php

namespace App\Helpers;

use App\sCase;
use Illuminate\Support\Facades\Cache;

class CaseCache
{
    public static function refresh($caseId)
    {
        // Anthropic Assistant: Optimized caching with eager loading
        Cache::forget("case_data_{$caseId}");

        $case = sCase::with([
            'jobs.material.jobtypes',
            'jobs.jobType',
            'jobs.assignedTo',
            'client',
            'tags.originalTagRecord',
            'notes.writtenBy'
        ])->find($caseId);

        if ($case) {
            Cache::put("case_data_{$caseId}", $case, now()->addMinutes(10));
            // Cache related counts separately for faster access
            Cache::put("case_units_{$caseId}", $case->unitsAmount(), now()->addMinutes(10));
            Cache::put("case_status_{$caseId}", $case->status(), now()->addMinutes(10));
        }
    }
}
