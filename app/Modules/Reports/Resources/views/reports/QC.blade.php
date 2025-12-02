@extends('layouts.app' ,[ 'pageSlug' => 'Quality Control Report'])

@section('content')
    <link href="{{asset('assets/css/picker.css')}}" rel="stylesheet">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <!-- styles to carry on while printing -->
    <div id="style">
        <style>
            footer{display:none}
            .korvionPanel {
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
        </style>
    </div>

    <form class="kt-form filtersPanel bd-callout bd-callout-info korvionPanel" method="GET" action="{{route('QC-report')}}" style="/*height:30%*/">


        <!-- FILTERS -->
        <div class="container">
            <div class="row " style="padding-left: 0;padding-top: 0;padding-bottom: 0px">
                <div class="col-lg-3 col-md-3 col-6 mb-2">
                    <div class="kt-subheader__search" style="">
                        <label>Date Range:</label>
                        <input class="form-control dateRange" name="dateRange" autocomplete="off" readonly
                               value="{{$dateRangeValue ?? "Select Period"}}" style="cursor: pointer;">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-6 mb-2">

                    <div class="dropdown">
                        <label>Failure Cause:</label>
                        <select style="width:100%" class="selectpicker clearOnAll" multiple name="causesInput[]"
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
                <div class="col-lg-3 col-md-3 col-6 mb-2">
                    @if(isset($clients))
                        <div class="dropdown">
                            <label>Doctor:</label>
                            <select style="width:100%" class="selectpicker clearOnAll" multiple name="doctor[]"
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
                <div class="col-lg-3 col-md-3 col-6 mb-2">

                    <div class="dropdown">
                        <label>Type of failure:</label>
                        <select style="width:100%" class="selectpicker clearOnAll" multiple name="failureTypeInput[]"
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
            <div class="row actionButtonsRow" style="padding-left: 10px;padding-top: 0;padding-bottom: 0px;    padding-right: 0;">

                <div class="col-lg-3 col-md-3 col-3" style="padding-left: 0;">
                    <div class="kt-subheader__search">

                        <div class="kt-form__actions">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-3 " >
                    <div class="kt-subheader__search">

                        <div class="kt-form__actions">
                            <button  class="btn btn-secondary printBtn">PRINT</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <div class="korvionPanel" style="">
        <div class="row" >
            <div class="col-lg-12 col-sm-12  row" style="flex-direction: row;padding-bottom:0px">
                {{--<div class="col-lg-3 col-md-3 col-3 mb-3">--}}
                    {{--<div class= "vertical">--}}
                        {{--<span style="font-weight: bold;font-size:15px;">Total Failures:</span><br>--}}
                        {{--<span style="font-weight:bold;font-size:19px; color:#3b8b45">{{array_sum(array_map("count", $failureLogs));}}</span>--}}
                        {{--<span style="font-size:13px;color:#3b8b45">Incidents</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="col-lg-3 col-md-3 col-3 mb-3">
                    <div class= "vertical">
                        <span style="font-weight: bold;font-size:15px;">Total cases:</span><br>
                        <span style="font-weight:bold;font-size:19px; color:#3b8b45">{{array_sum($amountOfCases) }}</span>
                        <span style="font-size:13px;color:#3b8b45">Cases</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-3 mb-3">
                    <div class= "vertical">
                        <span style="font-weight: bold;font-size:15px;">Total units:</span><br>

                        <!-- Filled via Jquery because of the calculation is after the display(below span) -->
                        <span id="numOfUnitsFailed" style="font-weight: bold;font-size:19px;color:red"> </span>

                        <span style="font-size:13px;color:red">Units</span>
                    </div>
                </div>
        </div>
        <div class="col-lg-12 col-sm-12">

            <div class=" ">
                <div class="">
                    <p class="text-muted"></p>
                    <div class="" style="overflow-x:auto;">
                        @php

                        $failuresDesc = [0 => "Rejection",1 => "Repeat", 2 => "Modification" , 3=> "Redo"];
                        $counterTest = 0;
                        $monthHasNoFailures = false;
                        @endphp

                        @foreach($selectedMonths as $month)
                            {{$monthHasNoFailures = false;}}
                            @php if ($amountOfCases[$month] == 0)
                            $monthHasNoFailures = true;
                            @endphp
                            <table border="1" class="xl649957 printable sunriseTable" style="border-collapse:collapse;">
                                <thead>
                                <tr class="bottom-Border subHeaderRow" style="mso-height-source:userset;">
                                    <th class="" style="background-color: transparent !important;">Month:</th>
                                    <th colspan="5"
                                        style="height:21.95pt;border-top:none">{{$month}} {{$amountOfCases != 0 ? '('.$amountOfCases[$month] . ") Cases" : ""}}</th>

                                </tr>
                                </thead>


                                <tbody>
                                <!-- The Months row -->

                                @if($monthHasNoFailures)
                                    <tr  style="text-align: center;color:forestgreen"> <td colspan="2" class="xl639957" style="height:21.95pt;border-top:none">No Incidents</td></tr>
                                    @continue

                                @endif

                                <!--The MAIN row -->
                                <tr class=" border-bottom tableHeaderRow">
                                    <td class="xl639957" style="height:21.95pt;border-top:none">Dr Name</td>
                                    <td class="xl639957" style="">Patient</td>
                                    <td class="xl639957" style="">Status</td>
                                    <td class="xl639957" style="">Causes</td>
                                    <td class="xl639957" style=""># of Units</td>
                                    <td class="xl639957" style="">Date Failure Registered</td>
                                </tr>
                                <!-- Client ROWS -->

                                @foreach($failureLogs[$month] as $failLog )
                                    @if(!in_array('all' ,$selectedClients))
                                        @if(isset($selectedClients) && !in_array($failLog->case->client->id ,$selectedClients))
                                            @continue;
                                        @endif
                                    @endif

                                    <!-- if all is selected, dont check if client is selected or not, otherwise check each one by id -->
                                    {{--@if(!in_array('all' ,$selectedClients))--}}
                                    {{--@if(isset($selectedClients) && !in_array($client->id ,$selectedClients))--}}
                                    {{--@continue;--}}
                                    {{--@endif--}}
                                    {{--@endif--}}

                                    <tr class="dataRow" style="">
                                        <td class="xl669957 doctorName">{{$failLog->case->client->name ?? "Case Not found"}}</td>
                                        <td class="xl669957">{{$failLog->case->patient_name ?? "Case Not found"}}</td>
                                        <td class="xl669957">{{$failuresDesc[$failLog->failure_type]}}</td>
                                        <td class="xl669957">{{$failLog->causeObject->text}}</td>

                                        <td class="xl669957">
                                            @php
                                             if(isset($failLog->case) ){
                                                $numOfUnits= $failLog->case->failedUnitsAmount($failLog->failure_type);
                                                $counterTest= $counterTest + $failLog->case->failedUnitsAmount($failLog->failure_type);}
                                            else
                                                $numOfUnits = "Case Not found";

                                            @endphp

                                                {{$numOfUnits}}
                                        </td>
                                        <td class="xl669957">{{substr($failLog->created_at,0,-3)}}</td>
                                    </tr>
                                @endforeach


                                <!-- Totals for whole lab Row -->
                                {{--<tr style="">--}}
                                    {{--<td class="xl669957">Totals</td>--}}

                                    {{--@foreach($labLevelTotal[$month] as $total)--}}
                                        {{--<td class="totalsRow" style="">{{$total}}</td>--}}
                                    {{--@endforeach--}}
                                    {{--<td class="totalsRow" style="">{{array_sum($labLevelTotal[$month])}}</td>--}}
                                {{--</tr>--}}
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            </div>
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
        $("#numOfUnitsFailed").html({!! $counterTest !!});
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
                setDate: {!! '"'. $dateRangeValue . '"' !!}

            }
        )
            .on('datePicker.done', function (e, result) {
                console.log(result);
            });

        console.log("Amount of units : " );
            console.log({!! $counterTest !!});
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
