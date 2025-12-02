@php use App\sCase; @endphp
{{-- sinteringCasesModal   sinteringCasesModal    sinteringCasesModal   sinteringCasesModal --}}

@props([
    'title',
    'btnText',
    'type',
    'deviceId',
    'sinteringCases' => null,
    'inactiveSinteringCases' => null,
    'isBuilds' => false
])



<title>Milling Jobs </title>

<!-- fonts / icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@php
    // Make sure deviceId is defined before using it
    if (isset($deviceId)) {
        $sinteringCases = sCase::whereHas('jobs', function ($q) use ($deviceId) {
            $q->where('stage', 4);
//                ->where('device_id', $deviceId);
        })->get();
    } else {
        $sinteringCases = collect(); // Empty collection if deviceId is not provided
    }
@endphp
<!-- ───────────────────  overlay & dialog ─────────────────── -->

    <div class="Tect
-workflow-modal active"  id="48sinteringCasesModal"tabindex="-1" role="dialog" aria-modal="true">
    <div class="YSH-modal">
        <div class="YSH-modal-header">
            <h2 class="YSH-modal-title">
                SINTERING FURNACE
            </h2>
            <button class="YSH-close-button" onclick="closeModal({id: '{{$deviceId}}sinteringCasesModal',isWaiting: false, deviceId: {{$deviceId}}, exactId: '{{$deviceId}}sinteringCasesModal'})">✖
            </button> </div>

        <div class="YSH-modal-body">
            <div class="YSH-job-list-container">
                <div class="YSH-job-list">
                    <!-- example job record -->
                    @foreach($sinteringCases as $sinteringCase)
                        <div class="YSH-job-item">
                            <div class="YSH-job-info">
                                <div class="YSH-job-name">{{$sinteringCase->client->name}}</div>
                            </div>
                            <div class="YSH-job-info">
                                <div class="YSH-job-description">{{$sinteringCase->patient_name}}</div>
                            </div>
                            <div class="YSH-unit-count"><i class="fas fa-cubes"></i>&nbsp;{{$sinteringCase->jobsCountByStage(4)}} </div>
                            <button
                                onclick="YSH_openSlidePanel({{ $sinteringCase->id }})"
                                class="YSH-view-button"
                                data-case-id="{{ $sinteringCase->id }}">
                                <i class="fas fa-eye"></i></button>
                        </div>
                    @endforeach
                    <!-- /example -->
                </div>
            </div>
        </div>

        <div class="YSH-modal-footer">
            <button class="YSH-start-button"
                    style='background-color: var(--main-blue); color:white'
                    onclick="startJobs()">Start</button>
        </div>
    </div>
    </div>


<!-- toast -->
<div id="notify" class="YSH-notification"><i class="fas fa-info-circle YSH-notification-icon"></i><span>Saved!</span></div>






<form id="process-form-{{ $deviceId }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="deviceId" value="{{ $deviceId }}">
    <input type="hidden" name="items" id="selected-items-{{ $deviceId }}" value="">
    <input type="hidden" name="action" id="action-type-{{ $deviceId }}" value="">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" class="buildsIdsHiddenInput{{$deviceId}}" name="buildsIdsHiddenInput{{$deviceId}}"
           id="action-buildsIds-{{ $deviceId }}" value="">
</form>





    @foreach($sinteringCases as $case)
        <x-partiels.caseSlidePanel :case="$case" stageId="4"/>
    @endforeach



