@extends('layouts.app' ,[ 'pageSlug' => 'Number of units Report'])

@section('content')
    <link href="{{asset('assets/css/picker.css')}}" rel="stylesheet">


   <!-- styles to carry on while printing -->
<div id="style">
    <style>
        @media print {
        .KorvexPanel {
            padding-bottom:10px;
            padding-top:10px;
            margin-bottom:15px;
            margin-top:15px;
            background-color: white;
        }
        .row {
            background-color: transparent;
        }
            .no-left-top-border {
            border-top-color: transparent;
            border-top-style: solid;
            border-top-width: 1px;

            border-left-color: transparent;
            border-left-style: solid;
            border-left-width: 1px;
        }
        td, tr {
            width: fit-content;
            height: fit-content;
        }

        th, td {
            /*text-align: left;*/
            padding: 0px;
        }

        .dataRow:nth-child(even) {
            background-color: #d0d0d0
        }
        table, th, td {
            border-collapse: collapse;
            padding:4px;
        }


        .tableHeaderRow{
            background-color: #f1f7ed;
            font-weight: 700;
        }
        .subHeaderRow{
            font-weight: 500;
            text-align:center;
            /*background-color: #8e8e8e;*/
            color:white;
            padding-top:5px;
            padding-botton:5px;
        }
        .totalsCol{
            color:black;background-color:#f1f7ed;border-bottom: solid 1px #ddd; padding-left:15px;padding-right:15px;text-align: center;
        }
        .totalsRow{
            color: #404040;
            text-align: left;
            border-top: 1px solid #ddd;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .doctorName{
            font-weight: bold;
        }
        }
    </style>
