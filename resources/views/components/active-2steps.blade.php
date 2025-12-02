@php use App\Build; use App\job; use App\sCase; @endphp
    <!-- active-jobs-dialog.blade.php -->
@props([
    'title',
    'btnText',
    'type',
    'deviceId',
    'isBuilds' => false
])

@php
    // Get all builds for this device that have not been finished
    $builds = Build::where('device_used', $deviceId)
        ->whereNotNull('set_at')
        ->whereNull('finished_at')
        ->get();

    // Create an array to store job data for each build
    $buildData = [];

    // For each build, get its jobs and cases
    foreach ($builds as $build) {
        // Get all jobs with this build ID based on workflow type
        $buildJobs = [];

        if ($type == 'sintering') {
            $buildJobs = job::where('sintering_build_id', $build->id)->get();
        }else{
        \Log::info("type is not sintering");
        return;
        }

        // Count the jobs
        $jobCount = count($buildJobs);

        // Create data structure for this build
        $buildInfo = [
            'build' => $build,
            'jobCount' => $jobCount,
            'cases' => [],
            'hasJobs' => $jobCount > 0
        ];

        // Group jobs by case
        $jobsByCaseId = [];
        foreach ($buildJobs as $job) {
            $caseId = $job->case_id;
            if (!isset($jobsByCaseId[$caseId])) {
                $jobsByCaseId[$caseId] = [];
            }
            $jobsByCaseId[$caseId][] = $job;
        }
        $jobsByCaseId = sCase::whereIn('id', array_keys($jobsByCaseId))->get();

        // For each case, get case details and job info
        foreach ($jobsByCaseId as $caseId => $jobs) {
            $case = sCase::find($caseId);
            if (!$case) continue;

            // Count units
            $unitCount = 0;
            $jobTypes = [];

            foreach ($jobs as $job) {
                // Count units
                if (!empty($job->unit_num)) {
                    $units = explode(',', $job->unit_num);
                    $unitCount += count($units);
                } else {
                    $unitCount += 1;
                }

                // Get job type
                if ($job->jobType) {
                    $jobTypes[] = $job->jobType->name;
                }
            }

            // Deduplicate job types
            $jobTypes = array_unique($jobTypes);

            // Add case to build data
            $buildInfo['cases'][] = [
                'case' => $case,
                'jobs' => $jobs,
                'jobCount' => count($jobs),
                'unitCount' => $unitCount,
                'jobTypes' => implode(', ', $jobTypes)
            ];
        }

        // Add build data to collection
        $buildData[] = $buildInfo;
    }

    // Check if any jobs are active
    $hasActiveJobs = true;

@endphp

<div class="alrazi-workflow-modal" id="{{$deviceId}}casesListDialog" tabindex="-1" role="dialog">
    <div class="alrazi-workflow-dialog">
        <div class="alrazi-workflow-header">
            <h2 class="alrazi-workflow-title">{{ $title }}</h2>
            <button class="alrazi-close-button" onclick="closeDeviceDialog('{{ $deviceId }}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="alrazi-workflow-body">
            <div class="alrazi-jobs-container doubleStepFakeForm">
                    <div class="alrazi-jobs-list">
                        @foreach($buildData as $data)
                            @foreach($data['cases'] as $caseData)
                                @foreach($caseData['jobs'] as $job)
                                    <div class="alrazi-job-row" style="background-color: var(--main-blue);">
                                        <div class="alrazi-job-header">
                                            <div class="alrazi-job-checkbox">
                                                    <input type="checkbox"
                                                           name="jobId[]"
                                                           value="{{ $job->id }}"
                                                           class="alrazi-checkbox {{ $type }} active-blue-row"
                                                           checked
                                                           onclick="event.preventDefault();"
                                                           onchange="updateActionButtonState('{{ $deviceId }}', '{{ $type }}')">
                                                </div>
                                            <div class="alrazi-job-main-info">
                                                <div class="alrazi-job-title">
                                                    <div class="alrazi-job-doctor">{{ $caseData['case']->client->name }}</div>
                                                    <div class="alrazi-job-patient">{{ $caseData['case']->patient_name }}</div>
                                                </div>
                                                <div class="alrazi-job-details">
                                                    <div class="alrazi-job-type">{{$job->jobType ? $job->jobType->name : "No Type" }}</div>
                                                    <div class="alrazi-job-units">{{ $job->unit_num }}</div>
                                                </div>
                                            </div>

                                            <div class="alrazi-job-actions">
                                                <button class="alrazi-job-view-btn"
                                                        onclick="YSH_openSlidePanel({{ $caseData['case']->id }}, '{{ $type }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @endforeach
                    </div>
            </div>
        </div>

        <div class="alrazi-workflow-footer">
            <button type="button"
                    class="alrazi-button"
                    id="actionX-button-{{ $deviceId }}"
                    disabled
                    style="background-color: var(--main-green) "
                    onclick="processWorkflowAction('{{$deviceId}}', '{{$type}}', 'jobs' , 'complete')">
                COMPLETE
            </button>
        </div>
    </div>
