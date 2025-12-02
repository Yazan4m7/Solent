<!-- devices-block.blade.php -->
@props(['title', 'btnText', 'type', 'devices', 'stageId', 'showBuildName' => false, 'counts'])

@php use App\Build; @endphp


<div class="YSH-body">
    <div class="YSH-container">
        <div class="YSH-header">{{ $title }}</div>
        <div class="YSH-content">
            @if(isset($devices) && $devices->count() > 0)
                @foreach ($devices->where('type', $stageId) as $device)
                    @if($device && isset($device['id']))
                        @php
                            try {
                                $builds = Build::where('device_used', $device['id'])
                                            ->whereNull('finished_at')
                                            ->get();

                                $activeUnitsOrBuilds = $stageId==3 ?
                                    (isset($counts[$device['id']]['activeBuilds']) ? $counts[$device['id']]['activeBuilds'] : 0) :
                                    (isset($counts[$device['id']][$stageId]['active']) ? $counts[$device['id']][$stageId]['active'] : 0);

                                $waitingUnitsOrBuilds = $stageId==3 ?
                                    (isset($counts[$device['id']]['waitingBuilds']) ? $counts[$device['id']]['waitingBuilds'] : 0) :
                                    (isset($counts[$device['id']][$stageId]['waiting']) ? $counts[$device['id']][$stageId]['waiting'] : 0);

                                $hasJobs = $activeUnitsOrBuilds > 0 || $waitingUnitsOrBuilds > 0;
                                $hasActiveJobs = $activeUnitsOrBuilds > 0;
                                $hasWaitingJobs = $waitingUnitsOrBuilds > 0;
                                $isGrayScale = !$hasActiveJobs && $hasWaitingJobs;


                                \Log::info("Device {$device['id']} (" . (isset($device['name']) ? $device['name'] : 'Unknown') . ") - Type: {$type} - Active: {$activeUnitsOrBuilds}, Waiting: {$waitingUnitsOrBuilds}");
                            } catch (Exception $e) {
                                \Log::error("Error processing device: " . $e->getMessage());
                                $activeUnitsOrBuilds = 0;
                                $waitingUnitsOrBuilds = 0;
                                $hasJobs = false;
                            }
                        @endphp

                        <div class="YSH-device {{ $hasActiveJobs ? 'clickable' : 'inactive' }}"
                            onclick="{{ $hasActiveJobs || $hasWaitingJobs ? "handleClick(this, '{$device['id']}', '{$type}')" : 'showNoJobsMessage()' }}">
                            <div class="">
                                <img class="{{ !$hasActiveJobs ? 'grayscale' : '' }} machine-img" alt="Some device :)"
                                    src="{{ asset(isset($device['img']) ? $device['img'] : 'devicesImages/no_device_img.PNG') }}" onerror="this.onerror=null; this.src='devicesImages/no_device_img.PNG';" />
                                <div class="YSH-badge-container" style="display: {{ $hasJobs ? 'flex' : 'none' }};">

                                    <div class="YSH-badge YSH-badge-blue" title="{{ isset($activeUnitsOrBuilds) ? $activeUnitsOrBuilds : '-'  }} active jobs">
                                        {{ isset($activeUnitsOrBuilds) ? $activeUnitsOrBuilds : '-'  }}</div>
                                    @if($type!="sintering")
                                    <div class="YSH-badge YSH-badge-red" title="{{ isset($waitingUnitsOrBuilds) ? $waitingUnitsOrBuilds : '-' }} waiting jobs">
                                        {{ isset($waitingUnitsOrBuilds) ? $waitingUnitsOrBuilds : '-' }}</div>
                                    @endif
                                </div>

                            </div>
                            <div class="YSH-device-name">{{ isset($device['name']) ? $device['name'] : 'Unknown Device' }} </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="no-devices">No devices available for this stage.</div>
            @endif

        </div>
    </div>
</div>
