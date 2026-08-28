@props([
    'case',
    'stageType' => '3dprinting',
    'panelScope' => null,
    'stageName' => null,
    'stageNumber' => null,
    'caseState' => null,
])

@php
    $panelSuffix = collect([$panelScope, $stageType, $case->id])
        ->filter(fn ($value) => filled($value))
        ->implode('-');
    $permissions = Cache::get('user' . Auth::id());
    $isAdmin = Auth::user()->is_admin;
    $canAssignEmployees = $isAdmin || ($permissions && $permissions->contains('permission_id', 129));
    $currentStageJob = $stageNumber ? $case->jobs->where('stage', $stageNumber)->first() : null;
    $hasAssignee = $currentStageJob && $currentStageJob->assignee;
    $isUserCase = $currentStageJob && (int) $currentStageJob->assignee === (int) Auth::id();
    $canBeFinished = true;

    if ($stageName === 'Finishing') {
        $canBeFinished = $case->allUnitsAtFinishing() && $case->abutmentsReceived();
    }

    $canComplete = $isUserCase && $canBeFinished;
    $assignmentModalId = $stageName === 'Delivery'
        ? 'myModal' . $case->id
        : 'assignModal' . $stageName . $case->id;
@endphp

<x-partiels.caseActionsModal
    :case="$case"
    :modalId="'caseActionsModal-' . $panelSuffix">
    <x-slot name="operationsActions">
        @if($stageNumber && $caseState === 'waiting')
            <form class="solent-case-actions-modal__operation-form"
                  action="{{ $stageName === 'Delivery' ? route('delivery-accept', $case->id) : route('assign-to-me', ['caseId' => $case->id, 'stage' => $stageNumber]) }}"
                  method="GET">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-user-check" aria-hidden="true"></i>
                    {{ $stageName === 'Delivery' ? (trans('ui.dom')['Take'] ?? 'Take') : (trans('ui.dom')['Assign To Me'] ?? 'Assign To Me') }}
                </button>
            </form>

            @if($stageName === 'QC')
                <a class="btn btn-info" href="{{ route('assign-and-finish', ['caseId' => $case->id, 'stage' => $stageNumber]) }}">
                    <i class="fas fa-arrow-trend-up" aria-hidden="true"></i> Assign &amp; Complete
                </a>
            @endif

            @if($canAssignEmployees && $stageName !== '3DPrinting')
                <button type="button" class="btn btn-warning" data-dismiss="modal"
                        data-toggle="modal" data-target="#{{ $assignmentModalId }}">
                    <i class="fas fa-user-gear" aria-hidden="true"></i>
                    {{ $hasAssignee ? 'Re-Assign' : 'Assign to' }}
                </button>
            @endif
        @endif

        @if($stageNumber && $caseState === 'active')
            @if($isAdmin && $canBeFinished && !$isUserCase)
                <a class="btn btn-success" href="{{ route('complete-by-admin', ['id' => $case->id, 'stage' => $stageNumber]) }}">
                    <i class="fas fa-check-double" aria-hidden="true"></i> Override Complete
                </a>
            @else
                <form class="solent-case-actions-modal__operation-form"
                      action="{{ route('finish-case', ['caseId' => $case->id, 'stage' => $stageNumber]) }}"
                      method="GET">
                    <button type="submit" class="btn btn-success" {{ $canComplete ? '' : 'disabled' }}>
                        <i class="fas fa-check" aria-hidden="true"></i>
                        {{ $canComplete ? 'Complete' : 'Case cannot be completed' }}
                    </button>
                </form>
            @endif

            @if($stageName === 'Milling')
                <button type="button" class="btn btn-dark" data-dismiss="modal"
                        data-toggle="modal" data-target="#MEX{{ $case->id }}">
                    <i class="fas fa-industry" aria-hidden="true"></i> Externally Milled
                </button>
            @endif

            @if($stageName === 'Delivery')
                <a class="btn btn-outline-info" href="{{ route('delivered-in-box', $case->id) }}">Delivered In Box</a>
                @if($case->delivered_to_client == 1 && ($isAdmin || ($permissions && $permissions->contains('permission_id', 9))))
                    <a class="btn btn-outline-secondary" href="{{ route('receive-voucher', $case->id) }}">Receive Voucher</a>
                @endif
            @endif

            <a class="btn btn-outline-danger" href="{{ route('reset-to-waiting', ['id' => $case->id, 'stage' => $stageNumber]) }}">
                <i class="fas fa-rotate-left" aria-hidden="true"></i> Reset To Waiting
            </a>
        @endif
    </x-slot>
</x-partiels.caseActionsModal>
