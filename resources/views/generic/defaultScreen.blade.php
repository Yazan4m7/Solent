@extends('oldLayout.mainlayout_blank')

@section('title')

@endsection

@section('head')
<!-- Responsive and DataTables -->
<meta http-equiv="refresh" content="10">
<style>
    .body-content{
    padding : 10px !important;
    }
    .page-head,.dataTables_info , .dataTables_paginate , .paging_simple_numbers{
        display:none;
    }
    .body-content{
        margin-left:0px !important;
    }
    .portlet .portlet-heading {
        padding: 10px 24px !important;
    }
    .tooltipX {
        position: relative;
        display: inline-block;
    }
    p{
        font-size:13px !important;
        margin-bottom: 3px !important;
        margin-top: 13px !important;
    }
    th{
        font-size:13px !important;
    }
    section{
        height:100% !important;
    }

    /* Tooltip text */
    .tooltipX .tooltiptext {
        padding:5px;
        visibility: hidden;
        display:none;
        /* width: 120px;*/
        background-color: #e8e8e8;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        border: 1px solid #000000;
        position: absolute;
        z-index: 1;
        top: -5px;
        right: 110%;
    }
    .tooltipX .tooltiptext::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 100%;
        margin-top: -5px;
        border-width: 5px;
        border-style: solid;
        border-color:transparent transparent transparent black;
    }
    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltipX:hover .tooltiptext {
        visibility: visible;
    }
    .hiddenByDefault{
        /*display:none;*/
    }
    .actionsBtn{
        height:20px;white-space: normal;
    }
    .actionsBtn::after{
        display: table !important;
    }
    th,td,tr,html{
        white-space: nowrap;
    }
    table.dataTable tbody th, table.dataTable tbody td {
        padding: 4px 5px;

    }
    .dataTables_paginate{
        padding: 0.1em 0.3em;
    }

    .paginate_button , .previous
    {height: 90%;}

    body{
        line-height: 0.5;
    }
    .lastRefresh{
        position: absolute;
        top:12px;
        right:0;
        font-size:12px;
        color:#1b1e21;
    }
    .portlet{
        height:48vh;
    }
    .dataTables_wrapper , .portlet-body {

    height: 41vh;}
    .dataTables_scrollBody{
        max-height: 35vh !important;
        height: 39vh !important;
        overflow-x:hidden   !important;
        overflow-y:hidden  !important;}

</style>
@endsection

@section('content')

   <span class="lastRefresh" style="">Last refresh : <b>{{now()->format('g:i A')}}</b></span>
