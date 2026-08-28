@extends('layouts.app', [
    'pageSlug' => 'Production Control',
    'class' => ' production-control-v2-page',
])

@php
    $permissions = $permissions ?? Cache::get('user' . Auth::id());
    $isAdmin = (bool) Auth::user()->is_admin;
    $canAssignEmployees = $isAdmin || ($permissions && $permissions->contains('permission_id', 129));

    $productionStages = collect([
        ['key' => 'design', 'label' => 'Design', 'number' => 1, 'permission' => 1, 'active' => $aDesign ?? collect(), 'waiting' => $wDesign ?? collect(), 'employees' => $designers ?? collect()],
        ['key' => 'milling', 'label' => 'Milling', 'number' => 2, 'permission' => 2, 'active' => $aMilling ?? collect(), 'waiting' => $wMilling ?? collect(), 'employees' => $millers ?? collect()],
        ['key' => '3d-printing', 'label' => '3D Printing', 'number' => 3, 'permission' => 3, 'active' => $aPrinting ?? collect(), 'waiting' => $wPrinting ?? collect(), 'employees' => $printers ?? collect()],
        ['key' => 'sintering', 'label' => 'Sintering', 'number' => 4, 'permission' => 4, 'active' => $aSintering ?? collect(), 'waiting' => $wSintering ?? collect(), 'employees' => $sinteringUsers ?? collect()],
        ['key' => 'pressing', 'label' => 'Pressing', 'number' => 5, 'permission' => 5, 'active' => $aPressing ?? collect(), 'waiting' => $wPressing ?? collect(), 'employees' => $pressingUsers ?? collect()],
        ['key' => 'metal-work', 'label' => 'Metal Work', 'number' => 9, 'permission' => 5, 'active' => $aMetalWork ?? collect(), 'waiting' => $wMetalWork ?? collect(), 'employees' => $pressingUsers ?? collect()],
        ['key' => 'finishing', 'label' => 'Finishing & Build up', 'number' => 6, 'permission' => 6, 'active' => $aFinishing ?? collect(), 'waiting' => $wFinishing ?? collect(), 'employees' => $finishingUsers ?? collect()],
        ['key' => 'quality-control', 'label' => 'Quality Control', 'number' => 7, 'permission' => 7, 'active' => $aQC ?? collect(), 'waiting' => $wQC ?? collect(), 'employees' => $qcUsers ?? collect()],
        ['key' => 'delivery', 'label' => 'Delivery', 'number' => 8, 'permission' => 8, 'active' => $aDelivery ?? collect(), 'waiting' => $wDelivery ?? collect(), 'employees' => $drivers ?? collect()],
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
    $allActive = $productionStages->sum(fn (array $stage): int => $stage['active']->count());
    $allWaiting = $productionStages->sum(fn (array $stage): int => $stage['waiting']->count());
@endphp

@push('css')
    <link href="{{ asset('assets/css/admin-production-control-v2.css') }}?v=20260824-1" rel="stylesheet">
@endpush

@section('content')
    <main class="pc-board"
          data-pc-root
          data-pc-selected-stage="{{ $initialStage }}"
          data-pc-selected-case="{{ $initialCaseId ?: '' }}"
          data-pc-processing-message="Processing your request...">
        <header class="pc-page-header">
            <div>
                <h1>Production Control</h1>
                <p>Monitor every production stage, inspect cases and manage assignments.</p>
            </div>
            <div class="pc-header-actions">
                <a class="pc-classic-link" href="{{ route('admin-dashboard-v2', ['view' => 'classic']) }}">Classic tools</a>
                <span class="pc-admin-label">Admin workspace</span>
            </div>
        </header>

        @if($productionStages->isEmpty())
            <section class="pc-empty-page">No production stages are available for this account.</section>
        @else
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
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                        <strong>{{ $stage['label'] }}</strong>
                        <span><b>{{ $stage['waiting']->count() }}</b> ready <b>{{ $stage['active']->count() }}</b> active</span>
                    </button>
                @endforeach
            </nav>

            <section class="pc-summary-grid" aria-label="Selected stage summary">
                <article class="pc-summary-card pc-summary-card--ready"><span>Ready</span><strong data-pc-summary="ready">0</strong></article>
                <article class="pc-summary-card pc-summary-card--active"><span>In progress</span><strong data-pc-summary="active">0</strong></article>
                <article class="pc-summary-card pc-summary-card--today"><span>Due today</span><strong data-pc-summary="due">0</strong></article>
                <article class="pc-summary-card"><span>Total cases</span><strong data-pc-summary="total">0</strong></article>
            </section>

            <section class="pc-toolbar" aria-label="Case filters">
                <label class="pc-search">
                    <span class="sr-only">Search cases</span>
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="search" data-pc-search placeholder="Search case, patient or doctor">
                </label>
                <div class="pc-filter-group">
                    <select data-pc-status-filter aria-label="Work status">
                        <option value="all">All work</option>
                        <option value="waiting">Ready</option>
                        <option value="active">In progress</option>
                    </select>
                    <select data-pc-assignment-filter aria-label="Assignment status">
                        <option value="all">All assignments</option>
                        <option value="unassigned">Unassigned</option>
                        <option value="assigned">Assigned</option>
                    </select>
                    <select data-pc-due-filter aria-label="Delivery date">
                        <option value="all">Any delivery date</option>
                        <option value="today">Due today</option>
                    </select>
                </div>
            </section>

            <section class="pc-workspace">
                <section class="pc-panel pc-queue-panel" aria-labelledby="pc-queue-title">
                    <header class="pc-panel-header">
                        <div>
                            <h2 id="pc-queue-title"><span data-pc-stage-title></span> cases</h2>
                            <p>Select a case to inspect and manage it.</p>
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
                <aside class="pc-panel pc-detail-panel" data-pc-detail-panel aria-labelledby="pc-detail-title">
                    <div class="pc-detail-placeholder" data-pc-detail-content>
                        <p>Select a case to view its production details.</p>
                    </div>
                </aside>
            </section>
        @endif
    </main>
@endsection

@push('js')
    <script src="{{ asset('assets/js/ysh-custom-js/adminProductionControlV2.js') }}?v=20260824-1"></script>
@endpush
