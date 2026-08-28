@extends('layouts.app' ,[ 'pageSlug' => 'Implants Report'])

@section('content')
    <link href="{{asset('assets/css/picker.css')}}" rel="stylesheet">
    <!-- styles to carry on while printing -->
    <div id="style">
        <style>
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

            table {
                border-collapse: collapse;
                border-spacing: 0;
                width: 100%;
                border: 1px solid #ddd;
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
            td{
                border:1px solid #ddd;
                border-top: none;
                border-bottom: none;
            }
            .bottom-Border {
                border-bottom:  1px solid #ddd;
            }
            .tableHeaderRow{
                background-color: #f1f7ed;
                font-weight: 700;
            }
            .subHeaderRow{
                font-weight: 500;
                text-align:center;
                background-color: #8e8e8e;
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
        </style>
    </div>

    @include('reports.partials.report-ui')

    <form class="kt-form filtersPanel KorvexPanel report-filters" method="GET" action="{{route('implants-report')}}">

        <div class="report-filter-grid">
            <div class="report-filter">
                <div class="kt-subheader__search">
                    <label class="report-filter-label"><i class="far fa-calendar" aria-hidden="true"></i><span>Date Range:</span></label>
                    <input class="form-control dateRange" name="dateRange" autocomplete="off" readonly
                           value="{{$dateRangeValue ?? "Select Period"}}" style="cursor: pointer;">
                </div>
            </div>
            <div class="report-filter">
                <div class="dropdown">
                    <label class="report-filter-label"><i class="fas fa-plus-circle" aria-hidden="true"></i><span>Implants:</span></label>
                    <select class="selectpicker clearOnAll" multiple name="implantsInput[]"
                            id="implantsInput" data-live-search="true" title="All" data-hide-disabled="true">

                            @if ($allImplantsSelected)
                                <option value="all" selected >All</option>
                                @foreach($implants as $d)
                                    <option value="{{$d->id}}" >{{$d->name}}</option>
                                @endforeach

                            @else
                                @php $idsOfImplantsSelected = $selectedImplants->pluck('id')->toArray(); @endphp
                                <option value="all">All</option>
                                @foreach($implants as $d)
                                    <option value="{{$d->id}}" {{(in_array($d->id ,$idsOfImplantsSelected)) ? 'selected' : ''}}>{{$d->name}}</option>
                                @endforeach
                            @endif

                    </select>
                </div>
            </div>
            <div class="report-filter">
                <div class="dropdown">
                    <label class="report-filter-label"><i class="fas fa-cog" aria-hidden="true"></i><span>Abutment:</span></label>
                    <select class="selectpicker clearOnAll" multiple name="abutmentsInput[]"
                            id="abutmentsInput" data-live-search="true" title="All" data-hide-disabled="true">

                            @if ($allAbutmentsSelected)
                                <option value="all" selected >All</option>
                                @foreach($abutments as $d)
                                    <option value="{{$d->id}}" >{{$d->name}}</option>
                                @endforeach

                            @else
                                @php $idsOfAbutmentsSelected = $selectedAbutments->pluck('id')->toArray(); @endphp
                                <option value="all">All</option>
                                @foreach($abutments as $d)
                                    <option value="{{$d->id}}" {{(in_array($d->id ,$idsOfAbutmentsSelected)) ? 'selected' : ''}}>{{$d->name}}</option>
                                @endforeach
                            @endif

                    </select>
                </div>
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
            <div class="report-filter report-toggle">
                <label class="report-filter-label" for="implantsPerToggle"><i class="fas fa-chart-bar" aria-hidden="true"></i><span>Measure by</span></label>
                <label class="report-switch" for="implantsPerToggle">
                    <input name="perToggle" {{ $perUnitTrigger ? 'checked' : '' }}
                        id="implantsPerToggle" type="checkbox">
                    <span class="report-switch__track">
                        <span class="report-switch__option report-switch__option--unchecked">Units</span>
                        <span class="report-switch__option report-switch__option--checked">Cases</span>
                    </span>
                </label>
            </div>
        </div>
        <div class="report-filter-actions">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary printBtn">Print</button>
        </div>
    </form>




    <div class="KorvexPanel report-results" data-report-consolidated="true">
        @include('reports.partials.report-range')
        @php
            $filteredClients = in_array('all', $selectedClients, true)
                ? $clients
                : $clients->whereIn('id', $selectedClients);
            $implantIds = $selectedImplants->pluck('id')->all();
            $reportTotals = array_fill_keys($selectedAbutments->pluck('id')->all(), 0);
        @endphp
        <div class="report-table-scroll">
            <table border="1" class="xl649957 printable sunriseTable report-table" style="border-collapse:collapse;">
                <thead>
                    <tr class="tableHeaderRow">
                        <th>Dr Name</th>
                        @foreach($selectedAbutments as $abutment)
                            <th>{{ $abutment->name }}</th>
                        @endforeach
                        <th class="totalsCol">All</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredClients as $client)
                        @php $doctorTotal = 0; @endphp
                        <tr class="dataRow">
                            <td class="doctorName">{{ $client->name }}</td>
                            @foreach($selectedAbutments as $abutment)
                                @php
                                    $currentTotal = collect($selectedMonths)->sum(function ($month) use ($client, $abutment, $implantIds, $perUnitTrigger) {
                                        return $perUnitTrigger
                                            ? $client->numOfCasesBy_abutment_implants($abutment->id, $implantIds, $month)
                                            : $client->numOfUnitsBy_abutment_implants($abutment->id, $implantIds, $month);
                                    });
                                    $doctorTotal += $currentTotal;
                                    $reportTotals[$abutment->id] += $currentTotal;
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
                        @foreach($selectedAbutments as $abutment)
                            <th>{{ $reportTotals[$abutment->id] }}</th>
                        @endforeach
                        <th>{{ array_sum($reportTotals) }}</th>
                    </tr>
                </tfoot>
            </table>
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
                RTL: false,
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
        newWin.document.write(' <h3 style="float:left">Clients Consumptions Report <span style="color:#2b2b2b"> - by Abutments & Implants, per '+'{{$perUnitTrigger ? "Case" : "Unit"}}'+'</span></h3> ' +
            ' <h4 style="float:right"> Date Printed :{!! date("d") !!} - {!! date("M") !!} - {!! date("Y") !!} </h4>');
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