<div class="row" style="">
    @php
        $stages = array('null', "Design", "Milling", "3D Printing", "Sintering Furnace", "Pressing Furnace
        ","Finishing & Build up", "Quality Control", "Delivery");
    @endphp

@foreach($stages as $stage => $stageTitle)
        @php
            if($stage ==0 ||$stage ==3 ||$stage ==7)
            continue;
        else
        $filteredCases= app('App\Http\Controllers\Helpers')->filterByStage($cases,$stage);

        @endphp
                        <div class="col-lg-4 col-sm-12" style="height: 41vh;margin-bottom: 42px; margin-top:15px;padding-right: 8px;
    padding-left: 8px;">
                            <div class="portlet" id="accordion{{$stage}}">
                                <div class="portlet-heading bg-info">
                                    <div class="row" style="justify-content:space-between;;">
                                    <div class="col-11 col-sm-2 pull-left " style="text-align: left">

                                            <h3 class="portlet-title text-white">{{$stageTitle}}</h3>
                                        </div>
                                    <div class="col-1 col-sm-2 pull-right "  style="text-align: right">
                                        <h3 class="portlet-title text-white">{{$filteredCases->count()}}</h3>
                                        </div></div>
                                    <div class="portlet-widgets">

                                        <span class="divider"></span>
                                        <a data-toggle="collapse" data-parent="#accordion{{$stage}}" href="#bg-info{{$stage}}"></a>
                                    </div>

                                </div>
                                <div id="bg-info{{$stage}}" class="panel-collapse collapse in show" style="">
                                    <div class="portlet-body" style="padding-top:5px">
                                        <table class="table display responsive screenTables"  style="width:100%">
                                            <thead>
                                            <tr>
                                                <th class="idColumn">ID</th>
                                                <th>Doctor</th>
                                                <th>Patient</th>
                                                <th style="width:6%;text-align:center">Deliv. Date</th>
                                                <th style="width:3%;text-align:center">#</th>
                                                <th>Status</th>
                                               <!--  <th>Actions</th> -->
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $flag = ""; @endphp
                                            @foreach ($filteredCases as $case)
                                                @php
                                                    $notReady=false;
                                                @endphp

                                                @if ($stage == 6 )
                                                    @php
                                                        $notReady=false;
                                                        if (!$case->shouldShowForFinishing()) continue;
                                                        if(!$case->allUnitsAtFinishing())
                                                        $notReady=true;
                                                    @endphp
                                                @endif
                                                <tr class="">
                                                    <td class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                    <td><p class="">{{$case->client->name}}</p></td>
                                                    <td style="padding:0"><p class="">{{$case->patient_name}}@if($notReady) <span style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">
                                                                            Not <br>
                                                                            Ready
                                                                            </span>
                                                            @endif</p></td>
                                                    <td  style="width:6%;text-align:center;padding:0px"><p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M") }}</p></td>
                                                    <td><p class="" style="text-align:center">{{$case->unitsAmount($stage)}}</p></td>
                                                    <td>

                                                        @if(str_contains($case->statusAt($stage), "In-Progress") || str_contains($case->statusAt($stage), "Active") )
                                                            @php
                                                            $job = $case->jobs->where("stage",$stage)->first();
                                                            if(isset($job->assignedTo))
                                                            $employee =$job->assignedTo;
                                                            elseif (isset($job->deliveryDriver))
                                                             $employee =$job->deliveryDriver;
                                                            else
                                                            $employee= null;
                                                            @endphp
                                                            <span style="width:auto; margin: auto; text-align: center" class="badge badge-primary">
                                                               <span> {{$employee != null ? $employee->name_initials : "N/A"}}
                                                               </span></span>
                                                        @elseif(str_contains($case->statusAt($stage), "Assigned"))
                                                            <span style="width:auto; margin: auto; text-align: center"
                                                                  class="badge badge-warning">
                                                        Assigned</span>

                                                        @elseif(str_contains($case->statusAt($stage), "Waiting"))
                                                            <span style="width:auto; margin: auto; text-align: center" class="badge badge-danger">
                                                        {{$case->statusAt($stage)}}</span>
                                                        @else
                                                            <span style="width:auto; margin: auto; text-align: center" class="badge badge-info">
                                                             {{$case->statusAt($stage)}} </span>

                                                        @endif
                                                    </td>

                                                </tr>


                                            @endforeach

                                            </tbody>
                                        </table></div>
                                </div>
                            </div>
                        </div>
                       @endforeach
                    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

    $('.screenTables').DataTable({

    "pageLength": 25,
    "searching": false,
    "lengthChange": false,
     "ordering": false,
     "paging": false,
        "scrollY": true,
    "responsive" : false,
    "columnDefs": [
    { targets: [0], visible: false},
    ],
    });
            $('.screenTables').each(function(i, obj){

                if ( $(obj).height() < 227) return;
                var speedOfScrollingDown =  $(obj).find('tr').length *470;
                var speedOfScrollingUp = $(obj).find('tr').length *470;
                var timeToWaitOnBottom =  2500;
                var timeToWaitOnTop =  2500;

                setTimeout(function() {
                $(obj).parent().animate({ scrollTop:  $(obj).height() },
                // Duration of Scrolling
                    speedOfScrollingDown);
                },timeToWaitOnBottom);

            // First scroll to TOP [ONCE]
            setTimeout(function() {
                    $(obj).parent().animate({
                    // Scroll to where:
                     scrollTop:0
                    },
                    // Duration of scrolling:
                    speedOfScrollingUp);
                },
                // Time to wait before scrolling back to TOP
                timeToWaitOnTop + speedOfScrollingUp + speedOfScrollingDown + timeToWaitOnBottom);


            /* ------------------------------------------------ */
                setInterval(function(){

                    $(obj).parent().animate({ scrollTop:
                        // Position to scroll to ( do not change)
                            $(obj).height() },
                        // Speed of scrolling down
                        speedOfScrollingDown);

                    setTimeout(function() {
                        $(obj).parent().animate({scrollTop:
                            // Position to scroll to ( do not change)
                                0},
                            // Speed of scrolling back up
                            speedOfScrollingUp);
                        // run 'setTimeOut' or Scroll to top after  xxxx milliseconds
                    },timeToWaitOnBottom+speedOfScrollingDown);

                    // Time it takes to execute code above and wait between execution (every xxxx milliseconds)
                    // increase to make it wait on top

                },timeToWaitOnTop + speedOfScrollingUp + speedOfScrollingDown + timeToWaitOnBottom);

            });



            // First scroll to bottom [ONCE]
//
//            $(".dataTables_scrollBody").animate({ scrollTop: $(".dataTables_scrollBody").height() },
//                // Duration of Scrolling
//                2000);
//
//
//            // First scroll to TOP [ONCE]
//            setTimeout(function() {
//                $('.dataTables_scrollBody').animate({
//                    // Scroll to where:
//                        scrollTop:0
//                    },
//                    // Duration of scrolling:
//                    2000);
//            },
//                // Time to wait before scrolling back to TOP
//                8000);

//            setInterval(function(){
//
//                $(".dataTables_scrollBody").animate({ scrollTop:
//                    // Position to scroll to ( do not change)
//                        $(".dataTables_scrollBody").height() },
//                    // Speed of scrolling down
//                    1000);
//
//                setTimeout(function() {
//                    $('.dataTables_scrollBody').animate({scrollTop:
//                            // Position to scroll to ( do not change)
//                            0},
//                    // Speed of scrolling back up
//                    1000);
//                    // run 'setTimeOut' or Scroll to top after  xxxx milliseconds
//                },3000);
//
//            // Time it takes to execute code above and wait between execution (every xxxx milliseconds)
//            // increase to make it wait on top
//
//            },5000);
        });
    </script>
    @endsection
