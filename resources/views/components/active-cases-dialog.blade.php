@php use App\Build;use App\job;use App\sCase;use App\Http\Controllers\OperationsUpgrade; @endphp

@props([
    'title',
    'btnText',
    'type',
    'deviceId',
    'isBuilds' => false
])

@php

    // Get stage configuration
    $stageConfig = OperationsUpgrade::STAGE_CONFIG;

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

        if ($type == 'milling') {
            $buildJobs = job::where('milling_build_id', $build->id)->get();
        } else if ($type == '3dprinting') {
            $buildJobs = job::where('printing_build_id', $build->id)->get();
        } else if ($type == 'sintering') {
            $buildJobs = job::where('sintering_build_id', $build->id)->get();
        } else if ($type == 'pressing') {
            $buildJobs = job::where('pressing_build_id', $build->id)->get();
        }

        // Count the jobs
        $jobCount = count($buildJobs);
        Log::info("Active cases dialog : jobCount: ".$jobCount);
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
         Log::info("Active cases dialog : jobsByCaseId count : ".count($jobsByCaseId));

        // For each case, get case details and job info
        foreach ($jobsByCaseId as $caseId => $jobs) {
            $case = sCase::find($caseId);
              Log::info("Active cases dialog : case : ". json_encode($case));
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
      Log::info("Active cases dialog device id : ".$deviceId." buildInfo : ". count($buildInfo['cases']));
Log::info("--------------------------------------------");
        }

        // Add build data to collection
        $buildData[] = $buildInfo;
    }
$hasActiveJobs =false;
foreach($buildData as $data)
                        {
                        Log::info("BUILD DATA: " .$data['build']->started_at);


 }

@endphp

@php
    $hasActiveJobs = collect($buildData)->contains(fn($data) => $data['build']->started_at !== null);
