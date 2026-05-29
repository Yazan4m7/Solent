<!-- machine-selection-dialog.blade.php -->
@props([
    'title',
    'btnText',
    'type',
    'devices',
    'stageId',
    'showBuildName' => true
])
<?php $escapedType = preg_replace('/\W/', '', $type); ?>
@php   $stageSpecs = ['milling' => ['route'=>'/set-multiple-cases','btnText'=>'NEST'],
     'sintering' => ['route'=>'/set-multiple-cases','btnText'=>"START"],
     'pressing' => ['route'=>'/set-multiple-cases','btnText'=>"SET"],
       '3dprinting' => ['route'=>'/','btnText'=>"SET"],
      ]; @endphp




<div class="Albasma-workflow-modal waiting-dialog" id="{{ $type }}-waiting" tabindex="-1" role="dialog">
    <div class="Albasma-workflow-dialog">
        <!-- Header with close button -->
        <div class="Albasma-workflow-header">
            <h2 class="Albasma-workflow-title">{{ $title }}</h2>
            <button class="Albasma-close-button" onclick="closeModal({id: '{{ $type }}', isWaiting:true})">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>


        <!-- Machine selection grid -->
        <div class="Albasma-workflow-body">
            <div class="Albasma-machines-grid">
                @foreach($devices->where('type', $stageId) as $device)
                    <div class="Albasma-machine-card {{ $type }}"
                         onclick="selectMachine(this, '{{ $type }}', {{ $device['id'] }})">
                        <div class="Albasma-machine-image-container">
                            <img src="{{ asset($device['img']) }}" alt="{{ $device['name'] }}" class="Albasma-machine-image">
                        </div>
                        <div class="Albasma-machine-name">{{ $device['name'] }}</div>
                    </div>
                @endforeach
            </div>
            @php
            $buildFieldName= ['milling' => 'Block', 'pressing' => 'Ring', 'delivery' => 'Assign','sintering' => 'START'  ];
                @endphp

            <!-- Build name input) -->

@if($type != "sintering")
                <div class="Albasma-form-group">

                <input type="text"
                           id="Albasma-build-name-{{ $type }}"
                           class="Albasma-form-control  {{$stageConfig[$type]['multiple-waiting']?'multiple-choice' :'single-choice' }} "
                           placeholder="Enter {{$buildFieldName[$type] ?? 'Build'}} name"
                           oninput="validateAndSetBuildName('{{ $type }}')">

                </div>

            @else
                <input type="hidden"
                       id="Albasma-build-name-{{ $type }}"
                       class="Albasma-form-control"
                       placeholder="Enter {{$buildFieldName[$type] ?? 'Build'}} name"
                       oninput="validateAndSetBuildName('{{ $type }}')">
    @endif

        </div>


        <!-- Action button -->
        <div class="Albasma-workflow-footer">
            <button type="button"
                    class="Albasma-button  {{ $escapedType }}"
                    id="Albasma-action-button-{{ $escapedType }}" style = "background-color: var({{$type != "sintering" ? '--main-orange' :  '--main-blue'}})"
                    disabled
                    onclick="submitWorkflow('{{ $escapedType }}')">
                {{ $stageSpecs[$type]['btnText'] }}
            </button>
        </div>
    </div>
</div>

<form id="hidden-form-{{ $type }}" method="POST" action="{{ $stageSpecs[$type]['route'] }}" class="d-none">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="deviceId" id="device-id-{{ $type }}" value="">
    <input type="hidden" name="WaitingPopupCheckBoxes{{ $type }}[]" id="case-ids-{{ $type }}" value="">
        <input type="hidden" name="buildName" id="build-name-{{ $type }}" value="">

</form>


<script>

    function setInnerTab(btnElement) {

        let id = btnElement.id;
        // Always use lowercase for 3dprinting
        if (id.toLowerCase().includes('3dprinting')) {
            id = id.replace(/3[dD][pP]rinting/i, '3dprinting');
        }
        Cookies.set('inner' + $(btnElement).attr('href'), id);
        console.log("set cookie for : " + 'inner' + $(btnElement).attr('href') + ' => ' + id);

        // Hide all inner tab panels for this stage
        const tablist = $(btnElement).closest('[role="tablist"]');
        const stageKey = $(btnElement).data('stageid');
        // Remove active/hidden from all panels for this stage
        $(`[aria-labelledby^='active-${stageKey}'], [aria-labelledby^='waiting-${stageKey}']`).attr('hidden', true).removeClass('active');
        // Show the selected panel
        $(`[aria-labelledby='${id}']`).removeAttr('hidden').addClass('active');

        // Update tab button states
        tablist.find('[role="tab"]').attr('aria-selected', false).attr('tabindex', -1);
        $(btnElement).attr('aria-selected', true).removeAttr('tabindex');


    }
</script>
