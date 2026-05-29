@extends('layouts.app', ['pageSlug' => 'Dashboard'])

@section('content')
    @php
        $currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
        $dashboardSampleDataMode = (bool) ($dashboardSampleDataMode ?? config('features.dashboard.sample_data', true));

        $paymentsReceivedToday = $paymentsReceivedToday ?? [];
        $DeliveriesToday = $DeliveriesToday ?? [];
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
        $completedUnits7dTotal = array_sum($compUnitsCount7Days);
        $revenueTotal = collect($collectionsInLast30Days)->sum();
        $ordersTotal = collect($compCasesCount30Days)->sum();
        $totalUnits = $completedUnits + $activeUnits + $waitingUnits;
        $conversionRate = $totalUnits > 0 ? round(($completedUnits / $totalUnits) * 100, 2) : 0;
        $newCustomers = collect($DeliveriesToday)->pluck('client_id')->unique()->count();

        $displayRevenueTotal = $dashboardSampleDataMode ? max($revenueTotal, 428540) : $revenueTotal;
        $displayOrdersTotal = $dashboardSampleDataMode ? max($ordersTotal, 3721) : $ordersTotal;
        $displayNewCustomers = $dashboardSampleDataMode ? max($newCustomers, 2145) : $newCustomers;
        $displayConversionRate = $dashboardSampleDataMode ? max($conversionRate, 2.48) : $conversionRate;
        $displayWorkloadTotal = $dashboardSampleDataMode ? max($completedUnits7dTotal + $activeUnits + $waitingUnits, 128) : ($completedUnits7dTotal + $activeUnits + $waitingUnits);

        $series = function ($values, int $length = 12): array {
            $result = collect($values)->take(-$length)->values();
            return $result->isEmpty() ? array_fill(0, $length, 0) : $result->all();
        };

        $productionLoadRows = collect($productionLoadRows ?? ($dashboardSampleDataMode ? [
            ['label' => 'Design', 'color' => '#4f6ef7', 'jobs' => 42, 'active' => 31, 'waiting' => 11, 'utilization' => 82, 'jobsScaled' => 76],
            ['label' => 'Milling', 'color' => '#30c7b5', 'jobs' => 35, 'active' => 26, 'waiting' => 9, 'utilization' => 74, 'jobsScaled' => 64],
            ['label' => '3D Printing', 'color' => '#fb8c1f', 'jobs' => 27, 'active' => 19, 'waiting' => 8, 'utilization' => 58, 'jobsScaled' => 50],
            ['label' => 'Sintering', 'color' => '#ee5ea3', 'jobs' => 18, 'active' => 12, 'waiting' => 6, 'utilization' => 41, 'jobsScaled' => 34],
        ] : [
            ['label' => 'Design', 'color' => '#4f6ef7', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => 'Milling', 'color' => '#30c7b5', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => '3D Printing', 'color' => '#fb8c1f', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
            ['label' => 'Sintering', 'color' => '#ee5ea3', 'jobs' => 0, 'active' => 0, 'waiting' => 0, 'utilization' => 0, 'jobsScaled' => 0],
        ]))->values();
        $productionLoadHighlights = $productionLoadRows->sortByDesc('jobs')->take(3)->values();

        $dashboardMetrics = [
            'kpiSparks' => [
                'revenue' => $dashboardSampleDataMode ? [18, 15, 21, 19, 27, 23, 31, 29, 35, 33, 39, 36] : $series($collectionsInLast30Days),
                'orders' => $dashboardSampleDataMode ? [9, 12, 10, 15, 17, 13, 18, 20, 19, 23, 21, 25] : $series($compCasesCount30Days),
                'clients' => $dashboardSampleDataMode ? [7, 9, 13, 11, 15, 16, 14, 20, 22, 18, 24, 21] : array_fill(0, 12, $newCustomers),
                'conversion' => $dashboardSampleDataMode ? [20, 18, 22, 25, 24, 27, 23, 21, 19, 20, 22, 23] : array_fill(0, 12, $conversionRate),
            ],
            'kpiBars' => [
                'workload' => $dashboardSampleDataMode ? [3, 4, 2, 5, 4, 6, 5, 7, 6, 8, 7, 9] : [$completedUnits7dTotal, $activeUnits, $waitingUnits, $deliveriesTodayCount, $paymentsCount],
            ],
            'workloadMix' => [
                ['label' => 'Completed units', 'value' => $dashboardSampleDataMode ? max(1, $completedUnits7dTotal) : $completedUnits7dTotal, 'color' => '#6d5dfc'],
                ['label' => 'Active units', 'value' => $dashboardSampleDataMode ? max(1, $activeUnits) : $activeUnits, 'color' => '#20b997'],
                ['label' => 'Waiting units', 'value' => $dashboardSampleDataMode ? max(1, $waitingUnits) : $waitingUnits, 'color' => '#2f8fed'],
                ['label' => 'Deliveries', 'value' => $dashboardSampleDataMode ? max(1, $deliveriesTodayCount) : $deliveriesTodayCount, 'color' => '#fb8c1f'],
                ['label' => 'Payments', 'value' => $dashboardSampleDataMode ? max(1, $paymentsCount) : $paymentsCount, 'color' => '#ec5fa5'],
            ],
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
        $ordersTrendValues = collect($compCasesCount7Days)->values();
        if ($dashboardSampleDataMode && $ordersTrendValues->sum() <= 0) {
            $ordersTrendValues = collect([820, 710, 960, 880, 835, 930, 948]);
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
                'color' => '#20b997',
                'modal' => '#receivePaymentModal' . $payment->id,
            ]);
        }
        foreach ($DeliveriesToday as $case) {
            $activityItems->push([
                'title' => 'Delivery scheduled for ' . ($case->client->name ?? 'Doctor'),
                'meta' => $case->patient_name ?? 'Patient',
                'time' => date('g:i a', strtotime(str_replace('T', ' ', $case->initial_delivery_date))),
                'icon' => 'fa-truck-fast',
                'color' => '#6d5dfc',
                'modal' => '#updateDeliveryDate' . $case->id,
            ]);
        }
        if ($dashboardSampleDataMode && $activityItems->isEmpty()) {
            $activityItems = collect([
                ['title' => 'New order #ORD-1256', 'meta' => '1,250.00 ' . $currencyLabel, 'time' => '2m ago', 'icon' => 'fa-clipboard-list', 'color' => '#20b997', 'modal' => null],
                ['title' => 'Payment received from John D.', 'meta' => '850.00 ' . $currencyLabel, 'time' => '15m ago', 'icon' => 'fa-money-bill-wave', 'color' => '#20b997', 'modal' => null],
                ['title' => 'New customer registered', 'meta' => 'Sarah Williams', 'time' => '1h ago', 'icon' => 'fa-user-plus', 'color' => '#6d5dfc', 'modal' => null],
                ['title' => 'Order #ORD-1255 shipped', 'meta' => '2,150.00 ' . $currencyLabel, 'time' => '2h ago', 'icon' => 'fa-truck-fast', 'color' => '#2f8fed', 'modal' => null],
                ['title' => 'Low stock alert', 'meta' => 'Zirconia blocks', 'time' => '3h ago', 'icon' => 'fa-triangle-exclamation', 'color' => '#ef476f', 'modal' => null],
            ]);
        }

        $kpiCards = [
            ['label' => 'Total Revenue', 'value' => $currencyLabel . ' ' . number_format($displayRevenueTotal, 0), 'canvas' => 'solentDashSparkRevenue', 'note' => 'current month payments total'],
            ['label' => 'Orders', 'value' => number_format($displayOrdersTotal), 'canvas' => 'solentDashSparkOrders', 'note' => 'completed cases total'],
            ['label' => 'Customers', 'value' => number_format($displayNewCustomers), 'canvas' => 'solentDashSparkClients', 'note' => 'active doctor network'],
            ['label' => 'Conversion Rate', 'value' => number_format($displayConversionRate, 2) . '%', 'canvas' => 'solentDashSparkConversion', 'note' => 'completed vs workload'],
            ['label' => 'Workload Mix', 'value' => number_format($displayWorkloadTotal), 'canvas' => 'solentDashSparkMix', 'note' => 'jobs each column represents amount of jobs in that day'],
        ];
    @endphp

    <link href="{{ asset('assets/css/elegant-dashboard.css') }}" rel="stylesheet">

    <div class="solent-dash" data-dashboard-sample-mode="{{ $dashboardSampleDataMode ? 'on' : 'off' }}">
        <main class="solent-dash-shell">
            <section class="solent-dash-kpis" aria-label="Dashboard key metrics">
                @foreach ($kpiCards as $card)
                    <article class="solent-dash-kpi-card">
                        <div class="solent-dash-kpi-head">
                            <div>
                                <span class="solent-dash-label">{{ $card['label'] }}</span>
                                <strong class="solent-dash-value">{{ $card['value'] }}</strong>
                            </div>
                        </div>
                        <div class="solent-dash-mini-chart"><canvas id="{{ $card['canvas'] }}"></canvas></div>
                        <span class="solent-dash-note">{{ $card['note'] }} <i class="fa-regular fa-circle-question"></i></span>
                    </article>
                @endforeach
            </section>

            <section class="solent-dash-layout">
                <article class="solent-dash-panel solent-dash-load-panel">
                    <div class="solent-dash-panel-head">
                        <div>
                            <span class="solent-dash-load-kicker">Production Load</span>
                            <h2 class="solent-dash-panel-title">Stage Daily Utilization</h2>
                        </div>
                        <button class="solent-dash-chip" type="button">Live mix</button>
                    </div>
                    <div class="solent-dash-load-body">
                        <div>
                            <div class="solent-dash-load-chart"><canvas id="solentDashProductionLoad"></canvas></div>
                            <div class="solent-dash-load-legend" aria-hidden="true">
                                <span><i class="solent-dash-load-swatch solent-dash-load-swatch-primary"></i>Utilization %</span>
                                <span><i class="solent-dash-load-swatch solent-dash-load-swatch-secondary"></i>Jobs in stage</span>
                            </div>
                        </div>
                        <div class="solent-dash-load-list">
                            @foreach ($productionLoadHighlights as $stageLoad)
                                <div class="solent-dash-load-item">
                                    <div>
                                        <strong>{{ $stageLoad['label'] }}</strong>
                                        <span>{{ $stageLoad['active'] }} active | {{ $stageLoad['waiting'] }} waiting</span>
                                    </div>
                                    <span class="solent-dash-load-jobs">{{ number_format($stageLoad['jobs']) }} jobs</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>

                <article class="solent-dash-panel solent-dash-panel-large">
                    <div class="solent-dash-panel-head">
                        <div>
                            <h2 class="solent-dash-panel-title">Revenue Overview</h2>
                            <p class="solent-dash-panel-value">{{ $currencyLabel }} {{ number_format($displayRevenueTotal, 0) }}</p>
                        </div>
                        <button class="solent-dash-chip" type="button">Daily <i class="fa-solid fa-chevron-down"></i></button>
                    </div>
                    <div class="solent-dash-chart-tall"><canvas id="solentDashRevenueOverview"></canvas></div>
                </article>

                <article class="solent-dash-panel">
                    <div class="solent-dash-panel-head">
                        <h2 class="solent-dash-panel-title">Sales by Channel</h2>
                    </div>
                    <div class="solent-dash-donut-total">
                        <span>Total</span>
                        <strong>{{ $currencyLabel }} {{ number_format($displayRevenueTotal, 0) }}</strong>
                    </div>
                    <div class="solent-dash-mix">
                        <div class="solent-dash-donut">
                            <canvas id="solentDashWorkloadMix"></canvas>
                            <div class="solent-dash-donut-center" aria-hidden="true"></div>
                        </div>
                        <div class="solent-dash-legend">
                            @foreach ($dashboardMetrics['workloadMix'] as $mix)
                                @php $mixTotal = collect($dashboardMetrics['workloadMix'])->sum('value'); @endphp
                                <div class="solent-dash-legend-item">
                                    <i class="solent-dash-dot" style="background: {{ $mix['color'] }}"></i>
                                    <strong>{{ $mix['label'] }}</strong>
                                    <span class="solent-dash-amount">{{ number_format($mix['value']) }}</span>
                                    <span class="solent-dash-percent">{{ number_format(($mix['value'] / max(1, $mixTotal)) * 100, 1) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            </section>

            <section class="solent-dash-mid-layout">
                <article class="solent-dash-panel">
                    <div class="solent-dash-panel-head">
                        <div>
                            <h2 class="solent-dash-panel-title">Customer Overview</h2>
                            <p class="solent-dash-panel-value">{{ number_format($displayNewCustomers) }}</p>
                        </div>
                    </div>
                    <div class="solent-dash-client-panel">
                        <div class="solent-dash-map" aria-hidden="true"></div>
                        <div class="solent-dash-country-list">
                            @forelse ($dashboardMetrics['clientCountries'] as $country)
                                <div class="solent-dash-country">
                                    <div class="solent-dash-country-line">
                                        <span>{{ $country['name'] }}</span>
                                        <strong>{{ number_format($country['value']) }}</strong>
                                    </div>
                                    <div class="solent-dash-track"><span style="width: {{ $country['width'] }}%"></span></div>
                                </div>
                            @empty
                                <div class="solent-dash-country">
                                    <div class="solent-dash-country-line">
                                        <span>No regional data</span>
                                        <strong>0</strong>
                                    </div>
                                    <div class="solent-dash-track"><span style="width: 0%"></span></div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </article>

                <article class="solent-dash-panel">
                    <div class="solent-dash-panel-head">
                        <div>
                            <h2 class="solent-dash-panel-title">Orders Over Time</h2>
                            <p class="solent-dash-panel-value">{{ number_format($displayOrdersTotal) }}</p>
                        </div>
                        <button class="solent-dash-chip" type="button">Daily <i class="fa-solid fa-chevron-down"></i></button>
                    </div>
                    <div class="solent-dash-chart-medium"><canvas id="solentDashOrdersOverTime"></canvas></div>
                </article>

                <article class="solent-dash-panel">
                    <div class="solent-dash-panel-head">
                        <h2 class="solent-dash-panel-title">Recent Activity</h2>
                        <button class="solent-dash-chip" type="button">View all</button>
                    </div>
                    <div class="solent-dash-activity-list">
                        @forelse ($activityItems->take(5) as $activity)
                            <a class="solent-dash-activity-item" href="{{ $activity['modal'] ?: '#' }}" @if($activity['modal']) data-toggle="modal" @endif>
                                <span class="solent-dash-activity-icon" style="background: {{ $activity['color'] }}18; color: {{ $activity['color'] }}"><i class="fa-solid {{ $activity['icon'] }}"></i></span>
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
                    <small style="text-align:center;font-size: 60%;color: gray;">PAYMENT ID : {{ $payment->id }}</small>
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
                        <small style="text-align:center;font-size: 60%;color: gray;">CASE ID : {{ $case->id }}</small>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('js')
    <script src="{{ asset('white') }}/js/plugins/chartjs.min.js"></script>
    <script>
        const solentDashPalette = {
            purple: '#6d5dfc',
            blue: '#2f8fed',
            teal: '#20b997',
            orange: '#fb8c1f',
            pink: '#ec5fa5',
            grid: 'rgba(17, 24, 39, 0.08)'
        };

        const solentDashData = {
            kpiSparks: {!! json_encode($dashboardMetrics['kpiSparks']) !!},
            kpiBars: {!! json_encode($dashboardMetrics['kpiBars']) !!},
            revenueLabels: {!! json_encode($revenueTrendLabels) !!},
            revenueValues: {!! json_encode($revenueTrendValues->values()->all()) !!},
            mixLabels: {!! json_encode(collect($dashboardMetrics['workloadMix'])->pluck('label')->values()->all()) !!},
            mixValues: {!! json_encode(collect($dashboardMetrics['workloadMix'])->pluck('value')->values()->all()) !!},
            mixColors: {!! json_encode(collect($dashboardMetrics['workloadMix'])->pluck('color')->values()->all()) !!},
            orderLabels: {!! json_encode($ordersTrendLabels) !!},
            orderValues: {!! json_encode($ordersTrendValues->values()->all()) !!},
            productionLoad: {
                labels: {!! json_encode($productionLoadRows->pluck('label')->values()->all()) !!},
                utilization: {!! json_encode($productionLoadRows->pluck('utilization')->values()->all()) !!},
                jobsScaled: {!! json_encode($productionLoadRows->pluck('jobsScaled')->values()->all()) !!},
                jobs: {!! json_encode($productionLoadRows->pluck('jobs')->values()->all()) !!},
                colors: {!! json_encode($productionLoadRows->pluck('color')->values()->all()) !!}
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            solentDashInitSparklines();
            solentDashInitRevenue();
            solentDashInitMix();
            solentDashInitOrders();
            solentDashInitProductionLoad();
        });

        function solentDashGradient(ctx, color, height) {
            const gradient = ctx.createLinearGradient(0, 0, 0, height);
            gradient.addColorStop(0, color + '33');
            gradient.addColorStop(1, color + '03');
            return gradient;
        }

        function solentDashBaseOptions(extra) {
            return Object.assign({
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: '#101828',
                    titleFontColor: '#ffffff',
                    bodyFontColor: '#e5e7eb',
                    displayColors: false
                }
            }, extra || {});
        }

        function solentDashSpark(canvasId, values, color) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: values.map(function (_, index) { return index + 1; }),
                    datasets: [{
                        data: values,
                        borderColor: color,
                        backgroundColor: solentDashGradient(ctx, color, 40),
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: true,
                        lineTension: 0.4
                    }]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{ display: false }],
                        yAxes: [{ display: false }]
                    },
                    tooltips: { enabled: false }
                })
            });
        }

        function solentDashInitSparklines() {
            solentDashSpark('solentDashSparkRevenue', solentDashData.kpiSparks.revenue, solentDashPalette.purple);
            solentDashSpark('solentDashSparkOrders', solentDashData.kpiSparks.orders, solentDashPalette.teal);
            solentDashSpark('solentDashSparkClients', solentDashData.kpiSparks.clients, solentDashPalette.blue);
            solentDashSpark('solentDashSparkConversion', solentDashData.kpiSparks.conversion, solentDashPalette.purple);
            solentDashMiniBars('solentDashSparkMix', solentDashData.kpiBars.workload, solentDashPalette.orange);
        }

        function solentDashMiniBars(canvasId, values, color) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: values.map(function (_, index) { return index + 1; }),
                    datasets: [{
                        data: values,
                        backgroundColor: color,
                        borderWidth: 0,
                        barThickness: 4,
                        maxBarThickness: 5
                    }]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{ display: false }],
                        yAxes: [{ display: false }]
                    },
                    tooltips: { enabled: false }
                })
            });
        }

        function solentDashInitRevenue() {
            const canvas = document.getElementById('solentDashRevenueOverview');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: solentDashData.revenueLabels,
                    datasets: [{
                        data: solentDashData.revenueValues,
                        borderColor: solentDashPalette.purple,
                        backgroundColor: solentDashGradient(ctx, solentDashPalette.purple, 240),
                        borderWidth: 3,
                        pointBackgroundColor: solentDashPalette.purple,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        lineTension: 0.38
                    }]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#79829a', fontSize: 11 } }],
                        yAxes: [{ ticks: { beginAtZero: true, fontColor: '#79829a', fontSize: 11 }, gridLines: { color: solentDashPalette.grid, drawBorder: false } }]
                    }
                })
            });
        }

        function solentDashInitMix() {
            const canvas = document.getElementById('solentDashWorkloadMix');
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: solentDashData.mixLabels,
                    datasets: [{
                        data: solentDashData.mixValues,
                        backgroundColor: solentDashData.mixColors,
                        borderColor: '#ffffff',
                        borderWidth: 3
                    }]
                },
                options: solentDashBaseOptions({ cutoutPercentage: 62 })
            });
        }

        function solentDashInitOrders() {
            const canvas = document.getElementById('solentDashOrdersOverTime');
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: solentDashData.orderLabels,
                    datasets: [{
                        data: solentDashData.orderValues,
                        backgroundColor: '#20b997',
                        borderWidth: 0,
                        barThickness: 22
                    }]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#79829a', fontSize: 11 } }],
                        yAxes: [{ ticks: { beginAtZero: true, fontColor: '#79829a', fontSize: 11 }, gridLines: { color: solentDashPalette.grid, drawBorder: false } }]
                    }
                })
            });
        }

        function solentDashInitProductionLoad() {
            const canvas = document.getElementById('solentDashProductionLoad');
            if (!canvas) return;
            new Chart(canvas.getContext('2d'), {
                type: 'horizontalBar',
                data: {
                    labels: solentDashData.productionLoad.labels,
                    datasets: [
                        {
                            label: 'Utilization %',
                            data: solentDashData.productionLoad.utilization,
                            backgroundColor: solentDashData.productionLoad.colors,
                            borderWidth: 0,
                            barThickness: 8,
                            maxBarThickness: 8
                        },
                        {
                            label: 'Jobs in stage',
                            data: solentDashData.productionLoad.jobsScaled,
                            backgroundColor: 'rgba(149, 157, 185, 0.26)',
                            borderWidth: 0,
                            barThickness: 8,
                            maxBarThickness: 8
                        }
                    ]
                },
                options: solentDashBaseOptions({
                    scales: {
                        xAxes: [{
                            ticks: { beginAtZero: true, max: 100, fontColor: '#98a2b3', fontSize: 10, stepSize: 20 },
                            gridLines: { color: 'rgba(17, 24, 39, 0.06)', drawBorder: false }
                        }],
                        yAxes: [{
                            ticks: { fontColor: '#344054', fontSize: 10, fontStyle: '600' },
                            gridLines: { display: false }
                        }]
                    },
                    tooltips: {
                        backgroundColor: '#101828',
                        titleFontColor: '#ffffff',
                        bodyFontColor: '#e5e7eb',
                        callbacks: {
                            label: function (tooltipItem, data) {
                                if (tooltipItem.datasetIndex === 0) {
                                    return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.xLabel + '%';
                                }

                                const jobs = solentDashData.productionLoad.jobs[tooltipItem.index];
                                return data.datasets[tooltipItem.datasetIndex].label + ': ' + jobs + ' jobs';
                            }
                        }
                    }
                })
            });
        }
    </script>
@endpush