<style>
    /* Modal Structure */
    .YSH-modal {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 90vh;
        width: 100%;
        max-width: 600px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: modal-appear 0.3s ease-out;
    }

    @keyframes modal-appear {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Fixed Header */
    .YSH-modal-header {
        position: sticky;
        top: 0;
        background-color: white;
        padding: 16px 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .YSH-modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
    }

    .YSH-close-button {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
    }

    .YSH-close-button:hover {
        color: #333;
    }

    /* Scrollable Body */
    .YSH-modal-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }

    .YSH-job-list-container {
        height: 100%;
        overflow-y: auto;
        padding: 0 10px;
        /* Smooth scrolling */
        scroll-behavior: smooth;
    }

    /* Job List Styling */
    .YSH-job-list {
        padding: 10px;
    }

    .YSH-job-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        margin-bottom: 10px;
        background-color: #f9f9f9;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        animation: item-appear 0.3s ease-out;
    }

    @keyframes item-appear {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .YSH-job-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }

    .YSH-job-info {
        flex: 1;
        margin-right: 10px;

    }

    .YSH-job-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .YSH-job-description {
        font-size: 0.9rem;
        color: #666;
    }

    .YSH-unit-count {
        display: flex;
        align-items: center;
        font-weight: 500;
        color: #0056b3;
        margin-right: 15px;
    }

    .YSH-view-button {
        background-color: var(--main-blue, #0056b3);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.2s;
    }

    .YSH-view-button:hover {
        background-color: #004494;
        transform: scale(1.05);
    }

    /* Fixed Footer */
    .YSH-modal-footer {
        position: sticky;
        bottom: 0;
        background-color: white;
        padding: 16px 20px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        z-index: 10;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
    }

    .YSH-start-button {
        padding: 8px 24px;
        border: none;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.1s;
    }

    .YSH-start-button:hover {
        opacity: 0.9;
    }

    .YSH-start-button:active {
        transform: scale(0.98);
    }

    /* Empty state styling */
    .Tect
-empty-state {
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

    .Tect
-empty-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .Tect
-empty-message {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 500;
    }

    .Tect
-empty-case-message {
        padding: 15px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 10px 0;
        border: 1px dashed #ced4da;
    }

    /* Case Tiles Grid */
    .Tect
-cases-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        padding: 16px;
    }

    .Tect
-case-tile {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
        transition: all 0.2s ease;
        cursor: pointer;
        overflow: hidden;
    }

    .Tect
-case-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .Tect
-case-tile.active-case {
        border-left: 4px solid var(--main-blue);
    }

    .Tect
-case-tile-header {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
    }

    .Tect
-case-tile-checkbox {
        margin-right: 12px;
    }

    .Tect
-case-tile-title {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .patient-name {
        font-weight: 600;
        color: #333;
    }

    .doctor-name {
        font-size: 0.85em;
        color: #666;
    }

    .Tect
-case-tile-actions {
        margin-left: auto;
    }

    .Tect
-case-tile-details {
        padding: 12px 16px;
    }

    .Tect
-case-tile-detail {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 0.9em;
        color: #555;
    }

    .Tect
-case-tile-detail i {
        margin-right: 8px;
        color: var(--main-blue);
        width: 16px;
        text-align: center;
    }

    .Tect
-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        grid-column: 1 / -1;
        color: #666;
    }

    .Tect
-empty-state i {
        font-size: 2.5em;
        margin-bottom: 16px;
        color: #ccc;
    }

    /* Build list styling */
    .Tect
-builds-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }

    .Tect
-build-row {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /*.Tect
-build-header {*/
    /*    display: flex;*/
    /*    align-items: center;*/
    /*    padding: 16px;*/
    /*    gap: 15px;*/
    /*    cursor: pointer;*/
    /*    transition: background-color 0.2s;*/
    /*    position: relative;*/
    /*}*/

    .Tect
-build-header:hover {
        opacity: 0.9;
    }

    .Tect
-build-radio {
        flex-shrink: 0;
    }


    .Tect
-build-title {
        font-weight: 600;
        color: white;
        flex-grow: 1;
    }

    .Tect
-build-info {
        display: flex;
        flex-direction: row;
        align-items: flex-end;
        margin-right: 20px;
    }

    .Tect
-build-date {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .Tect
-build-jobs-count {
        font-weight: 500;
        color: white;
        background-color: rgba(0, 0, 0, 0.2);
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .Tect
-build-toggle {
        margin-left: auto;
    }

    .Tect
-build-toggle i {
        color: white;
        transition: transform 0.3s;
    }

    .Tect
-build-details {
        display: none;
        padding: 0 16px 16px;
        background-color: #f8f9fa;
    }

    .Tect
-build-row.expanded .Tect
-build-details {
        display: block;
    }

    .Tect
-build-row.expanded .Tect
-build-toggle i {
        transform: rotate(180deg);
    }

    .Tect
-build-cases {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }

    .Tect
-case-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .Tect
-case-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .Tect
-case-doctor {
        font-weight: 500;
        color: #333;
    }

    .Tect
-case-patient {
        font-size: 0.9em;
        color: #666;
    }

    .Tect
-case-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 4px;
    }

    .Tect
-case-jobs-count {
        font-size: 0.85rem;
        color: #0056b3;
        font-weight: 500;
        margin-left:40px;
    }

    .Tect
-case-job-types {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }

    .Tect
-case-view-btn {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background-color 0.2s, color 0.2s;
    }

    .Tect
-case-view-btn:hover {
        background-color: #007bff;
        color: white;
    }

    /* Regular jobs list styling */
    .Tect
-jobs-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 16px;
    }

    .Tect
-job-row {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
    }

    .Tect
-job-header {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .Tect
-job-checkbox {
        flex-shrink: 0;
    }

    .Tect
-job-main-info {
        flex-grow: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .Tect
-job-title {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .Tect
-job-doctor {
        font-weight: 500;
        color: #333;
    }

    .Tect
-job-patient {
        font-size: 0.9em;
        color: #666;
    }

    .Tect
-job-details {
        text-align: right;
    }

    .Tect
-job-type {
        font-size: 0.9em;
        color: #666;
    }

    .Tect
-job-units {
        font-weight: 500;
        color: #333;
    }

    .Tect
-job-actions {
        flex-shrink: 0;
    }

    .Tect
-job-view-btn {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .Tect
-job-view-btn:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

</style>