Log::info("-----------Dialog has Active Jobs -------: ".$hasActiveJobs);
@endphp
<style>
    .animated-button {
        position: relative;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 16px 36px;
        border: 4px solid;
        border-color: transparent;
        font-size: 16px;
        background-color: inherit;
        border-radius: 100px;
        font-weight: 600;
        color: greenyellow;
        box-shadow: 0 0 0 2px greenyellow;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .animated-button svg {
        position: absolute;
        width: 24px;
        fill: greenyellow;
        z-index: 9;
        transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .animated-button .arr-1 {
        right: 16px;
    }

    .animated-button .arr-2 {
        left: -25%;
    }

    .animated-button .circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        background-color: greenyellow;
        border-radius: 50%;
        opacity: 0;
        transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .animated-button .text {
        position: relative;
        z-index: 1;
        transform: translateX(-12px);
        transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .animated-button:hover {
        box-shadow: 0 0 0 12px transparent;
        color: #212121;
        border-radius: 12px;
    }

    .animated-button:hover .arr-1 {
        right: -25%;
    }

    .animated-button:hover .arr-2 {
        left: 16px;
    }

    .animated-button:hover .text {
        transform: translateX(12px);
    }

    .animated-button:hover svg {
        fill: #212121;
    }

    .animated-button:active {
        scale: 0.95;
        box-shadow: 0 0 0 4px greenyellow;
    }

    .animated-button:hover .circle {
        width: 220px;
        height: 220px;
        opacity: 1;
    }

    /* Active Cases Dialog Layout Styles */
    .alsolent-build-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        gap: 12px;
    }

    .alsolent-job-checkbox {
        flex-shrink: 0;
    }

    .alsolent-build-title {
        flex: 1;
        font-weight: 700;
        font-size: 18px;
        text-align: left;
        min-width: 0; /* Allows text to wrap/truncate */
        /*font-family: 'Nunito', 'Segoe UI', 'Tahoma', 'Arial Unicode MS', Arial, sans-serif;*/
        /*direction: rtl;*/
        /*unicode-bidi: bidi-override;*/
        line-height: 1.5;
    }

    .alsolent-build-title.alsolent-date-title {
        direction: ltr;
        unicode-bidi: normal;
        text-align: left;
    }

    .alsolent-build-units {
        flex-shrink: 0;
        font-weight: 600;
        font-size: 16px;
        color: white;
        margin-right: 8px;
        background-color: rgba(0, 0, 0, 0.4);
        padding: 4px 8px;
        border-radius: 4px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .alsolent-build-toggle {
        flex-shrink: 0;
        cursor: pointer;
        padding: 4px;
    }

    /* Case Items Table-like Layout */
    .alsolent-build-details {
        width: 100%;
    }

    .alsolent-build-cases {
        width: 100%;
    }

    .alsolent-case-item {
        width: 100%;
    }

    .alsolent-case-info-row {
        display: grid;
        grid-template-columns: 2fr 2fr 1fr auto;
        gap: 16px;
        align-items: center;
        padding: 8px 16px;
        border-bottom: 1px solid #eee;
        width: 100%;
        box-sizing: border-box;
        cursor: pointer;
    }


    .alsolent-case-patient,
    .alsolent-case-units {
        font-size: 16px;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;

        line-height: 1.5;
    }

    .alsolent-case-doctor {
        font-weight: 700;
        color: #333;
    }

    .alsolent-case-patient {
        color: #555;
    }

    .alsolent-case-units {
        color: white;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.3);
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .alsolent-case-view {
        text-align: center;
    }

    .alsolent-case-view-btn {
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

    .alsolent-case-view-btn:hover {
        background-color: #f8f9fa;
        color: #0056b3;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .alsolent-case-info-row {
            grid-template-columns: 1fr 1fr auto;
            gap: 8px;
        }

        .alsolent-case-units {
            display: none; /* Hide units on small screens */
        }

        .alsolent-build-title {
            font-size: 16px;
        }

        .alsolent-build-units {
            font-size: 14px;
            padding: 3px 6px;
        }
    }

    /* Improved Submit Button Styling */
    .alsolent-animated-submit-button {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 32px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        min-width: 120px;
        height: 48px;
        outline: none;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .alsolent-animated-submit-button.start-mode {
        background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        color: white;
    }

    .alsolent-animated-submit-button.complete-mode {
        background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
        color: white;
    }

    .alsolent-animated-submit-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .alsolent-animated-submit-button:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .alsolent-animated-submit-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .alsolent-animated-submit-button .button-text {
        position: relative;
        z-index: 2;
        transition: opacity 0.3s;
    }

    .alsolent-animated-submit-button .button-ripple {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .alsolent-animated-submit-button:active .button-ripple:not(:disabled) {
        width: 300px;
        height: 300px;
    }

    .alsolent-animated-submit-button .button-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .alsolent-animated-submit-button.loading .button-text {
        opacity: 0;
    }

    .alsolent-animated-submit-button.loading .button-loader {
        opacity: 1;
    }

    .alsolent-animated-submit-button .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Dialog Dismissal Enhancements */
    .alsolent-workflow-modal {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .alsolent-workflow-modal.active {
        animation: fadeIn 0.3s ease-out;
    }

    .alsolent-workflow-modal.closing {
        animation: fadeOut 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
        }
        to {
            opacity: 1;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        to {
            opacity: 0;
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
        }
    }

</style>
{{--{{collect($buildData)--}}
{{--    ->flatMap(fn($data) => $data['cases'])}}--}}
<div class="alsolent-workflow-modal" id="{{$deviceId}}casesListDialog" tabindex="-1" role="dialog"
     onclick="handleDialogBackdropClick(event, '{{ $deviceId }}')">
    <div class="alsolent-workflow-dialog" onclick="event.stopPropagation()">
        <div class="alsolent-workflow-header">
            <h2 class="alsolent-workflow-title">{{ $title }}</h2>
            <button class="alsolent-close-button" onclick="closeDeviceDialog('{{ $deviceId }}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="alsolent-workflow-body">
            <div class="alsolent-jobs-container">


                <div class="alsolent-builds-list">

                    @foreach($buildData as $data)

                        @php
                            $caseActive = ($data['build']->started_at !=null);
                        @endphp
                        <div class="alsolent-build-row">
                            <div class="alsolent-build-header"
                                 style="background-color: var({{ $caseActive ? '--main-blue' : '--main-orange' }}); "
                                 onclick=" toggleBuildDetails(this)">
                                <div class="alsolent-job-checkbox" onclick="event.preventDefault();">
                                    @if($caseActive)
                                        <input type="checkbox"
                                               name="jobId[]"
                                               value="{{$data['build']->id }}"
                                               data-group-id="{{$deviceId}}"
                                               class='alsolent-checkbox {{ $deviceId }}
                                                checkboxes-group-{{$deviceId}} {{$stageConfig[$type]['multiple-active']?'multiple-choice' :'single-choice'  }}
                                                checkboxes-group-{{$deviceId}}
                                                 {{ $type }}   active-blue-row'
                                               onclick="event.stopPropagation();"
                                               checked disabled
                                        />
                                        <input type="hidden" name="jobId[]" value="{{$data['build']->id }}"
                                               class="value-holder checkboxes-group-{{$deviceId}} active-values-holder-{{$deviceId}} alsolent-checkbox {{$type}}"
                                               checked/>
                                    @else
                                        <input type="checkbox"
                                               name="jobId[]"
                                               onclick="event.stopPropagation();"
                                               data-group-id="{{$deviceId}}"
                                               value="{{$data['build']->id }}"
                                               class="alsolent-checkbox {{ $deviceId }} {{ $type }} checkboxes-group-{{$deviceId}}  {{$stageConfig[$type]['multiple-active']?'multiple-choice' :'single-choice' }} inactive-orange-row"

                                        {{$hasActiveJobs ? 'disabled' : ''}}
                                        "
                                        >

                                    @endif
                                </div>

                                @php
                                    // Calculate total units for all stages
                                    $totalUnits = 0;
                                    foreach ($data['cases'] as $caseData) {
                                        $totalUnits += $caseData['unitCount'];
                                    }
                                @endphp

                                @if($type == 'sintering')
                                    {{-- For sintering, show formatted date instead of build name --}}
                                    <div class="alsolent-build-title alsolent-date-title">{{ $data['build']->created_at ? $data['build']->created_at->format('M d, Y') : 'Recent Build' }}</div>
                                @else
                                    {{-- For other stages, show build info --}}
                                    <div class="alsolent-build-title">{{ $data['build']->name }}</div>
                                @endif

                                <div class="alsolent-build-units">{{ $totalUnits }}</div>
                                <div class="alsolent-build-toggle">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <div class="alsolent-build-details">
                                <div class="alsolent-build-cases">

                                    @if(count($data['cases']) == 0)
                                        <div class="alsolent-empty-case-message">
                                            No cases found in this build
                                        </div>
                                    @else

                                        @foreach($data['cases'] as $caseData)

                                            <div class="alsolent-case-item">
                                                <div class="alsolent-case-info-row"
                                                     role="button"
                                                     tabindex="0"
                                                     onclick="YSH_openSlidePanel({{ $caseData['case']->id }}, '{{ $type }}', '{{ $deviceId }}')"
                                                     onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); YSH_openSlidePanel({{ $caseData['case']->id }}, '{{ $type }}', '{{ $deviceId }}'); }">
                                                    <div class="alsolent-case-doctor">{{ $caseData['case']->client ? $caseData['case']->client->name : 'No Client' }}</div>
                                                    <div class="alsolent-case-patient">{{ $caseData['case']->patient_name }}</div>
                                                    <div class="alsolent-case-units">{{ $caseData['unitCount'] }}</div>
                                                    <div class="alsolent-case-view">
                                                        <button type="button"
                                                                class="alsolent-case-view-btn"
                                                                onclick="event.stopPropagation(); YSH_openSlidePanel({{ $caseData['case']->id }}, '{{ $type }}', '{{ $deviceId }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach

                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="alsolent-workflow-footer">


{{--            <button type="button"  {{$hasActiveJobs ? '' : 'disabled'}} --}}
{{--            class="neon-action-button" id="actionXX-button--{{ $deviceId }}"--}}
{{--            onclick="submitDeviceDialog('{{ $deviceId }}', '{{ $type }}', '{{ $isBuilds ? 'build' : 'jobs' }}'--}}
{{--                    ,'{{ $hasActiveJobs ? 'complete' : 'start' }}')">--}}
{{--                {{ $hasActiveJobs ? 'COMPLETE' : 'START' }}--}}
{{--            </button>--}}

            <button type="button"
                    class="alsolent-animated-submit-button {{ $hasActiveJobs ? 'complete-mode' : 'start-mode' }}"
                    id="actionXX-button-{{ $deviceId }}"
                    {{$hasActiveJobs ? '' : 'disabled'}}
                    onclick="submitDeviceDialog('{{ $deviceId }}', '{{ $type }}', '{{ $isBuilds ? 'build' : 'jobs' }}'
                    ,'{{ $hasActiveJobs ? 'complete' : 'start' }}')">
                <span class="button-text">{{ $hasActiveJobs ? 'COMPLETE' : 'START' }}</span>
                <div class="button-ripple"></div>
                <div class="button-loader">
                    <div class="spinner"></div>
                </div>
            </button>
        </div>
    </div>
</div>

<!--  TODO: remove id from inputs and keep it in forms ID, target class, IDs would have duplicates
     مع اطيب المتنيات و احر التعازي
     -->

<form id="process-form-{{ $deviceId }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="deviceId" value="{{ $deviceId }}">
    <input type="hidden" name="items" id="selected-items-{{ $deviceId }}" value="">
    <input type="hidden" name="action" id="action-type-{{ $deviceId }}" value="">
    <input type="hidden" name="type" id="action-type-{{ $deviceId }}" value="{{ $type }}">
    <input type="hidden" class="buildsIdsHiddenInput{{$deviceId}}" name="buildsIdsHiddenInput{{$deviceId}}"
           id="action-buildsIds-{{ $deviceId }}" value="">
</form>




@foreach($buildData as $data)
    @foreach($data['cases'] as $caseData)
        <x-partiels.caseSlidePanel :case="$caseData['case']" :stageType="$type" :panelScope="$deviceId"/>
    @endforeach
@endforeach


<style>
    /* Empty state styling */
    .alsolent-empty-state {
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

    .alsolent-empty-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .alsolent-empty-message {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 500;
    }

    .alsolent-empty-case-message {
        padding: 15px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 10px 0;
        border: 1px dashed #ced4da;
    }

    .alsolent-empty-case-message {

    /* Build list styling */
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
    .alsolent-builds-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }

    .alsolent-build-row {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /*.alsolent-build-header {*/
    /*    display: flex;*/
    /*    align-items: center;*/
    /*    padding: 16px;*/
    /*    gap: 15px;*/
    /*    cursor: pointer;*/
    /*    transition: background-color 0.2s;*/
    /*    position: relative;*/
    /*}*/

    .alsolent-build-header:hover {
        opacity: 0.9;
    }

    .alsolent-build-radio {
        flex-shrink: 0;
    }


    .alsolent-build-title {
        font-weight: 600;
        color: white;
        flex-grow: 1;
    }

    .alsolent-build-info {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
        margin-right: 20px;
    }

    .alsolent-build-date {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .alsolent-build-jobs-count {
        font-weight: 500;
        color: white;
        background-color: rgba(0, 0, 0, 0.2);
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .alsolent-build-toggle {
        margin-left: auto;
    }

    .alsolent-build-toggle i {
        color: white;
        transition: transform 0.3s;
    }

    .alsolent-build-details {
        display: none;
        padding: 0 16px 16px;
        background-color: #f8f9fa;
    }

    .alsolent-build-row.expanded .alsolent-build-details {
        display: block;
    }

    .alsolent-build-row.expanded .alsolent-build-toggle i {
        transform: rotate(180deg);
    }

    .alsolent-build-cases {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }

    .alsolent-case-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .alsolent-case-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .alsolent-case-doctor {
        font-weight: 500;
        color: #333;
    }

    .alsolent-case-patient {
        font-size: 0.9em;
        color: #666;
    }

    .alsolent-case-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 4px;
    }

    .alsolent-case-jobs-count {
        font-size: 0.85rem;
        color: #0056b3;
        font-weight: 500;
        margin-left:40px;
    }

    .alsolent-case-job-types {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }

    .alsolent-case-view-btn {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background-color 0.2s, color 0.2s;
    }

    .alsolent-case-view-btn:hover {
        background-color: #007bff;
        color: white;
    }

    /* Regular jobs list styling */
    .alsolent-jobs-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 16px;
    }

    .alsolent-job-row {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
    }

    .alsolent-job-header {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .alsolent-job-checkbox {
        flex-shrink: 0;
    }

    .alsolent-job-main-info {
        flex-grow: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alsolent-job-title {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .alsolent-job-doctor {
        font-weight: 500;
        color: #333;
    }

    .alsolent-job-patient {
        font-size: 0.9em;
        color: #666;
    }

    .alsolent-job-details {
        text-align: right;
    }

    .alsolent-job-type {
        font-size: 0.9em;
        color: #666;
    }

    .alsolent-job-units {
        font-weight: 500;
        color: #333;
    }

    .alsolent-job-actions {
        flex-shrink: 0;
    }

    .alsolent-job-view-btn {
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

    .alsolent-job-view-btn:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

</style>

</div>
</div>
</div>
</div>
</div>
