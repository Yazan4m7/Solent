@extends('layouts.app', [
    'pageSlug' => 'Production Control',
    'class' => ' production-control-v2-page',
])

@php
    $permissions = $permissions ?? Cache::get('user' . Auth::id());
    $isAdmin = (bool) Auth::user()->is_admin
        || ($permissions && $permissions->contains('permission_id', 122));
    $canAssignEmployees = $isAdmin || ($permissions && $permissions->contains('permission_id', 129));

    $productionStages = collect([
        ['key' => 'design', 'label' => 'Design', 'icon' => 'fa-pen-ruler', 'number' => 1, 'permission' => 1, 'active' => $aDesign ?? collect(), 'waiting' => $wDesign ?? collect(), 'employees' => $designers ?? collect()],
        ['key' => 'milling', 'label' => 'Milling', 'icon' => 'fa-gears', 'number' => 2, 'permission' => 2, 'active' => $aMilling ?? collect(), 'waiting' => $wMilling ?? collect(), 'employees' => $millers ?? collect()],
        ['key' => '3d-printing', 'label' => '3D Printing', 'icon' => 'fa-cube', 'number' => 3, 'permission' => 3, 'active' => $aPrinting ?? collect(), 'waiting' => $wPrinting ?? collect(), 'employees' => $printers ?? collect()],
        ['key' => 'sintering', 'label' => 'Sintering', 'icon' => 'fa-fire-flame-simple', 'number' => 4, 'permission' => 4, 'active' => $aSintering ?? collect(), 'waiting' => $wSintering ?? collect(), 'employees' => $sinteringUsers ?? collect()],
        ['key' => 'pressing', 'label' => 'Pressing', 'icon' => 'fa-compress', 'number' => 5, 'permission' => 5, 'active' => $aPressing ?? collect(), 'waiting' => $wPressing ?? collect(), 'employees' => $pressingUsers ?? collect()],
        ['key' => 'metal-work', 'label' => 'Metal Work', 'icon' => 'fa-hammer', 'number' => 9, 'permission' => 5, 'active' => $aMetalWork ?? collect(), 'waiting' => $wMetalWork ?? collect(), 'employees' => $pressingUsers ?? collect()],
        ['key' => 'finishing', 'label' => 'Finishing & Build up', 'icon' => 'fa-wand-magic-sparkles', 'number' => 6, 'permission' => 6, 'active' => $aFinishing ?? collect(), 'waiting' => $wFinishing ?? collect(), 'employees' => $finishingUsers ?? collect()],
        ['key' => 'quality-control', 'label' => 'Quality Control', 'icon' => 'fa-shield-halved', 'number' => 7, 'permission' => 7, 'active' => $aQC ?? collect(), 'waiting' => $wQC ?? collect(), 'employees' => $qcUsers ?? collect()],
        ['key' => 'delivery', 'label' => 'Delivery', 'icon' => 'fa-truck-fast', 'number' => 8, 'permission' => 8, 'active' => $aDelivery ?? collect(), 'waiting' => $wDelivery ?? collect(), 'employees' => $drivers ?? collect()],
    ])->filter(function (array $stage) use ($isAdmin, $permissions): bool {
        return $isAdmin || ($permissions && $permissions->contains('permission_id', $stage['permission']));
    })->map(function (array $stage): array {
        $stage['active'] = collect($stage['active'])->values();
        $stage['waiting'] = collect($stage['waiting'])->values();
        $stage['employees'] = collect($stage['employees'])->values();

        if ($stage['number'] === 6) {
            $stage['waiting'] = $stage['waiting']->filter(function ($case): bool {
                return method_exists($case, 'shouldShowForFinishing') ? $case->shouldShowForFinishing() : true;
            })->values();
        }

        $allCases = $stage['active']->concat($stage['waiting']);
        $stage['due_today'] = $allCases->filter(function ($case): bool {
            try {
                return $case->initial_delivery_date
                    && \Carbon\Carbon::parse(str_replace('T', ' ', (string) $case->initial_delivery_date))->isToday();
            } catch (\Throwable $exception) {
                return false;
            }
        })->count();

        return $stage;
    })->values();

    $requestedStageNumber = (int) request()->query('stage', 0);
    $firstStage = $productionStages->firstWhere('number', $requestedStageNumber) ?? $productionStages->first();
    $initialStage = $firstStage['key'] ?? '';
    $initialCaseId = (int) request()->query('case', 0);
    $initialStatus = request()->query('status') === 'waiting' ? 'waiting' : 'active';
@endphp

@push('css')
    <link href="{{ asset('assets/css/admin-production-control-v2.css') }}?v=20260901-4" rel="stylesheet">
@endpush