</div>
    <!-- styles for the view only -->
    <style>
      table{
          margin-bottom:5vh;}
    </style>
    @include('reports.partials.report-ui')

    <div class="solent-report-page">
    @include('reports.partials.report-header', [
        'title' => 'Units Summary',
        'description' => 'Compare completed units by doctor and material for the selected period.',
        'icon' => 'fas fa-layer-group',
    ])

    <form class="kt-form filtersPanel KorvexPanel report-filters" method="GET" action="{{route('num-of-units-report')}}">
        @include('reports.partials.report-section-heading', [
            'title' => 'Filters',
            'description' => 'Choose the period, materials, and doctors to include.',
            'icon' => 'fas fa-sliders-h',
        ])
        <div class="report-filter-grid">
            <div class="report-filter">
                <div class="kt-subheader__search">
                    <label class="report-filter-label"><i class="far fa-calendar" aria-hidden="true"></i><span>Date Range:</span></label>
                    <input class="form-control dateRange" name="dateRange" autocomplete="off" readonly
                           value="{{$dateRangeValue ?? "Select Period"}}">
                </div>
            </div>
            <div class="report-filter">
                @if(isset($materials))
                    <div class="dropdown">
                        <label class="report-filter-label"><i class="fas fa-cubes" aria-hidden="true"></i><span>Material:</span></label>
                        <select class="selectpicker clearOnAll" multiple name="material[]"
                                id="material" data-live-search="true" title="All" data-hide-disabled="true">

                                <option value="all" {{(isset($selectedMaterials) && $selectedMaterials== 'all') ? 'selected' : ''}}>
                                    All
                                </option>
                                @foreach($materials as $d)
                                    <option value="{{$d->id}}" {{(isset($selectedMaterials) && in_array($d->id ,$selectedMaterials)) ? 'selected' : ''}}>{{$d->name}}</option>
                                @endforeach

                        </select>
                    </div>
                @endif
            </div>
            <div class="report-filter">
                @if(isset($clients))
                    <div class="dropdown">
                        <label class="report-filter-label"><i class="fas fa-user-md" aria-hidden="true"></i><span>Doctor:</span></label>
                        <select class="selectpicker clearOnAll" multiple name="doctor[]"
                                id="doctor" data-live-search="true" title="All" data-hide-disabled="true">

                                <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>
                                    All
                                </option>
                                @foreach($clients as $d)
                                    <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                                @endforeach

                        </select>
                    </div>
                @endif
            </div>
        </div>
        <div class="report-filter-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter" aria-hidden="true"></i><span>Apply Filters</span></button>
            <button type="button" class="btn btn-secondary printBtn"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
        </div>
    </form>

    <div class="KorvexPanel report-results" data-report-consolidated="true">
        @php
            $filteredClients = in_array('all', $selectedClients, true)
                ? $clients
                : $clients->whereIn('id', $selectedClients);
            $reportMaterials = $materials->whereIn('id', $selectedMaterials)->values();
            $reportTotals = array_fill_keys($reportMaterials->pluck('id')->all(), 0);
        @endphp
        @include('reports.partials.report-section-heading', [
            'title' => 'Units by doctor',
            'description' => 'Material totals for every doctor in the selected range.',
            'icon' => 'fas fa-table',
            'count' => $filteredClients->count(),
            'countLabel' => 'doctors',
        ])
        @include('reports.partials.report-range')
        <div class="report-table-scroll">
            <table border="1" class="xl649957 printable sunriseTable report-table" style="border-collapse:collapse;">
                <thead>
                    <tr class="tableHeaderRow">
                        <th>Dr Name</th>
                        @foreach($reportMaterials as $reportMaterial)
                            <th>{{ $reportMaterial->name }}</th>
                        @endforeach
                        <th class="totalsCol">All</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredClients as $client)
                        @php $doctorTotal = 0; @endphp
                        <tr class="dataRow">
                            <td class="doctorName">{{ $client->name }}</td>
                            @foreach($reportMaterials as $reportMaterial)
                                @php
                                    $currentTotal = collect($selectedMonths)->sum(function ($month) use ($client, $reportMaterial) {
                                        return $client->numOfUnitsByMaterial($reportMaterial->id, $month);
                                    });
                                    $doctorTotal += $currentTotal;
                                    $reportTotals[$reportMaterial->id] += $currentTotal;
                                @endphp
                                <td>{{ $currentTotal }}</td>
                            @endforeach
                            <td class="totalsCol"><b>{{ $doctorTotal }}</b></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Totals</th>
                        @foreach($reportMaterials as $reportMaterial)
                            <th>{{ $reportTotals[$reportMaterial->id] }}</th>
                        @endforeach
                        <th>{{ array_sum($reportTotals) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    </div>

@endsection

@push('js')
<script src="{{asset('assets/js/tether.min.js')}}"></script>
<script src="{{asset('assets/js/datePicker.js')}}"></script>

<script>
    $(document).ready(function () {


        $('.dateRange').rangePicker(
            {
                RTL: {{ trans('ui.direction') === 'rtl' ? 'true' : 'false' }},
                closeOnSelect: true,
                presets: [{
                    buttonText: 'Last Month',
                    displayText: '1 Month',
                    value: '1m'
                }, {
                    buttonText: 'Last 3 Months',
                    displayText: '3 Months',
                    value: '3m'
                }, {
                    buttonText: 'Last 6 Months',
                    displayText: '6 Months',
                    value: '6m'
                }, {
                    buttonText: 'Last 12 Months',
                    displayText: '12 Months',
                    value: '12m'
                }],
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                minDate: [10, 2021],
                maxDate: [{!! date("m") !!}, {!! date("Y") !!}],
                setDate: {!! '"'. $dateRangeValue . '"' !!}}
        )
            .on('datePicker.done', function (e, result) {
                console.log(result);
            });

    });

    function printData()
    {
//        var table = $("#table1"),
//            tableWidth = table.outerWidth(),
//            pageWidth = 600,
//            pageCount = Math.ceil(tableWidth / pageWidth),
//            printWrap = $("<div></div>").insertAfter(table),
//            i,
//            printPage;
//        for (i = 0; i < pageCount; i++) {
//            printPage = $("<div></div>").css({
//                "overflow": "hidden",
//                "width": pageWidth,
//                "page-break-before": i === 0 ? "auto" : "always"
//            }).appendTo(printWrap);
//            table.clone().removeAttr("id").appendTo(printPage).css({
//                "position": "relative",
//                "left": -i * pageWidth
//            });
//        }
//        table.hide();
//        $(this).prop("disabled", true);
        var tables = $('.printable');

        var styling=document.getElementById("style");
        newWin= window.open("");
        newWin.document.write(styling.innerHTML);
        newWin.document.write('<h3 style="float:left">Doctor Consumptions Report</h3>  <h4 style="float:right"> Date Printed :{!! date("d") !!} - {!! date("M") !!} - {!! date("Y") !!} </h4>');
        $.each(tables, function(key, value) {
            newWin.document.write(value.outerHTML);
        });
        newWin.print();
        newWin.close();
    }
    $('.printBtn').on('click',function(){
        printData();
    })

</script>
@endpush
