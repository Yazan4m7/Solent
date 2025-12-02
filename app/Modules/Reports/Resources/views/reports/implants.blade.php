@extends('layouts.app' ,[ 'pageSlug' => 'Implants Report'])

@section('content')
    <link href="{{asset('assets/css/picker.css')}}" rel="stylesheet">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <!-- styles to carry on while printing -->
    <div id="style">
        <style>
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

    <form class="kt-form filtersPanel bd-callout bd-callout-info korvionPanel" method="GET" action="{{route('implants-report')}}" style="/*height:30%*/">

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
                        <label>Implants:</label>
                        <select style="width:100%" class="selectpicker clearOnAll" multiple name="implantsInput[]"
                                id="implantsInput" data-live-search="true" title="All" data-hide-disabled="true">

                                @php

                                        @endphp
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
                <div class="col-lg-3 col-md-3 col-6 mb-2">

                    <div class="dropdown">
                        <label>Abutment:</label>
                        <select style="width:100%" class="selectpicker clearOnAll" multiple name="abutmentsInput[]"
                                id="abutmentsInput" data-live-search="true" title="All" data-hide-disabled="true">

                                @php

                                        @endphp
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
                <div class="col-lg-3 col-md-3 col-3 mb-2">
                    <div class="kt-subheader__search">
                        <label> Per :</label>
                        <div class="kt-form__actions">
                            <input name="perToggle" {{$perUnitTrigger ? "" : "checked"}} class="unstyled" id="toggle" type="checkbox"  data-toggle="toggle" data-on="UNITS" data-off="CASES" data-onstyle="success" data-offstyle="info" >
                        </div>
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
        <div class="col-lg-12 col-sm-12">
            <div class=" ">
                <div class="">
                    <p class="text-muted"></p>
                    <div class="" style="overflow-x:auto;">
                        <div id="totalsTableHolder"> </div>
                        @foreach($selectedMonths as $month)
                            <table border="1" class="xl649957 printable sunriseTable" style="border-collapse:collapse;">

                                <thead>
                                <!-- The Months row -->
                                <tr class="bottom-Border subHeaderRow" style="mso-height-source:userset;">
                                    <th class="" style="background-color: transparent !important;">Month:</th>
                                    <th colspan="{{count($selectedAbutments)+1}}"
                                        style="height:21.95pt;border-top:none">{{$month}}</th>

                                </tr>
                                </thead>


                                <tbody>
                                <!--The MAIN row -->
                                <tr class=" border-bottom tableHeaderRow">
                                    <td class="xl639957" style="height:21.95pt;border-top:none">Dr Name</td>
                                    @foreach($selectedAbutments as $d)
                                        <td class="xl639957" style="">{{$d->name}}</td>
                                    @endforeach
                                    <td class="totalsCol" style="">All</td>
                                </tr>



                                @php
                                    if(!in_array('all' ,$selectedClients))
                                     $filteredClients = $clients->filter(function ($value, $key) use ($selectedClients) {
                                    return in_array($key ,$selectedClients);
                                       });
                                    else
                                    $filteredClients = $clients;

                                @endphp
                                <!-- Client ROWS -->

                                @foreach($filteredClients as $client )


                                    <!-- if all is selected, dont check if client is selected or not, otherwise check each one by id -->
                                    {{--@if(!in_array('all' ,$selectedClients))--}}
                                    {{--@if(isset($selectedClients) && !in_array($client->id ,$selectedClients))--}}
                                    {{--@continue;--}}
                                    {{--@endif--}}
                                    {{--@endif--}}

                                    <tr class="dataRow" style="">
                                        <td class="xl669957 doctorName">{{$client->name}}</td>

                                        @php
                                            $docTotalUnits = 0;
                                            $currentTotal = 0;
                                        $implantsIds = $selectedImplants->pluck("id")->toArray();
                                        @endphp

                                        @foreach($selectedAbutments as $abutment)
                                            @php

                                                $currentTotal= $perUnitTrigger ? $client->numOfCasesBy_abutment_implants($abutment->id,$implantsIds,$month) :  $client->numOfUnitsBy_abutment_implants($abutment->id,$implantsIds,$month) ;
                                                $docTotalUnits += $currentTotal;
                                                $clientLevelTotal[$month][$abutment->id] += $currentTotal;
                                                $labLevelTotal[$month][$abutment->id] += $currentTotal;
                                                $totals[$client->id][$abutment->id] +=$currentTotal;
                                                $totals2[$abutment->id] +=$currentTotal;
                                            @endphp
                                            <td class="xl649957">{{$currentTotal}}</td>
                                        @endforeach
                                        <td style="" class="totalsCol"><b>{{$docTotalUnits}}</b></td>
                                        @php  @endphp

                                    </tr>
                                @endforeach


                                <!-- Totals for whole lab Row -->
                                <tr style="">
                                    <td class="xl669957">Totals</td>

                                    @foreach($labLevelTotal[$month] as $total)
                                        <td class="totalsRow" style="">{{$total}}</td>
                                    @endforeach
                                    <td class="totalsRow" style="">{{array_sum($labLevelTotal[$month])}}</td>
                                </tr>
                                </tbody>
                            </table>
                        @endforeach
                        <div id="totalsTableTempHolder">

                            <table border="1" class="xl649957 printable sunriseTable" style="border-collapse:collapse;">

                                <thead>
                                <!-- The Months row -->
                                <tr class="bottom-Border subHeaderRow" style="mso-height-source:userset;">
                                    <th class="" style="background-color: transparent !important;">Month:</th>
                                    <th colspan="{{count($selectedAbutments)+1}}"
                                        style="height:21.95pt;border-top:none">All Time</th>

                                </tr>
                                </thead>


                                <tbody>
                                <!--The MAIN row -->
                                <tr class=" border-bottom tableHeaderRow">
                                    <td class="xl639957" style="height:21.95pt;border-top:none">Dr Name</td>
                                    @foreach($selectedAbutments as $d)
                                        <td class="xl639957" style="">{{$d->name}}</td>
                                    @endforeach
                                    <td class="totalsCol" style="">All</td>
                                </tr>



                                @php
                                    if(!in_array('all' ,$selectedClients))
                                     $filteredClients = $clients->filter(function ($value, $key) use ($selectedClients) {
                                    return in_array($key ,$selectedClients);
                                       });
                                    else
                                    $filteredClients = $clients;

                                @endphp
                                <!-- Client ROWS -->

                                @foreach($filteredClients as $client )


                                    <!-- if all is selected, dont check if client is selected or not, otherwise check each one by id -->
                                    {{--@if(!in_array('all' ,$selectedClients))--}}
                                    {{--@if(isset($selectedClients) && !in_array($client->id ,$selectedClients))--}}
                                    {{--@continue;--}}
                                    {{--@endif--}}
                                    {{--@endif--}}

                                    <tr class="dataRow" style="">
                                        <td class="xl669957 doctorName">{{$client->name}}</td>

                                        @php
                                            $docTotalUnits = 0;
                                            $currentTotal = 0;
                                        $implantsIds = $selectedImplants->pluck("id")->toArray();
                                        @endphp

                                        @foreach($selectedAbutments as $abutment)
                                            @php

                                                $currentTotal= $totals[$client->id][$abutment->id] ;
                                                $docTotalUnits += $currentTotal;

                                            @endphp
                                            <td class="xl649957">{{$currentTotal}}</td>
                                        @endforeach
                                        <td style="" class="totalsCol"><b>{{$docTotalUnits}}</b></td>
                                        @php  @endphp

                                    </tr>
                                @endforeach


                                <!-- Totals for whole lab Row -->
                                <tr style="">
                                    <td class="xl669957">Totals</td>

                                    @foreach($totals2 as $total)
                                        <td class="totalsRow" style="">{{$total}}</td>
                                    @endforeach
                                    <td class="totalsRow" style="">{{array_sum($totals2)}}</td>
                                </tr>
                                </tbody>
                            </table>
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
        $("#totalsTableHolder").html($("#totalsTableTempHolder").html());
        $("#totalsTableTempHolder").html("");
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
        newWin.document.write(' <h3 style="float:left">Clients Consumptions Report <span style="color:#2b2b2b"> - by Abutments & Implants, per '+'{{$perUnitTrigger ? "Unit" : "Case"}}'+'</span></h3> ' +
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
