@extends('layouts.app', ['pageSlug' => 'Home'])
@section('content')



    <link href="{{ asset('assets/css/elegant-dashboard.css') }}" rel="stylesheet">
    <style>
        /* Hide legacy top navbar only on this page */
        body.with-full-header nav.navbar.navbar-absolute {
            display: none !important;
        }
        /* Remove top spacing and padding for this page only */
        body.with-full-header .main-panel,
        body.with-full-header .main-panel .content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        body.with-full-header .main-panel .content {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        body.with-full-header {
            --ed-sidebar-width: 260px;
        }
        body.with-full-header .sidebar,
        body.with-full-header .sidebar .sidebar-wrapper {
            top: 0 !important;
        }
        body.with-full-header .full-width-header {
            width: calc(100% + var(--ed-sidebar-width));
            margin-left: calc(-1 * var(--ed-sidebar-width));
            margin-top: 0;
            padding-left: 32px;
            padding-right: 32px;
        }
        @media (max-width: 991.98px) {
            body.with-full-header .full-width-header {
                width: 100%;
                margin-left: 0;
            }
        }

        /* Premium pills for performance toggle */
        .ed-dashboard .performanceBtns {
            background: #f0f4f8 !important;
            color: #1f2a3d !important;
            border: 1px solid #d9e2ec !important;
            border-radius: 14px !important;
            padding: 8px 14px !important;
            transition: all 0.2s ease-in-out;
        }

        .ed-dashboard .performanceBtns.active,
        .ed-dashboard .performanceBtns:hover {
            background: linear-gradient(120deg, #0f7c63, #13a29b) !important;
            color: #0b0e14 !important;
            border-color: transparent !important;
            box-shadow: 0 10px 24px rgba(19, 162, 155, 0.25) !important;
        }
    </style>



    @php
        $paymentsReceivedToday = $paymentsReceivedToday ?? [];
        $DeliveriesToday = $DeliveriesToday ?? [];
        $compUnitsCount7Days = $compUnitsCount7Days ?? [];
        $compCasesCount7Days = $compCasesCount7Days ?? [];
        $collectionsInLast30Days = $collectionsInLast30Days ?? [];
        $compCasesCount30Days = $compCasesCount30Days ?? [];
        $compUnitsCount30Days = $compUnitsCount30Days ?? [];
        $sales30Days = $sales30Days ?? [];
        $last7DaysLabels = $last7DaysLabels ?? [];
        $last30DaysLabels = $last30DaysLabels ?? [];

        $paymentsCount = collect($paymentsReceivedToday ?? [])->count();
        $deliveriesTodayCount = collect($DeliveriesToday ?? [])->count();
        $completedUnits7dTotal = array_sum($compUnitsCount7Days ?? []);
        $completedCases7dTotal = array_sum($compCasesCount7Days ?? []);
        $revenueTotal = collect($collectionsInLast30Days ?? [])->sum();
        $ordersTotal = collect($compCasesCount30Days ?? [])->sum();
        $newCustomers = collect($DeliveriesToday ?? [])->pluck('client_id')->unique()->count();
        $totalUnits = ($CompletedJobsToday ?? 0) + ($ActiveJobsToday ?? 0) + ($waitingJobsToday ?? 0);
        $conversionRate = $totalUnits > 0 ? round((($CompletedJobsToday ?? 0) / $totalUnits) * 100, 2) : 0;

        $activityItems = collect();
        foreach ($paymentsReceivedToday ?? [] as $payment) {
            $activityItems->push([
                'title' => 'Payment received from ' . ($payment->client->name ?? 'Doctor'),
                'time' => $payment->created_at,
                'subtitle' => $payment->amount . ' JOD | Collector ' . ($payment->collectorUserRecord->name_initials ?? ''),
                'icon' => 'fa-coins',
                'color' => 'icon-green',
                'modal' => '#receivePaymentModal' . $payment->id,
            ]);
        }
        foreach ($DeliveriesToday ?? [] as $case) {
            $activityItems->push([
                'title' => 'Delivery | ' . ($case->client->name ?? ''),
                'time' => $case->initial_delivery_date,
                'subtitle' => ($case->patient_name ?? '') . ' | ' . date('g:i a', strtotime(str_replace('T', ' ', $case->initial_delivery_date))),
                'icon' => 'fa-truck',
                'color' => 'icon-blue',
                'modal' => '#updateDeliveryDate' . $case->id,
            ]);
        }
        $activityItems = $activityItems->sortByDesc('time')->take(6);
    @endphp

    <div class="ed-dashboard container-fluid px-0">
        <div class="full-width-header">
            <div class="header-left">
                <div class="logo">
                    <div class="logo-text">{{ config('site_vars.projectNameShort') ?? 'Dashboard' }}</div>
                </div>
                <div class="nav-menu">
                    <a class="nav-item active" href="#">Dashboard</a>
                    <a class="nav-item" href="#">Analytics</a>
                    <a class="nav-item" href="#">Orders</a>
                    <a class="nav-item" href="#">Customers</a>
                </div>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="header-actions">
                    <div class="icon-button"><i class="fas fa-bell"></i></div>
                    <div class="icon-button"><i class="fas fa-cog"></i></div>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth()->user()->name ?? 'U',0,2)) }}</div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth()->user()->name ?? 'User' }}</span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
            </div>
        </div>

        <main class="main-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">TOTAL REVENUE</div>
                        <div class="stat-icon icon-blue"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                    <div class="stat-value">${{ number_format($revenueTotal ?? 0, 0) }}</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> Live</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">TOTAL ORDERS</div>
                        <div class="stat-icon icon-green"><i class="fas fa-shopping-bag"></i></div>
                    </div>
                    <div class="stat-value">{{ number_format($ordersTotal ?? 0) }}</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> Rolling 30 days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">NEW CUSTOMERS</div>
                        <div class="stat-icon icon-pink"><i class="fas fa-user-plus"></i></div>
                    </div>
                    <div class="stat-value">{{ $newCustomers }}</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> Deliveries today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">CONVERSION RATE</div>
                        <div class="stat-icon icon-purple"><i class="fas fa-chart-pie"></i></div>
                    </div>
                    <div class="stat-value">{{ $conversionRate }}%</div>
                    <div class="stat-change {{ $conversionRate < 1 ? 'negative' : '' }}">
                        <i class="fas {{ $conversionRate < 1 ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                        Today
                    </div>
                </div>
            </div>

            <div class="grid-card card card-chart shadow-sm mb-3">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <p class="card-eyebrow mb-1">Performance</p>
                            <h4 class="card-title mb-0">Monthly Performance</h4>
                        </div>
                        <div class="btn-group btn-group-toggle chart-toggle" data-toggle="buttons">
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns active" id="0">
                                <input type="radio" name="options" checked>
                                <span>Units</span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="1">
                                <input type="radio" class="d-none d-sm-none" name="options">
                                <span>Cases</span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="3">
                                <input type="radio" class="d-none" name="options">
                                <span>Sales</span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="2">
                                <input type="radio" class="d-none" name="options">
                                <span>Payments</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartBig1"></canvas>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="chart-container">
                    <div class="section-header">
                        <h2 class="section-title">Revenue Overview</h2>
                        <a href="#" class="view-all">View Report</a>
                    </div>
                    <div class="chart-placeholder">
                        <canvas id="completedChart"></canvas>
                    </div>
                </div>
                <div class="activity-container">
                    <div class="section-header">
                        <h2 class="section-title">Recent Activity</h2>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="activity-list">
                        @if(isset($activityItems))
                        @forelse ($activityItems as $item)
                            <div class="activity-item" data-toggle="modal" data-target="{{ $item['modal'] }}">
                                <div class="activity-icon {{ $item['color'] }}">
                                    <i class="fas {{ $item['icon'] }}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $item['title'] }}</div>
                                    <div class="activity-time">{{ $item['subtitle'] }} - {{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <div class="activity-icon icon-blue">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">No recent activity</div>
                                    <div class="activity-time">Stay tuned</div>
                                </div>
                            </div>
                        @endforelse
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Payment modals --}}
    @foreach ($paymentsReceivedToday as $payment)
        <div class="modal fade" id="receivePaymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="exampleModalLabelform">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Receive Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body lightGrayTopBorder">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6"><strong>Doctor:</strong></div>
                                <div class="col-md-6">{{ $payment->client->name }}</div>
                            </div>
                        </div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6"><strong>Collected from doctor by: </strong></div>
                                <div class="col-md-6">{{ $payment->collectorFullName() }}</div>
                            </div>
                        </div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6"><strong>Payment Amount:</strong></div>
                                <div class="col-md-6">{{ $payment->amount }} JOD</div>
                            </div>
                        </div>
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6"><strong>Collected On:</strong></div>
                                <div class="col-md-6">{{ $payment->created_at }}</div>
                            </div>
                        </div>
                        @if ($payment->isCollected())
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Received On:</strong></div>
                                    <div class="col-md-6">{{ $payment->recieved_on }}</div>
                                </div>
                            </div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Received by:</strong></div>
                                    <div class="col-md-6">{{ $payment->receiverFullName() }}</div>
                                </div>
                            </div>
                        @endif
                        <hr class="noMargin lightGrayTopBorder">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6"><strong>Payment Method: </strong></div>
                                <div class="col-md-6">{{ $payment->notes }}</div>
                            </div>
                        </div>
                        @if ($payment->additional_notes)
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">{{ $payment->additional_notes }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @if (! $payment->isCollected())
                            <a href="{{ route('receive-payment', $payment->id) }}">
                                <button type="button" class="btn btn-danger">Receive</button>
                            </a>
                        @endif
                    </div>
                    <small style="text-align:center;font-size: 60%;color: gray;">PAYMENT ID : {{ $payment->id }}</small>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Delivery modals --}}
    @foreach ($DeliveriesToday as $case)
        <div class="modal fade" id="updateDeliveryDate{{ $case->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelform" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('edit-delivery-date') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $case->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Delivery Time</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Doctor Name </strong></div>
                                    <div class="col-md-6">{{ $case->client->name }}</div>
                                </div>
                            </div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Patient Name:</strong></div>
                                    <div class="col-md-6">{{ $case->patient_name }}</div>
                                </div>
                            </div>
                            @php
                                $time = date('Y-m-d g:i a', strtotime($case->initial_delivery_date));
                            @endphp
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Current Delivery Time:</strong></div>
                                    <div class="col-md-6">{{ $time }}</div>
                                </div>
                            </div>
                            <hr class="noMargin lightGrayTopBorder">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6"><strong>Change To:</strong></div>
                                    <div class="col-md-6">
                                        <input class="form-control SDTP" name="delivery_date" type="text" value="{{ $time }}" required readonly />
                                    </div>
                                </div>
                            </div>
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
        const edPrimary = '#4361ee';
        const edPrimarySoft = 'rgba(67,97,238,0.25)';
        const edSecondary = '#3f37c9';

        $(document).ready(function() {
            initComp7DaysChart();
            initPerformanceChart();
        });

        function initComp7DaysChart(){
            var completedChartData = {
                "Cases": ['{!! implode("','",$compCasesCount7Days) !!}'],
                "Units": ['{!! implode("','",$compUnitsCount7Days) !!}']
            };
            var labels = ['{!! implode("','", $last7DaysLabels) !!}'];

            var ctx = document.getElementById("completedChart").getContext('2d');

            const createConfig = (data, label) => ({
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        backgroundColor: edPrimarySoft,
                        borderColor: edPrimary,
                        borderWidth: 1,
                        hoverBackgroundColor: edPrimary,
                        data: data
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: {
                        backgroundColor: '#f5f5f5',
                        titleFontColor: '#333',
                        bodyFontColor: '#666',
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontColor: '#666'
                            },
                            gridLines: { color: 'rgba(17,21,30,0.08)' }
                        }],
                        xAxes: [{
                            ticks: { fontColor: '#666' },
                            gridLines: { display: false }
                        }]
                    }
                }
            });

            var completedChart = new Chart(ctx, createConfig(completedChartData["Units"], "Units"));

            $("#completedChartCases").click(function() {
                completedChart.destroy();
                completedChart = new Chart(ctx, createConfig(completedChartData["Units"], "Units"));
            });
            $("#completedChartUnits").click(function() {
                completedChart.destroy();
                completedChart = new Chart(ctx, createConfig(completedChartData["Cases"], "Cases"));
            });
        }

        function initPerformanceChart(){
            var chart_labels = ['{!! implode("', '", $last30DaysLabels) !!}'] ;

            var performanceChartData = {
                "Cases": ['{!! implode("','",$compCasesCount30Days) !!}'],
                "Units": ['{!! implode("','",$compUnitsCount30Days) !!}'],
                "Income": ['{!! implode("','",$collectionsInLast30Days) !!}'],
                "Sales": ['{!! implode("','",$sales30Days) !!}']
            };

            var ctx = document.getElementById("chartBig1").getContext('2d');

            var gradientStroke = ctx.createLinearGradient(0, 230, 0, 50);
            gradientStroke.addColorStop(1, 'rgba(67,97,238,0.25)');
            gradientStroke.addColorStop(0.4, 'rgba(67,97,238,0.08)');
            gradientStroke.addColorStop(0, 'rgba(67,97,238,0)');

            const buildConfig = (data, label) => ({
                type: 'line',
                data: {
                    labels: chart_labels,
                    datasets: [{
                        label: label,
                        fill: true,
                        backgroundColor: gradientStroke,
                        borderColor: edPrimary,
                        borderWidth: 2,
                        pointBackgroundColor: edSecondary,
                        pointBorderColor: 'rgba(255,255,255,0)',
                        pointRadius: 5,
                        data: data
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: {
                        backgroundColor: '#f5f5f5',
                        titleFontColor: '#333',
                        bodyFontColor: '#666',
                        bodySpacing: 4,
                        xPadding: 12,
                        mode: "nearest",
                        intersect: 0,
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return  tooltipItems.yLabel + ' ' + data.datasets[tooltipItems.datasetIndex].label;
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                padding: 20,
                                fontColor: "#666",
                            },
                            gridLines: { color: 'rgba(17,21,30,0.08)' }
                        }],
                        xAxes: [{
                            ticks: {
                                padding: 20,
                                fontColor: "#666",
                                fontStyle: 'bold'
                            },
                            gridLines: { display: false }
                        }]
                    }
                }
            });

            var myChartData = new Chart(ctx, buildConfig(performanceChartData["Units"], "Units"));
            $("#0").click(function() {
                myChartData.destroy();
                myChartData = new Chart(ctx, buildConfig(performanceChartData["Units"], "Units"));
            });
            $("#1").click(function() {
                myChartData.destroy();
                myChartData = new Chart(ctx, buildConfig(performanceChartData["Cases"], "Cases"));
            });
            $("#2").click(function() {
                myChartData.destroy();
                myChartData = new Chart(ctx, buildConfig(performanceChartData["Income"], "JOD Collected Payments"));
            });
            $("#3").click(function() {
                myChartData.destroy();
                myChartData = new Chart(ctx, buildConfig(performanceChartData["Sales"], "JOD"));
            });
        }
    </script>
@endpush
