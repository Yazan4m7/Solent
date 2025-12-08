@extends('layouts.app', ['pageSlug' => 'Home'])

@section('content')
    @php
        $paymentsCount = collect($paymentsReceivedToday ?? [])->count();
        $deliveriesTodayCount = collect($DeliveriesToday ?? [])->count();
    @endphp
    <style>
        .dashboard-page {
            position: relative;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .grid-span-2 {
            grid-column: span 2;
        }
        .grid-card {
            border-radius: 1.25rem;
            border: none;
            box-shadow: 0 15px 40px rgba(23, 43, 77, 0.08);
            overflow: hidden;
        }
        .grid-card .card-header {
            background: transparent;
        }
        .card-eyebrow {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #7d8a99;
        }
        .chart-area {
            min-height: 260px;
        }
        .chart-toggle .btn {
            border-radius: 999px;
            font-weight: 600;
            border: 1px solid rgba(31, 42, 61, 0.15);
            color: var(--rich-black);
            margin: 0 .25rem;
            transition: all .2s ease;
        }
        .chart-toggle .btn.active,
        .chart-toggle .btn:hover {
            background-color: var(--main-blue);
            border-color: var(--main-blue);
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 124, 99, 0.2);
        }
        .barsBtns,
        .performanceBtns {
            background-color: rgba(15, 124, 99, 0.08);
        }
        .barsBtns.active,
        .performanceBtns.active {
            background: var(--main-blue);
            color: #fff;
        }
        .table-modern table {
            margin-bottom: 0;
        }
        .table-modern thead th {
            border-top: none;
            font-size: .75rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #7d8a99;
        }
        .table-modern tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        @media (max-width: 575px) {
            .chart-area {
                min-height: 220px;
            }
        }
    </style>
    <div class="dashboard-page container-fluid px-0">
        <div class="dashboard-grid">
            <div class="grid-card card card-chart shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <p class="card-eyebrow mb-1">Production</p>
                            <h4 class="card-title mb-0">Completed in 7 Days</h4>
                        </div>
                        <div class="btn-group btn-group-toggle chart-toggle" data-toggle="buttons">
                            <label class="btn btn-sm btn-primary btn-simple barsBtns active" id="completedChartCases">
                                <input type="radio" name="options" checked>
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Units</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple barsBtns" id="completedChartUnits">
                                <input type="radio" class="d-none d-sm-none" name="options">
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Cases</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-solid fa-box"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="completedChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="grid-card card card-chart shadow-sm">
                <div class="card-header border-0 pb-0">
                    <p class="card-eyebrow mb-1">Capacity</p>
                    <h4 class="card-title mb-0">Cases/Units Currently In Work</h4>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <div id="chartContainer" style="height: 100%; width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="grid-card card card-chart shadow-sm grid-span-2">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <p class="card-eyebrow mb-1">Performance</p>
                            <h4 class="card-title mb-0">Monthly Performance</h4>
                        </div>
                        <div class="btn-group btn-group-toggle chart-toggle" data-toggle="buttons">
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns active" id="0">
                                <input type="radio" name="options" checked>
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Units</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="1">
                                <input type="radio" class="d-none d-sm-none" name="options">
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Cases</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-solid fa-box"></i>
                                </span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="3">
                                <input type="radio" class="d-none" name="options">
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Sales</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-solid fa-money-bill-trend-up"></i>
                                </span>
                            </label>
                            <label class="btn btn-sm btn-primary btn-simple performanceBtns" id="2">
                                <input type="radio" class="d-none" name="options">
                                <span class="d-none d-sm-block d-md-block d-lg-block d-xl-block">Payments</span>
                                <span class="d-inline d-sm-none">
                                    <i class="fa-regular fa-money-bill-1"></i>
                                </span>
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
            <div class="grid-card card shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <p class="card-eyebrow mb-1">Finance</p>
                            <h4 class="card-title mb-0">Payments Collected Today</h4>
                        </div>
                        <span class="badge badge-light text-primary font-weight-bold">{{ $paymentsCount }} logs</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-modern">
                        <table id="datatable" class="datatable hover compact stripe sunriseTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Payment</th>
                                    <th class="text-center">Collector</th>
                                    <th class="text-center">Time Collected</th>
                                    <th>Received by</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentsReceivedToday as $payment)
                                    <tr class="clickable" data-toggle="modal" data-target="#receivePaymentModal{{ $payment->id }}">
                                        <td>{{ $payment->client->name }}</td>
                                        <td>{{ $payment->amount }} JOD</td>
                                        <td class="text-center">{{ $payment->collectorUserRecord->name_initials }}</td>
                                        <td class="text-center">
                                            {{ date('g:i a', strtotime(substr(str_replace('T', ' ', $payment->recieved_on), 0, -3))) }}
                                        </td>
                                        <td>
                                            @if ($payment->receivedBy)
                                                <span style="color:green">{{ $payment->receivedBy->name_initials }}</span>
                                            @else
                                                <span style="color:red">NONE</span>
                                            @endif
                                        </td>
                                    </tr>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="grid-card card shadow-sm">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <p class="card-eyebrow mb-1">Logistics</p>
                            <h4 class="card-title mb-0">Deliveries Today</h4>
                        </div>
                        <span class="badge badge-light text-primary font-weight-bold">{{ $deliveriesTodayCount }} scheduled</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-modern">
                        <table class="datatable compact hover stripe sunriseTable" id="datatable2">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Patient name</th>
                                    <th class="text-center">Delivery time</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($DeliveriesToday as $case)
                                    <tr class="clickable" data-toggle="modal" data-target="#updateDeliveryDate{{ $case->id }}">
                                        <td>{{ $case->client->name }}</td>
                                        <td>{{ $case->patient_name }}</td>
                                        <td class="text-center">
                                            {{ date('g:i a', strtotime(str_replace('T', ' ', $case->initial_delivery_date))) }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $case->status();
                                                $active = true;
                                                if (str_contains($status, 'Waiting')) {
                                                    $active = false;
                                                }
                                            @endphp
                                            @if ($active)
                                                <span class="badge badge-primary" style="width:auto; margin:auto; text-align:center">{{ $status }}</span>
                                            @else
                                                <span class="badge badge-danger" style="width:auto; margin:auto; text-align:center">{{ $case->status() }}</span>
                                            @endif
                                        </td>
                                    </tr>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')

<script src="{{ asset('assets') }}/js/canvasjs.min.js"></script>
<script src="{{ asset('white') }}/js/plugins/chartjs.min.js"></script>


    <script>
        $(document).ready(function() {
            initDoughnutChart();
            initComp7DaysChart();
          initPerformanceChart();
            $('.datatable').DataTable({
                "pageLength": 50,
                "searching": false,
                "lengthChange": false,
                "ordering": false,
                "paging":false}
            );
        });
        function initComp7DaysChart(){

            var completedChartData = {
                "Cases": ['{!! implode("','",$compCasesCount7Days) !!}'],
                "Units": ['{!! implode("','",$compUnitsCount7Days) !!}']};

            var barChartConfiguration = {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: '#f5f5f5',
                    titleFontColor: '#333',
                    bodyFontColor: '#666',
                    bodySpacing: 4,
                    xPadding: 12,
                    mode: "nearest",
                    intersect: 0,
                    position: "nearest"
                },
                responsive: true,
                scales: {
                    yAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(29,140,248,0.1)',
                            zeroLineColor: "transparent",
                        },
                        ticks: {
                            suggestedMin: 20,
                            suggestedMax: 0,
                            padding: 20,
                            fontColor: "#9e9e9e"
                        }
                    }],

                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(29,140,248,0.1)',
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 20,
                            fontColor: "#9e9e9e"
                        }
                    }]
                }
            };

            var ctx = document.getElementById("completedChart").getContext("2d");

            var gradientStroke = ctx.createLinearGradient(0, 230, 0, 50);

            gradientStroke.addColorStop(1, 'rgba(29,140,248,0.2)');
            gradientStroke.addColorStop(0.4, 'rgba(29,140,248,0.0)');
            gradientStroke.addColorStop(0, 'rgba(29,140,248,0)'); //blue colors

            var options1 = {
                type: 'bar',
                responsive: true,
                legend: {
                    display: false
                },
                data: {
                    labels: ['{!! implode("','",$last7DaysLabels) !!}'],
                    datasets: [{
                        label: "Completed Units",
                        fill: true,
                        backgroundColor: gradientStroke,
                        hoverBackgroundColor: gradientStroke,
                        borderColor: '#1f8ef1',
                        borderWidth: 2,
                        borderDash: [],
                        borderDashOffset: 0.0,
                        data: completedChartData['Units']
                    }]
                },
                options: barChartConfiguration
            };
             var options2 = {
                 type: 'bar',
                 responsive: true,
                 legend: {
                     display: false
                 },
                 data: {
                     labels: ['{!! implode("','",$last7DaysLabels) !!}'],
                     datasets: [{
                         label: "Completed Cases",
                         fill: true,
                         backgroundColor: gradientStroke,
                         hoverBackgroundColor: gradientStroke,
                         borderColor: '#1f8ef1',
                         borderWidth: 2,
                         borderDash: [],
                         borderDashOffset: 0.0,
                         data: completedChartData['Cases']
                     }]
                 },
                 options: barChartConfiguration
             };
            var completedChart = new Chart(ctx,options1 );

             $("#completedChartCases").click(function() {

                 completedChart.destroy();
                 completedChart = new Chart(ctx,options1 );
             });
             $("#completedChartUnits").click(function() {

                 completedChart.destroy();
                 completedChart = new Chart(ctx,options2 );
             });
        }
        function initDoughnutChart(){
            var doughnetChartData = {
                "Units": [
                    { y: {!! $CompletedJobsToday !!}, name: "Completed" },
                    { y: {!! $ActiveJobsToday !!}, name: "Active" },
                    { y: {!! $waitingJobsToday !!}, name: "Waiting" }

                ]};
            CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                    "#37b44a",
                    "#007bff",
                    "#dc3545"
                ]);
            var options = {

                exportFileName: "Active/Waiting/Completed Chart",
                exportEnabled: false,
                animationEnabled: true,
                animationDuration: 800,
                colorSet: "greenShades",
//                title:{
//                    text: "Monthly Expense"
//                },
                legend:{
                    cursor: "pointer",
                    itemclick: explodePie
                },
                data: [{
                    type: "doughnut",
                    innerRadius: 50,
                    indexLabelTextAlign: "center",
                    //indexLabelWrap: true,

                    indexLabelPlacement: "outside",
                    indexLabelFontColor: "black",
                    showInLegend: false,
                    toolTipContent: "<b>{name}</b>: {y} (#percent%)",
                    indexLabel: "{name}",
                    dataPoints: doughnetChartData["Units"]
                }]

            };

            var compWaitingChart = new CanvasJS.Chart("chartContainer",
                options);

            compWaitingChart.render();




            function explodePie (e) {
                if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
                } else {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
                }
                e.chart.render();
            }

        }
        function initPerformanceChart(){

            gradientChartOptionsConfigurationWithTooltipPurple = {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },

                tooltips: {
                    backgroundColor: '#f5f5f5',
                    titleFontColor: '#333',
                    bodyFontColor: '#666',
                    bodySpacing: 4,
                    xPadding: 12,
                    mode: "nearest",
                    intersect: 0,
                    position: "nearest",
                    callbacks: {
                        label: function(tooltipItems, data) {
                            return  tooltipItems.yLabel + ' ' + data.datasets[tooltipItems.datasetIndex].label;
                        }
                    }
                },
                responsive: true,
                scales: {
                    yAxes: [
                        {
                        barPercentage: 1.6,
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(29,140,248,0.0)',
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            suggestedMin: 20,
                            suggestedMax: 0,
                            padding: 20,
                            fontColor: "#9a9a9a",

                        }
                    }],

                    xAxes: [{
                        barPercentage: 1.6,
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(225,78,202,0.1)',
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 20,
                            fontColor: "#9a9a9a",
                            fontStyle: 'bold'
                        }
                    }]
                }
            };
            var chart_labels = ['{!! implode("', '", $last30DaysLabels) !!}'] ;

            var performanceChartData = {
                "Cases": ['{!! implode("','",$compCasesCount30Days) !!}'],
                "Units": ['{!! implode("','",$compUnitsCount30Days) !!}'],
                "Income": ['{!! implode("','",$collectionsInLast30Days) !!}'],
                "Sales": ['{!! implode("','",$sales30Days) !!}']};


            var ctx = document.getElementById("chartBig1").getContext('2d');

            var gradientStroke = ctx.createLinearGradient(0, 230, 0, 50);

            gradientStroke.addColorStop(1, 'rgba(72,72,176,0.1)');
            gradientStroke.addColorStop(0.4, 'rgba(72,72,176,0.0)');
            gradientStroke.addColorStop(0, 'rgba(55, 180, 74,0)'); //purple colors
            var config = {
                type: 'line',
                data: {
                    labels: chart_labels,
                    datasets: [{
                        label: "Units",

                        fill: true,
                        backgroundColor: gradientStroke,
                        borderColor: '#31b72f',
                        borderWidth: 2,
                        borderDash: [],
                        borderDashOffset: 15.0,
                        pointBackgroundColor: '#226746',
                        pointBorderColor: 'rgba(255,255,255,0)',
 //                       pointHoverBackgroundColor: '#d346b1',
                        pointBorderWidth: 20,
 //                       pointHoverRadius: 4,
//                        pointHoverBorderWidth: 15,
                        pointRadius: 5,
                        data: performanceChartData["Units"]
                    }]
                },
                options: gradientChartOptionsConfigurationWithTooltipPurple
            };
            var myChartData = new Chart(ctx, config);
            $("#0").click(function() {
                var data = myChartData.config.data;
                data.datasets[0].data = performanceChartData["Units"];
                data.datasets[0].label = "Units";

                myChartData.update();
            });
            $("#1").click(function() {
                var data = myChartData.config.data;
                data.datasets[0].data = performanceChartData["Cases"];
                data.datasets[0].label = "Cases";

                myChartData.update();
            });

            $("#2").click(function() {
                var data = myChartData.config.data;
                data.datasets[0].data = performanceChartData["Income"];
                data.datasets[0].label = "JOD Collected Payments";

                myChartData.update();
            });
            $("#3").click(function() {
                var data = myChartData.config.data;
                data.datasets[0].data = performanceChartData["Sales"];
                data.datasets[0].label = "JOD";
                myChartData.update();
            });

        }
    </script>
@endpush
