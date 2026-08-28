@extends('layouts.app', ['pageSlug' => 'Dashboard'])

@push('css')
    <style>
        .solent-dash {
            position: relative;
        }
    </style>
@endpush

@section('content')
    @php
        $currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
        $dashboardSampleDataMode = (bool) ($dashboardSampleDataMode ?? config('features.dashboard.sample_data', false));

        $paymentsReceivedToday = $paymentsReceivedToday ?? [];
        $DeliveriesToday = $DeliveriesToday ?? [];
        $dashboardUi = trans('ui.dom');
        $deliveryScheduleDays = collect($deliveryScheduleDays ?? []);
        if ($deliveryScheduleDays->isEmpty()) {
            $deliveryScheduleDays = collect(range(0, 1))->map(function (int $offset) use ($DeliveriesToday) {
                return [
                    'key' => ['today', 'tomorrow'][$offset],
                    'label' => ['Today', 'Tomorrow'][$offset],
                    'date' => now()->copy()->addDays($offset)->toDateString(),
                    'cases' => $offset === 0 ? collect($DeliveriesToday) : collect(),
                ];
            });
        }
        $deliveryCardCases = $deliveryScheduleDays
            ->filter(fn (array $day) => in_array(($day['key'] ?? ''), ['today', 'tomorrow'], true))
            ->flatMap(fn (array $day) => collect($day['cases'] ?? []))
            ->sortBy(function ($case) {
                return method_exists($case, 'getRawOriginal')
                    ? ($case->getRawOriginal('initial_delivery_date') ?: $case->initial_delivery_date)
                    : $case->initial_delivery_date;
            })
            ->values();
        $compUnitsCount7Days = $compUnitsCount7Days ?? [];
        $compCasesCount7Days = $compCasesCount7Days ?? [];
        $collectionsInLast30Days = $collectionsInLast30Days ?? [];
        $compCasesCount30Days = $compCasesCount30Days ?? [];
        $compUnitsCount30Days = $compUnitsCount30Days ?? [];
        $sales30Days = $sales30Days ?? [];
        $last7DaysLabels = count($last7DaysLabels ?? []) ? $last7DaysLabels : collect(range(6, 0))->map(fn ($day) => now()->subDays($day)->format('Y-m-d'))->values()->all();
        $last30DaysLabels = count($last30DaysLabels ?? []) ? $last30DaysLabels : collect(range(29, 0))->map(fn ($day) => now()->subDays($day)->format('Y-m-d'))->values()->all();

        $paymentsCount = collect($paymentsReceivedToday)->count();
        $deliveriesTodayCount = collect($DeliveriesToday)->count();
        $completedUnits = (int) ($CompletedJobsToday ?? 0);
        $activeUnits = (int) ($ActiveJobsToday ?? 0);
        $waitingUnits = (int) ($waitingJobsToday ?? 0);
        $revenueTotal = collect($collectionsInLast30Days)->sum();
        $ordersTotal = collect($compCasesCount30Days)->sum();
        $totalUnits = $completedUnits + $activeUnits + $waitingUnits;
        $conversionRate = $totalUnits > 0 ? round(($completedUnits / $totalUnits) * 100, 2) : 0;
        $newCustomers = collect($DeliveriesToday)->pluck('client_id')->unique()->count();

        $displayRevenueTotal = $dashboardSampleDataMode ? max($revenueTotal, 428540) : $revenueTotal;
        $displayOrdersTotal = $dashboardSampleDataMode ? max($ordersTotal, 3721) : $ordersTotal;
        $displayNewCustomers = $dashboardSampleDataMode ? max($newCustomers, 2145) : $newCustomers;
        $displayConversionRate = $dashboardSampleDataMode ? max($conversionRate, 2.48) : $conversionRate;
        $serviceRevenueRows = collect();

        try {
            $serviceMonthStart = now()->startOfMonth()->toDateTimeString();
            $serviceMonthEnd = now()->endOfMonth()->toDateTimeString();

            if (\Illuminate\Support\Facades\Schema::hasColumn('cases', 'service_type')) {
                $serviceRevenueRows = \Illuminate\Support\Facades\DB::table('invoices')
                    ->join('cases', 'cases.id', '=', 'invoices.case_id')
                    ->selectRaw("COALESCE(NULLIF(cases.service_type, ''), 'Unassigned') as label, SUM(invoices.amount) as value")
                    ->whereBetween('invoices.date_applied', [$serviceMonthStart, $serviceMonthEnd])
                    ->where('invoices.status', 1)
                    ->whereNull('invoices.deleted_at')
                    ->whereNull('cases.deleted_at')
                    ->groupBy('label')
                    ->orderByDesc('value')
                    ->get();
            } else {
                $caseServiceQuery = \Illuminate\Support\Facades\DB::table('jobs')
                    ->leftJoin('job_types', 'job_types.id', '=', 'jobs.type')
                    ->selectRaw("jobs.case_id, COALESCE(MIN(NULLIF(job_types.name, '')), 'Unassigned') as service_label")
                    ->whereNull('jobs.deleted_at')
                    ->groupBy('jobs.case_id');

                $serviceRevenueRows = \Illuminate\Support\Facades\DB::table('invoices')
                    ->join('cases', 'cases.id', '=', 'invoices.case_id')
                    ->leftJoinSub($caseServiceQuery, 'case_services', 'case_services.case_id', '=', 'cases.id')
                    ->selectRaw("COALESCE(case_services.service_label, 'Unassigned') as label, SUM(invoices.amount) as value")
                    ->whereBetween('invoices.date_applied', [$serviceMonthStart, $serviceMonthEnd])
                    ->where('invoices.status', 1)
                    ->whereNull('invoices.deleted_at')
                    ->whereNull('cases.deleted_at')
                    ->groupBy('label')
                    ->orderByDesc('value')
                    ->get();
            }
        } catch (\Throwable $exception) {
            $serviceRevenueRows = collect();
        }

        if ($dashboardSampleDataMode && $serviceRevenueRows->sum('value') <= 0) {
            $serviceRevenueRows = collect([
                ['label' => 'Crowns', 'value' => 184200],
                ['label' => 'Implants', 'value' => 126450],
                ['label' => 'Bridges', 'value' => 74300],
                ['label' => 'Models', 'value' => 28640],
                ['label' => 'Repairs', 'value' => 14950],
            ]);
        }

        $serviceRevenueTotal = (float) $serviceRevenueRows->sum('value');
        $serviceRevenueRows = $serviceRevenueRows
            ->map(fn ($row) => [
                'label' => (string) (is_array($row) ? $row['label'] : $row->label),
                'value' => (float) (is_array($row) ? $row['value'] : $row->value),
            ])
            ->sortByDesc('value')
            ->take(5)
            ->values()
            ->map(fn (array $row) => $row + [
                'width' => $serviceRevenueTotal > 0 ? round(($row['value'] / $serviceRevenueTotal) * 100, 1) : 0,
            ]);

        $productionLoadRows = collect($productionLoadRows ?? ($dashboardSampleDataMode ? [
            ['label' => 'Design', 'jobs' => 42, 'active' => 31, 'waiting' => 11, 'utilization' => 82, 'jobsScaled' => 76],
            ['label' => 'Milling', 'jobs' => 35, 'active' => 26, 'waiting' => 9, 'utilization' => 74, 'jobsScaled' => 64],
            ['label' => '3D Printing', 'jobs' => 27, 'active' => 19, 'waiting' => 8, 'utilization' => 58, 'jobsScaled' => 50],
            ['label' => 'Sintering', 'jobs' => 18, 'active' => 12, 'waiting' => 6, 'utilization' => 41, 'jobsScaled' => 34],
        ] : [
            ['label' => 'Design', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => 'Milling', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => '3D Printing', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => 'Sintering', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
        ]))->values();
        $productionLoadHighlights = $productionLoadRows->sortByDesc('jobs')->values();

        $dashboardMetrics = [
            'clientCountries' => $dashboardSampleDataMode ? [
                ['name' => 'Amman', 'value' => max(842, $ordersTotal + 220), 'width' => 96],
                ['name' => 'Irbid', 'value' => max(312, $ordersTotal + 80), 'width' => 62],
                ['name' => 'Zarqa', 'value' => max(221, $ordersTotal + 44), 'width' => 48],
                ['name' => 'Aqaba', 'value' => max(198, $deliveriesTodayCount + 120), 'width' => 38],
                ['name' => 'Salt', 'value' => max(152, $paymentsCount + 95), 'width' => 30],
            ] : [],
        ];

        $revenueTrendLabels = collect($last30DaysLabels)->take(-7)->values()->all();
        $revenueTrendValues = collect($collectionsInLast30Days)->take(-7)->values();
        if ($dashboardSampleDataMode && $revenueTrendValues->sum() <= 0) {
            $revenueTrendValues = collect([360, 320, 410, 620, 440, 660, 345]);
        } elseif ($revenueTrendValues->isEmpty()) {
            $revenueTrendValues = collect(array_fill(0, count($revenueTrendLabels), 0));
        }

        $ordersTrendLabels = collect($last7DaysLabels)->values()->all();
        $ordersTrendSampleValues = collect([820, 710, 960, 880, 835, 930, 948]);
        $ordersTrendValues = collect($compCasesCount7Days)
            ->values()
            ->map(fn ($value) => (int) max(0, round((float) $value)));
        if ($dashboardSampleDataMode && $ordersTrendValues->sum() < $ordersTrendSampleValues->sum()) {
            $ordersTrendValues = $ordersTrendSampleValues;
        } elseif ($ordersTrendValues->isEmpty()) {
            $ordersTrendValues = collect(array_fill(0, count($ordersTrendLabels), 0));
        }

        $activityItems = collect();
        foreach ($paymentsReceivedToday as $payment) {
            $activityItems->push([
                'title' => 'Payment received from ' . ($payment->client->name ?? 'Doctor'),
                'meta' => number_format((float) $payment->amount, 2) . ' ' . $currencyLabel,
                'time' => optional($payment->created_at)->diffForHumans() ?? 'Today',
                'icon' => 'fa-money-bill-wave',
                'tone' => 'success',
                'modal' => '#receivePaymentModal' . $payment->id,
            ]);
        }
        foreach ($DeliveriesToday as $case) {
            $activityItems->push([
                'title' => 'Delivery scheduled for ' . ($case->client->name ?? 'Doctor'),
                'meta' => $case->patient_name ?? 'Patient',
                'time' => date('g:i a', strtotime(str_replace('T', ' ', $case->initial_delivery_date))),
                'icon' => 'fa-truck-fast',
                'tone' => 'warning',
                'modal' => '#updateDeliveryDate' . $case->id,
            ]);
        }
        if ($dashboardSampleDataMode && $activityItems->isEmpty()) {
            $activityItems = collect([
                ['title' => 'New dental case #CASE-1256', 'meta' => 'Implant crown | Dr. Lina Haddad', 'time' => '2m ago', 'icon' => 'fa-clipboard-list', 'tone' => 'accent', 'modal' => null],
                ['title' => 'Clinic payment received', 'meta' => '850.00 ' . $currencyLabel . ' | Abdoun Dental Center', 'time' => '15m ago', 'icon' => 'fa-money-bill-wave', 'tone' => 'success', 'modal' => null],
                ['title' => 'Case ready for delivery', 'meta' => 'Zirconia bridge | Sweifieh Clinic', 'time' => '1h ago', 'icon' => 'fa-truck-fast', 'tone' => 'warning', 'modal' => null],
            ]);
        }

        $kpiCards = [
            ['label' => 'Total Revenue', 'prefix' => $currencyLabel, 'number' => number_format($displayRevenueTotal, 0)],
            ['label' => 'Orders', 'prefix' => null, 'number' => number_format($displayOrdersTotal)],
            ['label' => 'Customers', 'prefix' => null, 'number' => number_format($displayNewCustomers)],
        ];

    @endphp

    <link href="{{ asset('assets/css/elegant-dashboard.css') }}" rel="stylesheet">

    <div class="solent-dash" data-dashboard-sample-mode="{{ $dashboardSampleDataMode ? 'on' : 'off' }}">
        <main class="solent-dash-shell">
            <section class="solent-dash-kpis" aria-label="Dashboard key metrics">
                @foreach ($kpiCards as $card)
                    <article class="solent-dash-kpi-card card-1">
                        <div class="solent-dash-kpi-head">
                            <div>
                                <span class="solent-dash-label">{{ $card['label'] }}</span>
                                <strong class="solent-dash-value" aria-label="{{ trim(($card['prefix'] ? $card['prefix'] . ' ' : '') . $card['number']) }}">
                                    @if ($card['prefix'])
                                        <span class="solent-dash-value-prefix">{{ $card['prefix'] }}</span>
                                    @endif
                                    <span class="solent-dash-value-number">{{ $card['number'] }}</span>
                                </strong>
                            </div>
                        </div>
                    </article>
                @endforeach
                <article class="solent-dash-kpi-card solent-dash-sample-card card-1">
                    <form class="solent-dash-sample-toggle" method="GET" action="{{ route('home') }}">
                        <div>
                            <span class="solent-dash-label">Demo Data</span>
                            <strong class="solent-dash-sample-status">
                                {{ $dashboardSampleDataMode ? 'On' : 'Off' }}
                            </strong>
                        </div>
                        <button
                            type="submit"
                            class="solent-dash-switch {{ $dashboardSampleDataMode ? 'is-active' : '' }}"
                            name="sample_data"
                            value="{{ $dashboardSampleDataMode ? '0' : '1' }}"
                            role="switch"
                            aria-checked="{{ $dashboardSampleDataMode ? 'true' : 'false' }}"
                            title="{{ $dashboardSampleDataMode ? 'Show real dashboard data' : 'Show demo dashboard data' }}"
                        >
                            <span class="sr-only">Show demo dashboard data</span>
                            <span class="solent-dash-switch__track" aria-hidden="true"></span>
                        </button>
                    </form>
                </article>
            </section>

            <section class="solent-dash-layout">
                <article class="solent-dash-panel solent-dash-customer-panel card-2">
                    <div class="solent-dash-panel-head">
                        <div class="solent-dash-map-card-copy">
                            <span class="solent-dash-map-card-kicker">{{ $dashboardUi['Geographic activity'] ?? 'Geographic activity' }}</span>
                            <h2 class="solent-dash-panel-title">{{ $dashboardUi['Case Activity by Governorate'] ?? 'Case Activity by Governorate' }}</h2>
                            <p class="solent-dash-map-card-summary">Explore how active cases are distributed across Jordan.</p>
                        </div>
                        <button class="solent-dash-map-card-action" type="button" data-toggle="modal" data-target="#solentJordanMapModal" aria-label="Explore case activity by Jordan governorate">
                            <i class="fa-solid fa-expand" aria-hidden="true"></i>
                            <span>Explore</span>
                        </button>
                    </div>
                    <div class="solent-dash-client-panel">
                        <div class="solent-dash-map-stage" aria-hidden="true" inert>
                            @include('partials.jordan-dashboard-map')
                        </div>
                    </div>
                </article>

                <article class="solent-dash-panel solent-dash-panel-large card-2">
                    <div class="solent-dash-panel-head">
                        <div>
                            <h2 class="solent-dash-panel-title">Revenue Overview</h2>
                            <p class="solent-dash-panel-value">{{ $currencyLabel }} {{ number_format($displayRevenueTotal, 0) }}</p>
                        </div>
                    </div>
                    <div class="solent-dash-chart-tall"><canvas id="solentDashRevenueOverview"></canvas></div>
                </article>

                <article class="solent-dash-panel solent-dash-delivery-panel card-2" data-dashboard-delivery-card>
                    <div class="solent-dash-panel-head">
                        <h2 class="solent-dash-panel-title">{{ $dashboardUi['Case delivery'] ?? 'Case delivery' }}</h2>
                        <a class="solent-dash-delivery-link" href="{{ route('delivery-schedule') }}" aria-label="{{ $dashboardUi['View delivery schedule'] ?? 'View delivery schedule' }}">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                            <span>{{ $dashboardUi['View delivery schedule'] ?? 'View delivery schedule' }}</span>
                        </a>
                    </div>
                    <div class="solent-dash-delivery-case-list" data-dashboard-delivery-cases>
                        @forelse ($deliveryCardCases as $deliveryCase)
                            @php
                                $rawDeliveryDate = method_exists($deliveryCase, 'getRawOriginal')
                                    ? ($deliveryCase->getRawOriginal('initial_delivery_date') ?: $deliveryCase->initial_delivery_date)
                                    : $deliveryCase->initial_delivery_date;
                                $deliveryMoment = \Carbon\Carbon::parse(str_replace('T', ' ', (string) $rawDeliveryDate));
                                $deliveryDoctor = $deliveryCase->client->name ?? ($dashboardUi['Doctor'] ?? 'Doctor');
                                $deliveryPatient = $deliveryCase->patient_name ?? ($dashboardUi['Patient'] ?? 'Patient');
                            @endphp
                            <a class="solent-dash-delivery-case" href="{{ route('view-case', ['id' => $deliveryCase->id, 'stage' => -2]) }}">
                                <span class="solent-dash-delivery-case-copy">
                                    <span class="solent-dash-delivery-case-field">
                                        <small>{{ $dashboardUi['Doctor'] ?? 'Doctor' }}</small>
                                        <strong title="{{ $deliveryDoctor }}">{{ $deliveryDoctor }}</strong>
                                    </span>
                                    <span class="solent-dash-delivery-case-field">
                                        <small>{{ $dashboardUi['Patient'] ?? 'Patient' }}</small>
                                        <strong title="{{ $deliveryPatient }}">{{ $deliveryPatient }}</strong>
                                    </span>
                                </span>
                                <time datetime="{{ $deliveryMoment->toIso8601String() }}" dir="ltr">
                                    <small>{{ $dashboardUi['Delivery time'] ?? 'Delivery time' }}</small>
                                    <strong>{{ $deliveryMoment->translatedFormat('d M') }} · {{ $deliveryMoment->format('H:i') }}</strong>
                                </time>
                            </a>
                        @empty
                            <div class="solent-dash-delivery-empty">
                                <i class="fa-regular fa-circle-check" aria-hidden="true"></i>
                                <span>{{ $dashboardUi['No cases scheduled'] ?? 'No cases scheduled' }}</span>
                            </div>
                        @endforelse
                    </div>
                </article>

                <article class="solent-dash-panel solent-dash-service-panel card-2">
                    <div class="solent-dash-panel-head">
                        <h2 class="solent-dash-panel-title">Revenue by Service Type</h2>
                    </div>
                    <div class="solent-dash-service-total">
                        <span>This month</span>
                        <strong>{{ $currencyLabel }} {{ number_format($serviceRevenueTotal, 0) }}</strong>
                    </div>
                    <div class="solent-dash-service-bars">
                        @forelse ($serviceRevenueRows as $serviceRevenueRow)
                            <div class="solent-dash-service-row">
                                <div class="solent-dash-service-line">
                                    <span>{{ $serviceRevenueRow['label'] }}</span>
                                    <strong>{{ $currencyLabel }} {{ number_format($serviceRevenueRow['value'], 0) }}</strong>
                                </div>
                                <div class="solent-dash-service-track">
                                    <span style="width: {{ $serviceRevenueRow['width'] }}%"></span>
                                </div>
                            </div>
                        @empty
                            <div class="solent-dash-service-empty">No service revenue this month</div>
                        @endforelse
                    </div>
                </article>

                <article class="solent-dash-panel solent-dash-orders-panel card-2">
                    <div class="solent-dash-panel-head">
                        <div>
                            <h2 class="solent-dash-panel-title">Orders Over Time</h2>
                            <p class="solent-dash-panel-value">{{ number_format($displayOrdersTotal) }}</p>
                        </div>
                    </div>
                    <div class="solent-dash-chart-medium"><canvas id="solentDashOrdersOverTime"></canvas></div>
                </article>
            </section>

            <section class="solent-dash-mid-layout solent-dash-hidden-section" hidden>
                <article class="solent-dash-panel solent-dash-load-panel solent-dash-hidden-card card-2" hidden>
                    <div class="solent-dash-panel-head">
                        <div>
                            <span class="solent-dash-load-kicker">Production Load</span>
                            <h2 class="solent-dash-panel-title">Stage Daily Utilization</h2>
                        </div>
                        <button class="solent-dash-chip" type="button">Current cases</button>
                    </div>
                    <div class="solent-dash-load-body">
                        <div>
                            <div class="solent-dash-load-chart"><canvas id="solentDashProductionLoad"></canvas></div>
                            <div class="solent-dash-load-legend" aria-hidden="true">
                                <span><i class="solent-dash-load-swatch solent-dash-load-swatch-primary"></i>Active cases</span>
                                <span><i class="solent-dash-load-swatch solent-dash-load-swatch-secondary"></i>Waiting cases</span>
                            </div>
                        </div>
                        <div class="solent-dash-load-list">
                            @foreach ($productionLoadHighlights as $stageLoad)
                                <div class="solent-dash-load-item card-3">
                                    <div>
                                        <strong>{{ $stageLoad['label'] }}</strong>
                                        <span>{{ number_format($stageLoad['active']) }} active cases</span>
                                        <span>{{ number_format($stageLoad['waiting']) }} waiting cases</span>
                                    </div>
                                    <span class="solent-dash-load-jobs">{{ number_format($stageLoad['jobs']) }} total cases</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>

                <article class="solent-dash-panel solent-dash-activity-panel solent-dash-hidden-card card-2" hidden>
                    <div class="solent-dash-panel-head">
                        <h2 class="solent-dash-panel-title">Recent Activity</h2>
                        <button class="solent-dash-chip" type="button">View all</button>
                    </div>
                    <div class="solent-dash-activity-list">
                        @forelse ($activityItems->take(5) as $activity)
                            <a class="solent-dash-activity-item card-3" href="{{ $activity['modal'] ?: '#' }}" @if($activity['modal']) data-toggle="modal" @endif>
                                <span class="solent-dash-activity-icon tone-{{ $activity['tone'] }}"><i class="fa-solid {{ $activity['icon'] }}"></i></span>
                                <span>
                                    <strong>{{ $activity['title'] }}</strong>
                                    <span>{{ $activity['meta'] }}</span>
                                </span>
                                <span class="solent-dash-activity-meta">{{ $activity['time'] }}</span>
                            </a>
                        @empty
                            <div class="solent-dash-activity-empty">No recent activity</div>
                        @endforelse
                    </div>
                </article>
            </section>
        </main>
    </div>

    <div class="modal fade solent-dash-map-modal" id="solentJordanMapModal" tabindex="-1" role="dialog" aria-labelledby="solentJordanMapModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="solent-dash-map-modal-copy">
                        <span class="solent-dash-map-modal-eyebrow">Regional overview</span>
                        <h5 class="modal-title" id="solentJordanMapModalTitle">Case Activity Across Jordan</h5>
                        <p class="solent-dash-map-modal-summary">Select a governorate to inspect its case activity in more detail.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="solent-dash-map-stage">
                        @include('partials.jordan-dashboard-map', ['expanded' => true, 'drilldown' => true])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade solent-dash-map-detail-modal" id="solentJordanGovernorateModal" tabindex="-1" role="dialog" aria-labelledby="solentJordanGovernorateModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="solent-dash-map-modal-copy">
                        <span class="solent-dash-map-modal-eyebrow">Governorate detail</span>
                        <h5 class="modal-title" id="solentJordanGovernorateModalTitle">Governorate Detail</h5>
                        <p class="solent-dash-map-detail-meta" id="solentJordanGovernorateModalMeta">0 cases in this governorate</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="solent-dash-map-stage">
                        @include('partials.jordan-dashboard-map', ['expanded' => true, 'focusRegion' => ''])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($paymentsReceivedToday as $payment)
        <div class="modal fade" id="receivePaymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="exampleModalLabelform">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Receive Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body lightGrayTopBorder">
                        <div class="container"><div class="row"><div class="col-md-6"><strong>Doctor:</strong></div><div class="col-md-6">{{ $payment->client->name }}</div></div></div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container"><div class="row"><div class="col-md-6"><strong>Collected from doctor by: </strong></div><div class="col-md-6">{{ $payment->collectorFullName() }}</div></div></div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container"><div class="row"><div class="col-md-6"><strong>Payment Amount:</strong></div><div class="col-md-6">{{ $payment->amount }} {{ $currencyLabel }}</div></div></div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container"><div class="row"><div class="col-md-6"><strong>Collected On:</strong></div><div class="col-md-6">{{ $payment->created_at }}</div></div></div>
                        @if ($payment->isCollected())
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Received On:</strong></div><div class="col-md-6">{{ $payment->recieved_on }}</div></div></div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Received by:</strong></div><div class="col-md-6">{{ $payment->receiverFullName() }}</div></div></div>
                        @endif
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container"><div class="row"><div class="col-md-6"><strong>Payment Method: </strong></div><div class="col-md-6">{{ $payment->notes }}</div></div></div>
                        @if ($payment->additional_notes)
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-12">{{ $payment->additional_notes }}</div></div></div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @if (! $payment->isCollected())
                            <a href="{{ route('receive-payment', $payment->id) }}"><button type="button" class="btn btn-danger">Receive</button></a>
                        @endif
                    </div>
                    <small style="text-align:center;font-size: 60%;color: var(--text-2);">PAYMENT ID : {{ $payment->id }}</small>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($DeliveriesToday as $case)
        <div class="modal fade" id="updateDeliveryDate{{ $case->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelform" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('edit-delivery-date') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $case->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Delivery Time</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            @php $time = date('Y-m-d g:i a', strtotime($case->initial_delivery_date)); @endphp
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Doctor Name </strong></div><div class="col-md-6">{{ $case->client->name ?? 'Unknown Doctor' }}</div></div></div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Patient Name:</strong></div><div class="col-md-6">{{ $case->patient_name }}</div></div></div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Current Delivery Time:</strong></div><div class="col-md-6">{{ $time }}</div></div></div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container"><div class="row"><div class="col-md-6"><strong>Change To:</strong></div><div class="col-md-6"><input class="form-control SDTP" name="delivery_date" type="text" value="{{ $time }}" required readonly /></div></div></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">UPDATE</button>
                        </div>
                        <small style="text-align:center;font-size: 60%;color: var(--text-2);">CASE ID : {{ $case->id }}</small>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('js')
    <script src="{{ asset('white') }}/js/plugins/chartjs.min.js"></script>
    <script>
        const solentDashStyles = getComputedStyle(document.documentElement);
        const solentDashCss = function (name) {
            return solentDashStyles.getPropertyValue(name).trim();
        };
        const solentDashPalette = {
            accent: solentDashCss('--accent'),
            accentLt: solentDashCss('--accent-lt'),
            accentBg: solentDashCss('--accent-bg'),
            success: solentDashCss('--success'),
            warning: solentDashCss('--warning'),
            danger: solentDashCss('--danger'),
            text1: solentDashCss('--text-1'),
            text2: solentDashCss('--text-2'),
            surface: solentDashCss('--surface'),
            grid: solentDashCss('--chart-grid')
        };
        solentDashPalette.stageAccent = '#6366f1';

        const solentDashData = {
            revenueLabels: {!! json_encode($revenueTrendLabels) !!},
            revenueValues: {!! json_encode($revenueTrendValues->values()->all()) !!},
            orderLabels: {!! json_encode($ordersTrendLabels) !!},
            orderValues: {!! json_encode($ordersTrendValues->values()->all()) !!},
            productionLoad: {
                labels: {!! json_encode($productionLoadRows->pluck('label')->values()->all()) !!},
                active: {!! json_encode($productionLoadRows->pluck('active')->values()->all()) !!},
                waiting: {!! json_encode($productionLoadRows->pluck('waiting')->values()->all()) !!},
                jobs: {!! json_encode($productionLoadRows->pluck('jobs')->values()->all()) !!}
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            solentDashInitJordanMapDrilldown();
            solentDashInitRevenue();
            solentDashInitOrders();
        });

        function solentDashInitJordanMapDrilldown() {
            const sourceModal = document.getElementById('solentJordanMapModal');
            const targetModal = document.getElementById('solentJordanGovernorateModal');
            const targetTitle = document.getElementById('solentJordanGovernorateModalTitle');
            const targetMeta = document.getElementById('solentJordanGovernorateModalMeta');
            const targetMap = targetModal ? targetModal.querySelector('[data-solent-jordan-map]') : null;

            if (!sourceModal || !targetModal || !targetMap) return;

            function formatCaseCount(value) {
                const caseCount = Number(value) || 0;
                return caseCount.toLocaleString() + (caseCount === 1 ? ' case' : ' cases') + ' in this governorate';
            }

            document.addEventListener('solent:jordan-region-selected', function (event) {
                if (!sourceModal.contains(event.target)) return;

                const detail = event.detail || {};
                targetMap.dataset.focusRegion = detail.normalizedName || '';
                targetTitle.textContent = (detail.name || 'Governorate') + ' Governorate';
                targetMeta.textContent = formatCaseCount(detail.cases);

                if (typeof window.solentJordanMapRerender === 'function') {
                    window.solentJordanMapRerender(targetMap, false);
                }

                if (window.jQuery) {
                    window.jQuery(targetModal).modal({ backdrop: false, keyboard: true, show: true });
                }
            });

            if (window.jQuery) {
                const $sourceModal = window.jQuery(sourceModal);
                const $targetModal = window.jQuery(targetModal);

                $targetModal
                    .on('show.bs.modal', function () {
                        sourceModal.setAttribute('aria-hidden', 'true');
                    })
                    .on('hidden.bs.modal', function () {
                        if ($sourceModal.hasClass('show')) {
                            document.body.classList.add('modal-open');
                            sourceModal.setAttribute('aria-hidden', 'false');
                            const sourceCloseButton = sourceModal.querySelector('.modal-header .close');
                            if (sourceCloseButton) sourceCloseButton.focus();
                        }
                    });
            }
        }

        function solentDashGradient(ctx, color, height, topOpacity) {
            const gradient = ctx.createLinearGradient(0, 0, 0, height);
            gradient.addColorStop(0, Chart.helpers.color(color).alpha(topOpacity || 0.35).rgbString());
            gradient.addColorStop(1, Chart.helpers.color(color).alpha(0).rgbString());
            return gradient;
        }

        function solentDashDateLabel(value) {
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }

        function solentDashBaseOptions(extra) {
            return Object.assign({
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: solentDashPalette.text1,
                    titleFontColor: solentDashPalette.surface,
                    bodyFontColor: solentDashPalette.surface,
                    displayColors: false
                }
            }, extra || {});
        }

        if (!window.solentDashOrderValueLabelsRegistered) {
            Chart.plugins.register({
                afterDatasetsDraw: function (chart) {
                    if (chart.canvas.id !== 'solentDashOrdersOverTime') return;

                    const bars = chart.getDatasetMeta(0).data;
                    const values = chart.data.datasets[0].data;
                    const context = chart.ctx;

                    context.save();
                    context.fillStyle = '#312e81';
                    context.font = '600 11px Inter, Arial, sans-serif';
                    context.textAlign = 'center';
                    context.textBaseline = 'bottom';

                    bars.forEach(function (bar, index) {
                        const value = Math.max(0, Math.round(Number(values[index]) || 0));
                        const position = bar.tooltipPosition();
                        context.fillText(value.toLocaleString(), position.x, position.y - 7);
                    });

                    context.restore();
                }
            });
            window.solentDashOrderValueLabelsRegistered = true;
        }

        function solentDashInitRevenue() {
            const canvas = document.getElementById('solentDashRevenueOverview');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const revenueAccent = '#6366f1';
            const revenueGradient = ctx.createLinearGradient(0, 0, 0, 240);
            revenueGradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            revenueGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: solentDashData.revenueLabels,
                    datasets: [{
                        data: solentDashData.revenueValues,
                        borderColor: revenueAccent,
                        backgroundColor: revenueGradient,
                        borderWidth: 2.5,
                        pointBackgroundColor: revenueAccent,
                        pointBorderColor: solentDashPalette.surface,
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        lineTension: 0.38
                    }]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{ gridLines: { display: false }, ticks: { callback: solentDashDateLabel, fontColor: solentDashPalette.text2, fontSize: 11 } }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) {
                                    const roundedValue = Number(Number(value).toFixed(1));
                                    return roundedValue === 0 ? '0' : roundedValue + 'K';
                                },
                                fontColor: solentDashPalette.text2,
                                fontSize: 11
                            },
                            gridLines: { color: solentDashPalette.grid, drawBorder: false }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return 'JOD ' + Number(tooltipItem.yLabel || 0).toLocaleString();
                            }
                        }
                    }
                })
            });
        }

        function solentDashInitOrders() {
            const canvas = document.getElementById('solentDashOrdersOverTime');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const orderValues = solentDashData.orderValues.map(function (value) {
                return Math.max(0, Math.round(Number(value) || 0));
            });
            const orderAxisMax = Math.max.apply(null, orderValues.concat([0]));
            const orderTickStep = orderAxisMax >= 500 ? 200 : (orderAxisMax >= 100 ? 50 : (orderAxisMax >= 20 ? 10 : 1));
            const orderGradient = ctx.createLinearGradient(0, 0, 0, 280);
            orderGradient.addColorStop(0, 'rgba(99, 102, 241, 0.92)');
            orderGradient.addColorStop(0.62, 'rgba(129, 140, 248, 0.56)');
            orderGradient.addColorStop(1, 'rgba(199, 210, 254, 0.22)');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: solentDashData.orderLabels,
                    datasets: [
                        {
                            label: 'Orders',
                            data: orderValues,
                            backgroundColor: orderGradient,
                            hoverBackgroundColor: '#4f46e5',
                            borderColor: 'rgba(99, 102, 241, 0.7)',
                            borderWidth: 1,
                            barPercentage: 0.56,
                            categoryPercentage: 0.72,
                            maxBarThickness: 30
                        },
                        {
                            type: 'line',
                            label: 'Order trend',
                            data: orderValues,
                            borderColor: '#312e81',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 2.5,
                            pointHoverRadius: 4,
                            fill: false,
                            lineTension: 0.34,
                            orderTrend: true
                        }
                    ]
                },
                options: solentDashBaseOptions({
                    layout: { padding: { top: 18 } },
                    hover: { mode: 'index', intersect: false },
                    scales: {
                        xAxes: [{ gridLines: { display: false }, ticks: { callback: solentDashDateLabel, fontColor: solentDashPalette.text2, fontSize: 11 } }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                stepSize: orderTickStep,
                                suggestedMax: orderAxisMax >= 500 ? Math.ceil(orderAxisMax / 200) * 200 : undefined,
                                fontColor: solentDashPalette.text2,
                                fontSize: 11,
                                callback: function (value) {
                                    return Number.isInteger(value) ? value.toLocaleString() : '';
                                }
                            },
                            gridLines: { color: solentDashPalette.grid, drawBorder: false }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        filter: function (tooltipItem, chartData) {
                            return !chartData.datasets[tooltipItem.datasetIndex].orderTrend;
                        },
                        callbacks: {
                            label: function (tooltipItem) {
                                return Number(tooltipItem.yLabel || 0).toLocaleString() + ' orders';
                            }
                        }
                    }
                })
            });
        }

        function solentDashInitProductionLoad() {
            const canvas = document.getElementById('solentDashProductionLoad');
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: solentDashData.productionLoad.labels,
                    datasets: [
                        {
                            label: @json(trans('ui.dom')['Active cases'] ?? 'Active cases'),
                            data: solentDashData.productionLoad.active,
                            backgroundColor: solentDashPalette.stageAccent,
                            hoverBackgroundColor: solentDashPalette.stageAccent,
                            borderColor: solentDashPalette.stageAccent,
                            borderWidth: 0,
                            barThickness: 14,
                            maxBarThickness: 18
                        },
                        {
                            label: @json(trans('ui.dom')['Waiting cases'] ?? 'Waiting cases'),
                            data: solentDashData.productionLoad.waiting,
                            backgroundColor: solentDashPalette.accentBg,
                            borderWidth: 0,
                            barThickness: 14,
                            maxBarThickness: 18
                        }
                    ]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{
                            ticks: { fontColor: solentDashPalette.text1, fontSize: 10, fontStyle: '600' },
                            gridLines: { display: false }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                fontColor: solentDashPalette.text2,
                                fontSize: 10,
                                callback: function (value) {
                                    return Number.isInteger(value) ? value.toLocaleString() : '';
                                }
                            },
                            gridLines: { color: solentDashPalette.grid, drawBorder: false }
                        }]
                    },
                    tooltips: {
                        backgroundColor: solentDashPalette.text1,
                        titleFontColor: solentDashPalette.surface,
                        bodyFontColor: solentDashPalette.surface,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const cases = dataset.data[tooltipItem.index] || 0;
                                return dataset.label + ': ' + Number(cases).toLocaleString() + ' cases';
                            }
                        }
                    }
                })
            });
        }
    </script>
@endpush
