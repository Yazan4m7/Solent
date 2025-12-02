<!-- waiting-3dprinting-dialog.blade.php -->
@props([
    'title',
    'devices',
    'stageId'
])

<div class="alrazi-workflow-modal waiting-dialog" id="3dprinting-waiting" tabindex="-1" role="dialog">
    <div class="alrazi-workflow-dialog">
        <!-- Header with close button -->
        <div class="alrazi-workflow-header">
            <h2 class="alrazi-workflow-title waiting">{{ $title }}</h2>
            <button class="alrazi-close-button" onclick="closeModal({id: '3dprinting', isWaiting:true})">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Machine selection grid -->
        <div class="alrazi-workflow-body">
            <div class="alrazi-machines-grid">
                @if(isset($devices) && $devices->count() > 0)
                    @foreach($devices->where('type', $stageId) as $device)
                        <div class="alrazi-machine-card 3dprinting"
                             onclick="selectMachine(this, '3dprinting', {{ $device['id'] }})">
                            <div class="alrazi-machine-image-container">
                                <img src="{{ asset(isset($device['img']) ? $device['img'] : 'images/default-device.png') }}"
                                     alt="{{ isset($device['name']) ? $device['name'] : 'Device' }}" class="alrazi-machine-image">
                            </div>
                            <div class="alrazi-machine-name">{{ isset($device['name']) ? $device['name'] : 'Unknown Device' }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="no-devices-message">No devices available for this stage.</div>
                @endif
            </div>

            <!-- Build name input (only for 3D printing) -->
            <div class="alrazi-form-group">
                <label for="alrazi-build-name-3dprinting">Build Name</label>
                <input type="text"
                       id="alrazi-build-name-3dprinting"
                       class="alrazi-form-control   {{$stageConfig['3dprinting']['multiple-waiting']?'multiple-choice' :'single-choice' }}"
                       placeholder="Enter Build name"

                       oninput="validateAndSetBuildName('3dprinting')">

            </div>

        </div>

        <!-- Action button -->
        <div class="alrazi-workflow-footer">
            <button type="button"
                    class="alrazi-button 3dprinting"
                    id="alrazi-action-button-3dprinting" style = "background-color: var(--main-orange)"
                    disabled
                    onclick="submitWorkflow('3dprinting')">
                SET
            </button>

            <!-- From Uiverse.io by Creatlydev — Namespaced with YSH- -->
            <button class="YSH-button" style="--clr: #00ad54;">
                <span class="YSH-button-decor"></span>
                <div class="YSH-button-content">
                    <div class="YSH-button__icon">
                        <!-- SVG stays the same -->
                        <svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" width="24">
                            <!-- ... SVG content omitted for brevity ... -->
                        </svg>
                    </div>
                    <span class="YSH-button__text">SET</span>
                </div>
            </button>

        </div>
    </div>
</div>

<form id="hidden-form-3dprinting" method="POST" action="/set-cases-on-printer" class="d-none">
    @csrf
    <input type="hidden" name="type" value="3dprinting">
    <input type="hidden" name="deviceId" id="device-id-3dprinting" value="">
    <input type="hidden" name="WaitingPopupCheckBoxes3dprinting[]" id="case-ids-3dprinting" value="">
    <input type="hidden" name="buildName" id="build-name-3dprinting" value="">
</form>


