@extends('layouts.app' ,[ 'pageSlug' => 'Remakes Report' ])


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
        'title' => 'Remakes',
        'description' => 'Compare rejected, repeated, modified, redone, and successful work by doctor.',
        'icon' => 'fas fa-redo-alt',
    ])

    <form class="kt-form filtersPanel KorvexPanel report-filters" method="GET" action="{{route('repeats-report')}}">
        @include('reports.partials.report-section-heading', [
            'title' => 'Filters',
            'description' => 'Choose the period, outcomes, doctors, measurement, and display mode.',
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
                    <label class="report-filter-label"><i class="fas fa-redo" aria-hidden="true"></i><span>Repeat:</span></label>
                    <select class="selectpicker clearOnAll" multiple name="failureTypeInput[]"
                                id="failureTypeInput" data-live-search="true" title="All" data-hide-disabled="true">

                                @php

                                        @endphp
                                @if ($allFailureTypesSelected)
                                    <option value="all" selected >All</option>

                                    <option value="0"  >Reject</option>
                                    <option value="1">Repeat</option>
                                    <option value="2" >Modification</option>
                                    <option value="3">Redo</option>
                                    <option value="4" >Successful</option>

                                @else

                                    <option value="all">All</option>
                                    <option value="0" {{array_key_exists(0, $selectedFailureTypes) ? 'selected' : ''}} >Reject</option>
                                    <option value="1" {{array_key_exists(1, $selectedFailureTypes) ? 'selected' : ''}} >Repeat</option>
                                    <option value="2" {{array_key_exists(2, $selectedFailureTypes) ? 'selected' : ''}} >Modification</option>
                                    <option value="3" {{array_key_exists(3, $selectedFailureTypes) ? 'selected' : ''}} >Redo</option>
                                    <option value="4" {{array_key_exists(4, $selectedFailureTypes) ? 'selected' : ''}} >Successful</option>

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
                <label class="report-filter-label" for="repeatsPerToggle"><i class="fas fa-chart-bar" aria-hidden="true"></i><span>Measure by</span></label>
                <label class="report-switch" for="repeatsPerToggle">
                    <input name="perToggle" {{ $perUnitTrigger ? 'checked' : '' }}
                        id="repeatsPerToggle" type="checkbox">
                    <span class="report-switch__track">
                        <span class="report-switch__option report-switch__option--unchecked">Cases</span>
                        <span class="report-switch__option report-switch__option--checked">Units</span>
                    </span>
                </label>
            </div>
            <div class="report-filter report-toggle">
                <label class="report-filter-label" for="repeatsDisplayToggle"><i class="fas fa-percentage" aria-hidden="true"></i><span>Show as</span></label>
                <label class="report-switch" for="repeatsDisplayToggle">
                    <input name="countOrPercentageToggle" {{ $showPercentage ? 'checked' : '' }}
                        id="repeatsDisplayToggle" type="checkbox">
                    <span class="report-switch__track">
                        <span class="report-switch__option report-switch__option--unchecked">Count</span>
                        <span class="report-switch__option report-switch__option--checked">Percentage</span>
                    </span>
                </label>
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
            $reportTotals = array_fill_keys(array_keys($selectedFailureTypes), 0);
        @endphp
        @include('reports.partials.report-section-heading', [
            'title' => 'Remake breakdown',
            'description' => 'Outcome totals for each doctor in the selected range.',
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
                        @foreach($selectedFailureTypes as $failureDescription)
                            <th>{{ $failureDescription }}</th>
                        @endforeach
                        @unless($showPercentage)
                            <th class="totalsCol">All</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredClients as $client)
                        @php
                            $countsByFailureType = [];
                            $typesToCount = $perUnitTrigger
                                ? array_keys($allFailureTypes)
                                : array_keys($selectedFailureTypes);
                            foreach ($typesToCount as $failureTypeId) {
                                $countsByFailureType[$failureTypeId] = collect($selectedMonths)->sum(function ($month) use ($client, $failureTypeId, $perUnitTrigger) {
                                    return $perUnitTrigger
                                        ? $client->getFailedUnitsCount($month, $failureTypeId)
                                        : $client->getFailedCasesCount($month, $failureTypeId);
                                });
                            }
                            $doctorTotal = collect($countsByFailureType)->only(array_keys($selectedFailureTypes))->sum();
                            $denominator = $perUnitTrigger
                                ? array_sum($countsByFailureType)
                                : $client->cases()->whereBetween('actual_delivery_date', [
                                    $rangeStart->format('Y-m-d H:i:s'),
                                    $rangeEnd->format('Y-m-d H:i:s'),
                                ])->count();
                        @endphp
                        <tr class="dataRow">
                            <td class="doctorName">{{ $client->name }}</td>
                            @foreach($selectedFailureTypes as $failureTypeId => $failureDescription)
                                @php
                                    $currentTotal = $countsByFailureType[$failureTypeId] ?? 0;
                                    $reportTotals[$failureTypeId] += $currentTotal;
                                    $displayTotal = $showPercentage
                                        ? number_format($denominator > 0 ? ($currentTotal / $denominator) * 100 : 0, 2) . '%'
                                        : $currentTotal;
                                @endphp
                                <td>{{ $displayTotal }}</td>
                            @endforeach
                            @unless($showPercentage)
                                <td class="totalsCol"><b>{{ $doctorTotal }}</b></td>
                            @endunless
                        </tr>
                    @endforeach
                </tbody>
                @unless($showPercentage)
                    <tfoot>
                        <tr>
                            <th>Totals</th>
                            @foreach($selectedFailureTypes as $failureTypeId => $failureDescription)
                                <th>{{ $reportTotals[$failureTypeId] }}</th>
                            @endforeach
                            <th>{{ array_sum($reportTotals) }}</th>
                        </tr>
                    </tfoot>
                @endunless
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
        $('input[name="perToggle"]').parent().addClass("toggleBtnGrandParent");

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
        var tables = $('.printable');

        var styling=document.getElementById("style");
        newWin= window.open("");
        newWin.document.write(styling.innerHTML);
        newWin.document.write('<h3 style="float:left">Cases Repeat Report <span style="color:#2b2b2b"> - by Repeat, per '+'{{$perUnitTrigger ? "Unit" : "Case"}}'+'</span></h3> ' +
            ' <h4 style="float:right"> Date Printed :{!! date("d") !!} - {!! date("M") !!} - {!! date("Y") !!} </h4>');
        $.each(tables, function(key, value) {
            newWin.document.write(value.outerHTML);
        });
        newWin.print();
        newWin.close();
    }
    $('.printBtn').on('click',function(){
        printData();
    });

</script>
@endpush
