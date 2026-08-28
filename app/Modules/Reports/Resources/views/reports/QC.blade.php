@extends('layouts.app' ,[ 'pageSlug' => 'Quality Control Report'])

@section('content')
    <link href="{{asset('assets/css/picker.css')}}" rel="stylesheet">
    <!-- styles to carry on while printing -->
    <div id="style">
        <style>
            @media print {
            footer{display:none}
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
                background-color: #F1F7ED;
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

    @include('reports.partials.report-ui')

    <div class="solent-report-page">
    @include('reports.partials.report-header', [
        'title' => 'QC Summary',
        'description' => 'Track quality incidents, affected cases, units, failure types, and causes.',
        'icon' => 'fas fa-shield-alt',
    ])

    <form class="kt-form filtersPanel KorvexPanel report-filters" method="GET" action="{{route('QC-report')}}">
        @include('reports.partials.report-section-heading', [
            'title' => 'Filters',
            'description' => 'Choose the period, causes, doctors, and failure types.',
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
                <div class="dropdown">
                    <label class="report-filter-label"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>Failure Cause:</span></label>
                    <select class="selectpicker clearOnAll" multiple name="causesInput[]"
                                id="causesInput" data-live-search="true" title="All" data-hide-disabled="true">


                                @if ($allCausesSelected)
                                    <option value="all" selected >All</option>
                                    @foreach($allFailureCauses as $d)
                                        <option value="{{$d->id}}" >{{$d->text}}</option>
                                    @endforeach

                                @else
                                    @php $idsOfSelectedCauses = $selectedFailureCauses->pluck('id')->toArray(); @endphp
                                    <option value="all">All</option>
                                    @foreach($allFailureCauses as $d)
                                        <option value="{{$d->id}}" {{ in_array($d->id ,$idsOfSelectedCauses) ? 'selected' : ''}}>{{$d->text}}</option>
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
            <div class="report-filter">
                <div class="dropdown">
                    <label class="report-filter-label"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i><span>Type of failure:</span></label>
                    <select class="selectpicker clearOnAll" multiple name="failureTypeInput[]"
                                id="failureTypeInput" data-live-search="true" title="All" data-hide-disabled="true">

                                <option value="all" {{in_array("all" , $typesSelected) ? 'selected' : ''}}>All</option>
                                <option value="0" {{in_array(0 , $typesSelected) ? 'selected' : ''}} >Reject</option>
                                <option value="1" {{in_array(1 , $typesSelected) ? 'selected' : ''}} >Repeat</option>
                                <option value="2" {{in_array(2 , $typesSelected) ? 'selected' : ''}} >Modification</option>
                                <option value="3" {{in_array(3 , $typesSelected) ? 'selected' : ''}} >Redo</option>

                    </select>
                </div>
            </div>
        </div>
        <div class="report-filter-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter" aria-hidden="true"></i><span>Apply Filters</span></button>
            <button type="button" class="btn btn-secondary printBtn"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
        </div>
    </form>


    @php
        $failuresDesc = [0 => 'Rejection', 1 => 'Repeat', 2 => 'Modification', 3 => 'Redo'];
        $counterTest = $failureLogs->sum(function ($failLog) {
            return $failLog->case
                ? $failLog->case->failedUnitsAmount($failLog->failure_type)
                : 0;
        });
    @endphp

    <div class="KorvexPanel report-results" data-report-consolidated="true">
        @include('reports.partials.report-section-heading', [
            'title' => 'Quality incidents',
            'description' => 'Detailed failure records matching the selected filters.',
            'icon' => 'fas fa-clipboard-check',
            'count' => $failureLogs->count(),
            'countLabel' => 'incidents',
        ])
        @include('reports.partials.report-range')
        <div class="report-summary-grid">
            <article class="report-summary-card">
                <span class="report-summary-icon" aria-hidden="true"><i class="fas fa-clipboard-list"></i></span>
                <div><span class="report-summary-label">Incidents</span><strong class="report-summary-value">{{ number_format($failureLogs->count()) }}</strong></div>
            </article>
            <article class="report-summary-card">
                <span class="report-summary-icon" aria-hidden="true"><i class="fas fa-folder-open"></i></span>
                <div><span class="report-summary-label">Affected cases</span><strong class="report-summary-value">{{ number_format($amountOfCases) }}</strong></div>
            </article>
            <article class="report-summary-card">
                <span class="report-summary-icon" aria-hidden="true"><i class="fas fa-tooth"></i></span>
                <div><span class="report-summary-label">Affected units</span><strong id="numOfUnitsFailed" class="report-summary-value">{{ number_format($counterTest) }}</strong></div>
            </article>
        </div>
        <div class="report-table-scroll">
                        <table border="1" class="xl649957 printable sunriseTable report-table" style="border-collapse:collapse;">
                            <thead>
                                <tr class="tableHeaderRow">
                                    <th>Dr Name</th>
                                    <th>Patient</th>
                                    <th>Status</th>
                                    <th>Causes</th>
                                    <th># of Units</th>
                                    <th>Date Failure Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($failureLogs as $failLog)
                                    <tr class="dataRow">
                                        <td class="doctorName">{{ optional(optional($failLog->case)->client)->name ?? 'Case Not found' }}</td>
                                        <td>{{ optional($failLog->case)->patient_name ?? 'Case Not found' }}</td>
                                        <td>{{ $failuresDesc[$failLog->failure_type] ?? 'Unknown' }}</td>
                                        <td>{{ optional($failLog->causeObject)->text ?? '-' }}</td>
                                        <td>{{ $failLog->case ? $failLog->case->failedUnitsAmount($failLog->failure_type) : '-' }}</td>
                                        <td>{{ optional($failLog->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
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
        $(".toggle-group > *").addClass("unstyled");
        $(".toggle").addClass("unstyled");
        $(".toggle-group > label").addClass("toggleInnerBtns");
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
                setDate: {!! '"'. $dateRangeValue . '"' !!}

            }
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
        newWin.document.write('<h3 style="float:left">Quality Control Report</h3> ' +
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