@section('content')
    <main class="pc-board"
          data-pc-root
          data-pc-selected-stage="{{ $initialStage }}"
          data-pc-selected-status="{{ $initialStatus }}"
          data-pc-selected-case="{{ $initialCaseId ?: '' }}"
          data-pc-processing-message="Processing your request...">
        <header class="pc-page-header">
            <div class="pc-page-title">
                <span class="pc-page-header__icon" aria-hidden="true"><i class="fa-solid fa-layer-group"></i></span>
                <div>
                    <span class="pc-eyebrow">Operations workspace</span>
                    <h1>Production Control</h1>
                    <p>Move through today&rsquo;s workload with every case and action in one place.</p>
                </div>
            </div>
            <div class="pc-header-actions">
                <span class="pc-admin-label"><i class="fa-solid {{ $isAdmin ? 'fa-user-shield' : 'fa-user-gear' }}" aria-hidden="true"></i>{{ $isAdmin ? 'Admin workspace' : 'My workspace' }}</span>
                @include('cases.partials.operations-view-toggle', ['operationsView' => 'new'])
            </div>
        </header>

        @if($productionStages->isEmpty())
            <section class="pc-empty-page">No production stages are available for this account.</section>
        @else
            <section class="pc-stage-navigator" aria-labelledby="pc-stage-heading">
                <header class="pc-section-heading">
                    <div>
                        <span class="pc-eyebrow">Workflow</span>
                        <h2 id="pc-stage-heading">Choose a production stage</h2>
                    </div>
                    <span class="pc-section-hint"><i class="fa-solid fa-arrows-left-right" aria-hidden="true"></i> Swipe to browse</span>
                </header>
                <nav class="pc-stage-strip" aria-label="Production stages">
                    @foreach($productionStages as $stage)
                        <button class="pc-stage-card"
                                type="button"
                                data-pc-stage-button
                                data-stage="{{ $stage['key'] }}"
                                data-label="{{ $stage['label'] }}"
                                data-ready="{{ $stage['waiting']->count() }}"
                                data-active="{{ $stage['active']->count() }}"
                                data-due="{{ $stage['due_today'] }}"
                                aria-pressed="{{ $stage['key'] === $initialStage ? 'true' : 'false' }}">
                            <span class="pc-stage-card__icon" aria-hidden="true"><i class="fa-solid {{ $stage['icon'] }}"></i></span>
                            <span class="pc-stage-card__copy">
                                <strong>{{ $stage['label'] }}</strong>
                                <small>{{ $stage['active']->count() + $stage['waiting']->count() }} cases</small>
                            </span>
                            <span class="pc-stage-card__metrics" aria-hidden="true">
                                <span><b>{{ $stage['waiting']->count() }}</b> ready</span>
                                <span><b>{{ $stage['active']->count() }}</b> active</span>
                            </span>
                        </button>
                    @endforeach
                </nav>
            </section>

            <section class="pc-control-deck" aria-label="Selected stage summary and case filters">
                <div class="pc-command-heading">
                    <span class="pc-eyebrow">Current stage</span>
                    <strong data-pc-stage-title>{{ $firstStage['label'] ?? '' }}</strong>
                </div>
                <div class="pc-status-toggle" role="group" aria-label="Work status">
                    <button class="pc-status-toggle__button pc-status-toggle__button--ready"
                            type="button"
                            data-pc-status-toggle="waiting"
                            aria-pressed="{{ $initialStatus === 'waiting' ? 'true' : 'false' }}">
                        <span>Ready</span>
                        <strong data-pc-summary="ready">0</strong>
                    </button>
                    <button class="pc-status-toggle__button pc-status-toggle__button--active"
                            type="button"
                            data-pc-status-toggle="active"
                            aria-pressed="{{ $initialStatus === 'active' ? 'true' : 'false' }}">
                        <span>In progress</span>
                        <strong data-pc-summary="active">0</strong>
                    </button>
                </div>
                <label class="pc-search">
                    <span class="sr-only">Search cases</span>
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" data-pc-search placeholder="Search case, patient or doctor">
                </label>
                <button class="pc-due-toggle" type="button" data-pc-due-toggle aria-pressed="false" aria-label="Show cases due today">
                    <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
                    <span>Due today</span>
                    <strong data-pc-summary="due">0</strong>
                </button>
            </section>

            <section class="pc-workspace">
                <section class="pc-panel pc-queue-panel" aria-labelledby="pc-queue-title">
                    <header class="pc-panel-header">
                        <div>
                            <span class="pc-eyebrow">Live queue</span>
                            <h2 id="pc-queue-title">Cases</h2>
                            <p>Select a case to see its jobs and available actions.</p>
                        </div>
                        <span class="pc-visible-count" data-pc-visible-count>0</span>
                    </header>

                    <div class="pc-case-list" data-pc-case-list>
                        @foreach($productionStages as $stage)
                            @foreach($stage['active'] as $case)
                                @include('cases.partials.production-control-case-card', [
                                    'case' => $case,
                                    'stage' => $stage,
                                    'state' => 'active',
                                    'canAssignEmployees' => $canAssignEmployees,
                                    'isAdmin' => $isAdmin,
                                ])
                            @endforeach
                            @foreach($stage['waiting'] as $case)
                                @include('cases.partials.production-control-case-card', [
                                    'case' => $case,
                                    'stage' => $stage,
                                    'state' => 'waiting',
                                    'canAssignEmployees' => $canAssignEmployees,
                                    'isAdmin' => $isAdmin,
                                ])
                            @endforeach
                        @endforeach

                        <div class="pc-empty-state" data-pc-empty hidden>No cases match these filters.</div>
                    </div>
                </section>

                <div class="pc-detail-backdrop" data-pc-detail-backdrop hidden></div>
                <aside class="pc-panel pc-detail-panel" data-pc-detail-panel aria-labelledby="pc-detail-title" aria-hidden="true" inert>
                    <div class="pc-detail-placeholder" data-pc-detail-content>
                        <p>Select a case to view its production details.</p>
                    </div>
                </aside>
            </section>
        @endif
    </main>
@endsection

@push('js')
    <script src="{{ asset('assets/js/ysh-custom-js/adminProductionControlV2.js') }}?v=20260901-4"></script>
@endpush
