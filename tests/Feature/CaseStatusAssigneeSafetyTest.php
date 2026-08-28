<?php

namespace Tests\Feature;

use Tests\TestCase;

class CaseStatusAssigneeSafetyTest extends TestCase
{
    public function test_case_status_tooltip_handles_a_missing_assignee_record(): void
    {
        $source = file_get_contents(app_path('sCase.php'));
        $start = strpos($source, 'public function getStatusToolTipHTML');
        $end = strpos($source, 'public function stageToText', $start);
        $method = substr($source, $start, $end - $start);

        $this->assertStringContainsString(
            '$assigneeName = optional($job->assignedTo)->first_name ?: \'Former user\';',
            $method
        );
        $this->assertStringNotContainsString('$job->assignedTo->first_name', $method);
    }

    public function test_demo_seeders_only_assign_jobs_to_non_deleted_users(): void
    {
        $migrationPaths = [
            database_path('migrations/2026_05_30_001800_seed_demo_doctors_and_clinical_cases.php'),
            database_path('migrations/2026_07_19_000001_expand_demo_clinical_dataset.php'),
        ];

        foreach ($migrationPaths as $migrationPath) {
            $source = file_get_contents($migrationPath);

            $this->assertStringContainsString(
                "DB::table('users')->whereNull('deleted_at')->where('is_admin', 1)->value('id')",
                $source
            );
            $this->assertStringContainsString(
                "DB::table('users')->whereNull('deleted_at')->value('id')",
                $source
            );
        }
    }

    public function test_historical_job_and_note_users_include_soft_deleted_records(): void
    {
        $jobSource = file_get_contents(app_path('job.php'));
        $noteSource = file_get_contents(app_path('note.php'));

        $this->assertStringContainsString(
            "belongsTo('App\\User', 'assignee', 'id')->withTrashed()",
            $jobSource
        );
        $this->assertStringContainsString(
            "belongsTo('App\\User', 'written_by', 'id')->withTrashed()",
            $noteSource
        );
    }
}