</div>
</div>
<form id="process-form-{{ $deviceId }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="deviceId" value="{{ $deviceId }}">
    <input type="hidden" name="items" id="selected-items-{{ $deviceId }}" value="">
    <input type="hidden" name="action" id="action-type-{{ $deviceId }}" value="">
</form>

@foreach($buildData as $data)
    @foreach($data['cases'] as $caseData)
        <x-partiels.caseSlidePanel :case="$caseData['case']" :stageType="$type"/>
    @endforeach
@endforeach

<script>
    // Reset build selection and enable action button if a build is already selected
    document.addEventListener('DOMContentLoaded', function() {
        // Reset global variables
        window.selectedBuildId = null;
        selectedBuildId = null;

        // For 3D printing, we need to add a click handler for the entire row
        // const buildRows = document.querySelectorAll('.alrazi-build-row');
        // buildRows.forEach(row => {
        //     row.addEventListener('click', function(e) {
        //         // Skip if clicking on the toggle or details section
        //         if (e.target.closest('.alrazi-build-details') ||
        //             e.target.closest('.alrazi-build-toggle')) {
        //             return;
        //         }
        //
        //         // Find the radio button inside this row and click it
        //         const radio = this.querySelector('input[type="radio"]');
        //         if (radio) {
        //             radio.checked = true;
        //             // Trigger the onchange event manually
        //             const event = new Event('change');
        //             radio.dispatchEvent(event);
        //         }
        //     });
        // });
    });
</script>

<style>
    /* Empty state styling */
    .alrazi-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        text-align: center;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 1rem;
    }

    .alrazi-empty-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .alrazi-empty-message {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 500;
    }

    .alrazi-empty-case-message {
        padding: 15px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 10px 0;
        border: 1px dashed #ced4da;
    }

    /* Build list styling */
    .alrazi-builds-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }

    .alrazi-build-row {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /*.alrazi-build-header {*/
    /*    display: flex;*/
    /*    align-items: center;*/
    /*    padding: 16px;*/
    /*    gap: 15px;*/
    /*    cursor: pointer;*/
    /*    transition: background-color 0.2s;*/
    /*    position: relative;*/
    /*}*/

    .alrazi-build-header:hover {
        opacity: 0.9;
    }

    .alrazi-build-radio {
        flex-shrink: 0;
    }

    .alrazi-build-title {
        font-weight: 600;
        color: white;
        flex-grow: 1;
    }

    .alrazi-build-info {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
        margin-right: 20px;
    }

    .alrazi-build-date {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .alrazi-build-jobs-count {
        font-weight: 500;
        color: white;
        background-color: rgba(0, 0, 0, 0.2);
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .alrazi-build-toggle {
        margin-left: auto;
    }

    .alrazi-build-toggle i {
        color: white;
        transition: transform 0.3s;
    }

    .alrazi-build-details {
        display: none;
        padding: 0 16px 16px;
        background-color: #f8f9fa;
    }

    .alrazi-build-row.expanded .alrazi-build-details {
        display: block;
    }

    .alrazi-build-row.expanded .alrazi-build-toggle i {
        transform: rotate(180deg);
    }

    .alrazi-build-cases {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }

    .alrazi-case-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .alrazi-case-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .alrazi-case-doctor {
        font-weight: 500;
        color: #333;
    }

    .alrazi-case-patient {
        font-size: 0.9em;
        color: #666;
    }

    .alrazi-case-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 4px;
    }

    .alrazi-case-jobs-count {
        font-size: 0.85rem;
        color: #0056b3;
        font-weight: 500;
    }

    .alrazi-case-job-types {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }

    .alrazi-case-view-btn {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background-color 0.2s, color 0.2s;
    }

    .alrazi-case-view-btn:hover {
        background-color: #007bff;
        color: white;
    }

    /* Regular jobs list styling */
    .alrazi-jobs-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 16px;
    }

    .alrazi-job-row {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
    }

    .alrazi-job-header {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .alrazi-job-checkbox {
        flex-shrink: 0;
    }

    .alrazi-job-main-info {
        flex-grow: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alrazi-job-title {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .alrazi-job-doctor {
        font-weight: 500;
        color: #333;
    }

    .alrazi-job-patient {
        font-size: 0.9em;
        color: #666;
    }

    .alrazi-job-details {
        text-align: right;
    }

    .alrazi-job-type {
        font-size: 0.9em;
        color: #666;
    }

    .alrazi-job-units {
        font-weight: 500;
        color: #333;
    }

    .alrazi-job-actions {
        flex-shrink: 0;
    }

    .alrazi-job-view-btn {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background-color 0.2s;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .alrazi-job-view-btn:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }
</style>

