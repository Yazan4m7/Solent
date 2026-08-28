@extends('layout.mainlayout_blank')

@section('title')

@endsection

@section('head')
    <!-- Responsive and DataTables -->
    <meta http-equiv="refresh" content="10">
    <style>
        .body-content{
            box-sizing: border-box;
            background: var(--color-main-bg, #f8fafc);
            padding : 10px !important;
            width: 100%;
        }
        .page-head,.dataTables_info , .dataTables_paginate , .paging_simple_numbers{
            display:none;
        }
        .body-content{
            margin-left:0px !important;
        }
        .case-monitor-card {
            height: 41vh;
            margin-bottom: 42px;
            margin-top: 15px;
            padding-inline: 8px;
        }
        .case-monitor-grid {
            margin-inline: -8px;
        }
        .case-monitor-card .portlet {
            background: var(--color-card, #ffffff);
            border: 1px solid var(--color-card-border, rgba(15, 23, 42, 0.08));
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
            height: 48vh;
            overflow: hidden;
        }
        .case-monitor-card .portlet-heading {
            background: linear-gradient(
                135deg,
                var(--color-primary-teal, #6366f1),
                var(--color-secondary-purple, #4f46e5)
            ) !important;
            border-radius: 11px 11px 0 0;
            padding: 10px 16px !important;
        }
        .case-monitor-header {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            min-width: 0;
            width: 100%;
        }
        .case-monitor-title {
            flex: 1 1 auto;
            font-size: clamp(15px, 1.6vw, 20px);
            line-height: 1.2;
            margin: 0;
            min-width: 0;
            overflow-wrap: anywhere;
            white-space: normal;
        }
        .case-monitor-count {
            flex: 0 0 auto;
            font-size: 20px;
            line-height: 1;
            margin: 0;
            min-inline-size: 28px;
            text-align: center;
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
            background-color: var(--color-card, #FFFFFF);
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
        .case-monitor-table th,
        .case-monitor-table td {
            white-space: nowrap;
        }
        .case-monitor-table {
            margin: 0 !important;
            table-layout: fixed;
            width: 100% !important;
        }
        .case-monitor-table td {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .case-monitor-table td > p {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .case-monitor-table.dataTable thead th {
            background: var(--color-surface-soft, #eef2ff) !important;
            border-bottom: 2px solid var(--color-primary-teal, #6366f1) !important;
            box-sizing: border-box;
            color: var(--color-text, #0f172a) !important;
            font-size: clamp(10px, 0.9vw, 13px) !important;
            font-weight: 700;
            line-height: 1.3;
            overflow: hidden;
            overflow-wrap: break-word;
            padding: 8px 5px !important;
            text-overflow: clip;
            vertical-align: middle;
            white-space: normal !important;
            word-break: normal;
        }
        .case-monitor-card .dataTables_scrollHead,
        .case-monitor-card .dataTables_scrollHeadInner,
        .case-monitor-card .dataTables_scrollHeadInner > table {
            width: 100% !important;
        }
        .case-monitor-card .dataTables_scrollHeadInner > table {
            table-layout: fixed !important;
        }
        .case-monitor-card .dataTables_scrollBody .case-monitor-table.dataTable thead th {
            border-block: 0 !important;
            font-size: 0 !important;
            height: 0 !important;
            line-height: 0 !important;
            padding-block: 0 !important;
        }
        .case-monitor-doctor-column { width: 22% !important; }
        .case-monitor-patient-column { width: 25% !important; }
        .case-monitor-date-column { width: 17% !important; }
        .case-monitor-units-column { width: 6% !important; }
        body .case-monitor-card .case-monitor-status-column {
            min-inline-size: 0;
            overflow: hidden;
            width: 30% !important;
        }
        body .case-monitor-card .solent-case-status-badge {
            inline-size: calc(100% - 8px) !important;
            max-inline-size: 104px;
            min-inline-size: 0;
            width: calc(100% - 8px) !important;
        }
        .case-monitor-date-column,
        .case-monitor-units-column {
            text-align: center;
        }
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 4px 5px;

        }
        .dataTables_paginate{
            padding: 0.1em 0.3em;
        }

        .paginate_button , .previous
        {height: 90%;}


        .lastRefresh{
            position: absolute;
            top:12px;
            right:0;
            font-size:12px;
            color:#1b1e21;
        }
        .case-monitor-card .dataTables_wrapper,
        .case-monitor-card .portlet-body {

            height: 41vh;}
        .case-monitor-card .dataTables_scrollBody{
            max-height: 35vh !important;
            height: 39vh !important;
            overflow-x:hidden   !important;
            overflow-y:hidden  !important;}

        body .case-monitor-card .badge-primary,
        body .case-monitor-card .badge-success,
        body .case-monitor-card .badge-warning,
        body .case-monitor-card .badge-danger {
            color: var(--surface, #ffffff) !important;
        }
        body .case-monitor-card .badge-primary {
            background: #6366f1 !important;
            border-color: #6366f1 !important;
        }
        body .case-monitor-card .badge-success {
            background: #10b981 !important;
            border-color: #10b981 !important;
        }
        body .case-monitor-card .badge-warning {
            background: #f59e0b !important;
            border-color: #f59e0b !important;
        }
        body .case-monitor-card .badge-danger {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
        }

        @media (max-width: 1199px) {
            .case-monitor-doctor-column {
                display: none;
            }
            .case-monitor-patient-column { width: 36% !important; }
            .case-monitor-date-column { width: 21% !important; }
            .case-monitor-units-column { width: 8% !important; }
            body .case-monitor-card .case-monitor-status-column { width: 35% !important; }
        }

    </style>
@endsection

@section('content')

    <div class="case-monitor-page">
    <span class="lastRefresh" style="">Last refresh : <b>{{now()->format('g:i A')}}</b></span>
    <div class="row case-monitor-grid">
        @php
            $stages = array('null', "Design", "Milling", "3D Printing", "Sintering Furnace", "Pressing Furnace
            ","Finishing & Build up", "Quality Control", "Delivery");
            $ui = trans('ui.dom');
        @endphp

        @foreach($stages as $stage => $stageTitle)
            @php
                if($stage ==0 ||$stage ==3 ||$stage ==7)
                continue;
            else
            $filteredCases = $casesByStage[$stage] ?? collect();

            @endphp
            <div class="col-12 col-md-6 col-xl-4 case-monitor-card">
                <div class="portlet" id="accordion{{$stage}}">
                    <div class="portlet-heading case-monitor-stage-heading">
                        <div class="case-monitor-header">
                            <h3 class="portlet-title text-white case-monitor-title" dir="auto">{{$stageTitle}}</h3>
                            <h3 class="portlet-title text-white case-monitor-count">{{$filteredCases->count()}}</h3>
                        </div>
                        <div class="portlet-widgets">

                            <span class="divider"></span>
                            <a data-toggle="collapse" data-parent="#accordion{{$stage}}" href="#bg-info{{$stage}}"></a>
                        </div>

                    </div>
                    <div id="bg-info{{$stage}}" class="panel-collapse collapse in show" style="">
                        <div class="portlet-body" style="padding-top:5px">
                            <table class="table display responsive screenTables case-monitor-table"  style="width:100%">
                                <thead>
                                <tr>
                                    <th class="idColumn" scope="col">ID</th>
                                    <th class="case-monitor-doctor-column" scope="col">{{ $ui['Doctor'] ?? 'Doctor' }}</th>
                                    <th class="case-monitor-patient-column" scope="col">{{ $ui['Patient'] ?? 'Patient' }}</th>
                                    <th class="case-monitor-date-column" scope="col">{{ $ui['Delivery date'] ?? 'Delivery date' }}</th>
                                    <th class="case-monitor-units-column" scope="col">#</th>
                                    <th class="solent-case-status-column case-monitor-status-column" scope="col">{{ $ui['Status'] ?? 'Status' }}</th>
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
                                        <td class="case-monitor-doctor-column"><p class="" dir="auto">{{$case->client->name}}</p></td>
                                        <td class="case-monitor-patient-column" style="padding:0"><p class="" dir="auto">{{$case->patient_name}}@if($notReady) <span style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">
                                                                            Not <br>
                                                                            Ready
                                                                            </span>
                                                @endif</p></td>
                                        <td class="case-monitor-date-column" style="padding:0px"><p class="" dir="ltr">{{date_format(date_create($case->initDeliveryDate()),"d-M") }}</p></td>
                                        <td class="case-monitor-units-column"><p class="">{{$case->unitsAmount($stage)}}</p></td>
                                        <td class="solent-case-status-column case-monitor-status-column">
                                            @php
                                                $status = $case->statusAt($stage);
                                                $statusLabel = $status === 'DC' ? 'Completed' : $status;
                                                $statusLabel = $ui[$statusLabel] ?? $statusLabel;
                                            @endphp

                                            @if(str_contains($status, "In-Progress") || str_contains($status, "Active") )
                                                @php
                                                    $job = $case->jobs->where("stage",$stage)->first();
                                                    if(isset($job->assignedTo))
                                                    $employee =$job->assignedTo;
                                                    elseif (isset($job->deliveryDriver))
                                                     $employee =$job->deliveryDriver;
                                                    else
                                                    $employee= null;
                                                @endphp
                                            <span class="badge badge-primary solent-case-status-badge">
                                                               <span> {{$employee != null ? $employee->name_initials : "N/A"}}
                                                               </span></span>
                                            @elseif(str_contains($status, "Assigned"))
                                                <span class="badge badge-warning solent-case-status-badge">
                                                        {{ $statusLabel }}</span>

                                            @elseif(str_contains($status, "Waiting"))
                                            <span class="badge badge-danger solent-case-status-badge">
                                                        {{ $statusLabel }}</span>
                                            @elseif($status === 'DC' || $status === 'Completed')
                                            <span class="badge badge-success solent-case-status-badge">
                                                        {{ $statusLabel }}</span>
                                            @else
                                            <span class="badge badge-warning solent-case-status-badge">
                                                             {{ $statusLabel }} </span>

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
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Destroy any existing DataTables to prevent reinitialisation error
            $('.screenTables').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });

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
