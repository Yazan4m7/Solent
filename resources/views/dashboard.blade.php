@extends('layouts.app', ['pageSlug' => 'Dashboard'])

@push('css')
    <style>
        .solent-dash {
            position: relative;
        }

        .solent-dash-metrics-guide-toggle {
            align-items: center;
            appearance: none;
            background: #111827;
            border: 0;
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(17, 24, 39, 0.18);
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            font-weight: 800;
            height: 28px;
            justify-content: center;
            line-height: 1;
            padding: 0;
            position: absolute;
            right: 10px;
            top: 10px;
            width: 28px;
            z-index: 30;
        }

        .solent-dash-metrics-guide-panel {
            background: #ffffff;
            border: 1px solid #dbe1ea;
            border-radius: 14px;
            box-shadow: 0 22px 55px rgba(17, 24, 39, 0.18);
            max-height: min(78vh, 820px);
            overflow: auto;
            padding: 14px;
            position: absolute;
            right: 10px;
            top: 46px;
            width: min(760px, calc(100% - 20px));
            z-index: 29;
        }

        .solent-dash-metrics-guide-head {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .solent-dash-metrics-guide-title {
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            margin: 0;
        }

        .solent-dash-metrics-guide-meta {
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            margin: 4px 0 0;
        }

        .solent-dash-metrics-guide-close {
            background: transparent;
            border: 0;
            color: #6b7280;
            cursor: pointer;
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
            padding: 0;
        }

        .solent-dash-metrics-guide-badge {
            background: #eef2ff;
            border-radius: 999px;
            color: #4f46e5;
            display: inline-flex;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 8px;
        }

        .solent-dash-metrics-guide-table {
            border-collapse: collapse;
            width: 100%;
        }

        .solent-dash-metrics-guide-table th,
        .solent-dash-metrics-guide-table td {
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            line-height: 1.45;
            padding: 8px 9px;
            text-align: left;
            vertical-align: top;
        }

        .solent-dash-metrics-guide-table th {
            background: #f8fafc;
            color: #374151;
            font-weight: 800;
            position: sticky;
            top: -14px;
            z-index: 1;
        }

        .solent-dash-metrics-guide-table td:first-child,
        .solent-dash-metrics-guide-table th:first-child {
            white-space: nowrap;
            width: 46px;
        }

        .solent-dash-metrics-guide-table td:nth-child(2),
        .solent-dash-metrics-guide-table th:nth-child(2) {
            min-width: 180px;
        }

        .solent-dash-metrics-guide-table td:nth-child(3),
        .solent-dash-metrics-guide-table th:nth-child(3) {
            min-width: 140px;
        }

        .solent-dash-metrics-guide-table td:last-child,
        .solent-dash-metrics-guide-table th:last-child {
            min-width: 120px;
        }

        @media (max-width: 768px) {
            .solent-dash-metrics-guide-panel {
                max-height: 72vh;
                padding: 12px;
                right: 8px;
                top: 42px;
                width: calc(100% - 16px);
            }

            .solent-dash-metrics-guide-toggle {
                right: 8px;
                top: 8px;
            }
        }
    </style>
@endpush

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
                ['title' => 'New dental case #CASE-1256', 'meta' => 'Implant crown | Dr. Lina Haddad', 'time' => '2m ago', 'icon' => 'fa-clipboard-list', 'color' => '#20b997', 'modal' => null],
                ['title' => 'Clinic payment received', 'meta' => '850.00 ' . $currencyLabel . ' | Abdoun Dental Center', 'time' => '15m ago', 'icon' => 'fa-money-bill-wave', 'color' => '#20b997', 'modal' => null],
                ['title' => 'Case ready for delivery', 'meta' => 'Zirconia bridge | Sweifieh Clinic', 'time' => '1h ago', 'icon' => 'fa-truck-fast', 'color' => '#6d5dfc', 'modal' => null],
                ['title' => 'Design stage completed', 'meta' => '6-unit anterior case', 'time' => '2h ago', 'icon' => 'fa-pen-ruler', 'color' => '#2f8fed', 'modal' => null],
                ['title' => 'QC approved', 'meta' => 'E.max veneer case', 'time' => '3h ago', 'icon' => 'fa-clipboard-check', 'color' => '#ec5fa5', 'modal' => null],
            ]);
        }

        $kpiCards = [
            ['label' => 'Total Revenue', 'value' => $currencyLabel . ' ' . number_format($displayRevenueTotal, 0), 'canvas' => 'solentDashSparkRevenue', 'note' => 'current month payments total'],
            ['label' => 'Orders', 'value' => number_format($displayOrdersTotal), 'canvas' => 'solentDashSparkOrders', 'note' => 'completed cases total'],
            ['label' => 'Customers', 'value' => number_format($displayNewCustomers), 'canvas' => 'solentDashSparkClients', 'note' => 'active doctor network'],
            ['label' => 'Conversion Rate', 'value' => number_format($displayConversionRate, 2) . '%', 'canvas' => 'solentDashSparkConversion', 'note' => 'completed vs workload'],
            ['label' => 'Workload Mix', 'value' => number_format($displayWorkloadTotal), 'canvas' => 'solentDashSparkMix', 'note' => 'jobs each column represents amount of jobs in that day'],
        ];

        $dashboardMetricGuideRows = [
            ['metric' => 'Total Revenue card', 'value' => $currencyLabel . ' ' . number_format($displayRevenueTotal, 0), 'formula' => 'Sum of payment.amount over the last 30 days. In sample mode it shows the larger of the real total or 428,540.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Orders card', 'value' => number_format($displayOrdersTotal), 'formula' => 'Sum of completed case counts across the last 30 days. In sample mode it shows the larger of the real total or 3,721.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Customers card', 'value' => number_format($displayNewCustomers), 'formula' => 'Unique client_id count from today\'s undelivered deliveries. In sample mode it shows the larger of the real total or 2,145.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Conversion Rate card', 'value' => number_format($displayConversionRate, 2) . '%', 'formula' => 'CompletedJobsToday / (CompletedJobsToday + ActiveJobsToday + waitingJobsToday) * 100, rounded to 2 decimals. In sample mode it shows the larger of the real rate or 2.48%.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Workload Mix card', 'value' => number_format($displayWorkloadTotal), 'formula' => 'completedUnits7dTotal + activeUnits + waitingUnits. In sample mode it shows the larger of the real total or 128.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Completed Jobs Today', 'value' => number_format($completedUnits), 'formula' => 'Units inside cases whose actual_delivery_date is today. A unit counts only when job.material.count_as_unit = 1, then it counts the comma-separated unit_num entries.', 'source' => 'Real'],
            ['metric' => 'Waiting Jobs Today', 'value' => number_format($waitingUnits), 'formula' => 'All unassigned jobs where stage != -1, counted as units. Despite the label, it is not limited to today.', 'source' => 'Real'],
            ['metric' => 'Active Jobs Today', 'value' => number_format($activeUnits), 'formula' => 'All assigned jobs where stage != -1, counted as units. Despite the label, it is not limited to today.', 'source' => 'Real'],
            ['metric' => 'Deliveries Today count', 'value' => number_format($deliveriesTodayCount), 'formula' => 'Cases where initial_delivery_date is today and delivered_to_client = 0.', 'source' => 'Real'],
            ['metric' => 'Payments Today count', 'value' => number_format($paymentsCount), 'formula' => 'Count of payment rows whose created_at is today.', 'source' => 'Real'],
            ['metric' => 'Customer Overview total', 'value' => number_format($displayNewCustomers), 'formula' => 'Same number as the Customers card.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Revenue Overview total', 'value' => $currencyLabel . ' ' . number_format($displayRevenueTotal, 0), 'formula' => 'Same displayed total as the Total Revenue card.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Revenue Overview chart', 'value' => collect($revenueTrendValues)->implode(', '), 'formula' => 'Last 7 daily payment sums from the 30-day collection series. If sample mode is on and the real sum is 0, it falls back to 360, 320, 410, 620, 440, 660, 345.', 'source' => $dashboardSampleDataMode ? 'Real or sample fallback' : 'Real'],
            ['metric' => 'Orders Over Time total', 'value' => number_format($displayOrdersTotal), 'formula' => 'Same displayed total as the Orders card.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Orders Over Time chart', 'value' => collect($ordersTrendValues)->implode(', '), 'formula' => '7 daily completed-case counts. If sample mode is on and the real sum is 0, it falls back to 820, 710, 960, 880, 835, 930, 948.', 'source' => $dashboardSampleDataMode ? 'Real or sample fallback' : 'Real'],
            ['metric' => 'Sales by Channel total', 'value' => $currencyLabel . ' ' . number_format($displayRevenueTotal, 0), 'formula' => 'Same displayed total as the Total Revenue card, even though the donut below is actually workload data.', 'source' => $dashboardSampleDataMode ? 'Real + sample floor' : 'Real'],
            ['metric' => 'Revenue sparkline', 'value' => collect($dashboardMetrics['kpiSparks']['revenue'])->implode(', '), 'formula' => '12-point mini series for the Total Revenue card. Uses last 12 payment totals, or sample values in sample mode.', 'source' => $dashboardSampleDataMode ? 'Real or sample series' : 'Real'],
            ['metric' => 'Orders sparkline', 'value' => collect($dashboardMetrics['kpiSparks']['orders'])->implode(', '), 'formula' => '12-point mini series for the Orders card. Uses last 12 completed-case totals, or sample values in sample mode.', 'source' => $dashboardSampleDataMode ? 'Real or sample series' : 'Real'],
            ['metric' => 'Customers sparkline', 'value' => collect($dashboardMetrics['kpiSparks']['clients'])->implode(', '), 'formula' => '12 points. In live mode it repeats the same newCustomers value 12 times. In sample mode it uses a sample series.', 'source' => $dashboardSampleDataMode ? 'Sample series' : 'Derived real'],
            ['metric' => 'Conversion sparkline', 'value' => collect($dashboardMetrics['kpiSparks']['conversion'])->implode(', '), 'formula' => '12 points. In live mode it repeats the same conversionRate value 12 times. In sample mode it uses a sample series.', 'source' => $dashboardSampleDataMode ? 'Sample series' : 'Derived real'],
            ['metric' => 'Workload mini bars', 'value' => collect($dashboardMetrics['kpiBars']['workload'])->implode(', '), 'formula' => 'In live mode: completedUnits7dTotal, activeUnits, waitingUnits, deliveriesTodayCount, paymentsCount. In sample mode it uses a sample 12-point bar series.', 'source' => $dashboardSampleDataMode ? 'Sample series' : 'Derived real'],
        ];

        foreach ($dashboardMetrics['workloadMix'] as $mix) {
            $mixTotal = collect($dashboardMetrics['workloadMix'])->sum('value');
            $dashboardMetricGuideRows[] = [
                'metric' => 'Workload donut - ' . $mix['label'],
                'value' => number_format($mix['value']) . ' (' . number_format(($mix['value'] / max(1, $mixTotal)) * 100, 1) . '%)',
                'formula' => 'Donut value from the workloadMix array. Percent = item value / sum of all donut values * 100.',
                'source' => $dashboardSampleDataMode ? 'Real or sample minimum' : 'Real',
            ];
        }

        foreach ($productionLoadRows as $stageLoad) {
            $dashboardMetricGuideRows[] = [
                'metric' => 'Production Load - ' . $stageLoad['label'],
                'value' => number_format($stageLoad['jobs']) . ' jobs | ' . number_format($stageLoad['active']) . ' active | ' . number_format($stageLoad['waiting']) . ' waiting | ' . number_format($stageLoad['utilization']) . '%',
                'formula' => 'Reads productionLoadRows. No real controller calculation is passed to this view, so this comes from the view fallback rows.',
                'source' => $dashboardSampleDataMode ? 'Sample fallback' : 'View fallback',
            ];
        }

        foreach ($dashboardMetrics['clientCountries'] as $country) {
            $dashboardMetricGuideRows[] = [
                'metric' => 'Customer Overview - ' . $country['name'],
                'value' => number_format($country['value']) . ' (' . $country['width'] . '% bar width)',
                'formula' => 'Comes from the clientCountries array. In sample mode these are hardcoded floors driven by ordersTotal, deliveriesTodayCount, or paymentsCount.',
                'source' => $dashboardSampleDataMode ? 'Sample fallback' : 'Real if provided',
            ];
        }
    @endphp

    <link href="{{ asset('assets/css/elegant-dashboard.css') }}" rel="stylesheet">

    <div class="solent-dash" data-dashboard-sample-mode="{{ $dashboardSampleDataMode ? 'on' : 'off' }}">
        <button
            type="button"
            class="solent-dash-metrics-guide-toggle"
            id="solentDashMetricsGuideToggle"
            aria-label="Open dashboard metrics guide"
            aria-controls="solentDashMetricsGuidePanel"
            aria-expanded="false"
        >!</button>
        <section class="solent-dash-metrics-guide-panel" id="solentDashMetricsGuidePanel" hidden>
            <div class="solent-dash-metrics-guide-head">
                <div>
                    <h3 class="solent-dash-metrics-guide-title">Dashboard Numbers Guide</h3>
                    <p class="solent-dash-metrics-guide-meta">Each row shows the current number, how it is calculated, and whether it is real or sample-driven.</p>
                </div>
                <div>
                    <span class="solent-dash-metrics-guide-badge">{{ $dashboardSampleDataMode ? 'Sample mode ON' : 'Sample mode OFF' }}</span>
                    <button type="button" class="solent-dash-metrics-guide-close" id="solentDashMetricsGuideClose" aria-label="Close dashboard metrics guide">&times;</button>
                </div>
            </div>
            <table class="solent-dash-metrics-guide-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Metric</th>
                        <th>Current Value</th>
                        <th>How It Is Calculated</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dashboardMetricGuideRows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['metric'] }}</td>
                            <td>{{ $row['value'] }}</td>
                            <td>{{ $row['formula'] }}</td>
                            <td>{{ $row['source'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
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
                        <button class="solent-dash-map-button" type="button" data-toggle="modal" data-target="#solentJordanMapModal" aria-label="Expand Jordan city activity map">
                            @include('partials.jordan-dashboard-map')
                        </button>
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

    <div class="modal fade solent-dash-map-modal" id="solentJordanMapModal" tabindex="-1" role="dialog" aria-labelledby="solentJordanMapModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="solentJordanMapModalTitle">Jordan Clinic Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @include('partials.jordan-dashboard-map', ['expanded' => true])
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
            solentDashInitMetricsGuide();
            solentDashInitSparklines();
            solentDashInitRevenue();
            solentDashInitMix();
            solentDashInitOrders();
            solentDashInitProductionLoad();
        });

        function solentDashInitMetricsGuide() {
            const toggle = document.getElementById('solentDashMetricsGuideToggle');
            const panel = document.getElementById('solentDashMetricsGuidePanel');
            const close = document.getElementById('solentDashMetricsGuideClose');

            if (!toggle || !panel || !close) return;

            const setOpen = function (open) {
                panel.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            setOpen(false);

            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                setOpen(panel.hidden);
            });

            close.addEventListener('click', function (event) {
                event.preventDefault();
                setOpen(false);
            });

            panel.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('click', function () {
                if (!panel.hidden) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });
        }

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
