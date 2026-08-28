@extends('layouts.app' ,[ 'pageSlug' => 'Delivery Schedule' ])


@section('content')
    <style>
        .row{
            margin:0 !important;;
        }
        .table-odd tbody>tr:nth-of-type(odd) {
            background-color: var(--surface) !important;
        }
        .table-odd tbody>tr:nth-of-type(even) {
            background-color: var(--surface-raised) !important;
        }
        .mb-3, .my-3 {
            margin-bottom: 0rem!important;
        }
        .vertical {
            padding-left:5px;
            border-left: 1px solid var(--border);
        }
    </style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp
<div class="row">

    <div class="col-lg-12 col-sm-12 ">

        <form class="kt-form list-filter-shell" method="GET" action="{{route('delivery-schedule')}}">
            @csrf
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3 noLeftPadding">
                    <label>From date</label><br>
                    <input class="form-control SDTP" name="from"  type="text"   value="{{$data['from'] ?? ''}}" required readonly/>

                    @if ($errors->has('from'))
                        <span class="help-block" style="color: red">{{ $errors->first('from') }}</span>
                    @endif
                      </div>
                        <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group">
                    <label>To date</label>
                    <input class="form-control SDTP" name="to" type="text"   value="{{ $data['to'] ?? ''}}" required readonly/>


                    @if ($errors->has('to'))
                        <span class="help-block" style="color: red">{{ $errors->first('to') }}</span>
                    @endif
                </div>
                    </div>
                        </div>
                        </div>

                <div class="row">
                <div class="col-12 col-sm-6 col-md-3 noLeftPadding" >
                        <button type="submit" class="btn btn-primary fillWidth">Filter</button>
                </div>
                    <div class="col-12 col-sm-6 col-md-3" >
                        <button type="button" onclick="printResult()" class="btn btn-secondary fillWidth">Print</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
</div>
@php

 $date = new DateTime;
 $date2 = $date->modify('+1 day');
 $date3 = $date->modify('+2 day');
$endofToday  = substr(now()->addDays(0),0,10) . "T23:59:00";
$endofTomorrow = substr(now()->addDays(1),0,10) . "T23:59:00";
$endofSeventhDay = substr(now()->addDays(7),0,10) . "T23:59:00";
$overdue = 0;
$numOfUnits = 0;
foreach ($cases as $case) {
    $numOfUnits += $case->unitsAmount();
    if (strtotime($case->initial_delivery_date) < strtotime('now')) {
        $overdue++;
    }
}
@endphp

            <div class="delivery-summary-grid delivery-summary-grid--standalone">
                <article class="delivery-summary-card">
                    <span class="delivery-summary-card__label">Total deliveries</span>
                    <strong class="delivery-summary-card__value">{{ count($cases) }}</strong>
                    <span class="delivery-summary-card__meta">Cases</span>
                </article>
                <article class="delivery-summary-card">
                    <span class="delivery-summary-card__label">Overdue deliveries</span>
                    <strong class="delivery-summary-card__value">{{ $overdue }}</strong>
                    <span class="delivery-summary-card__meta">Cases</span>
                </article>
                <article class="delivery-summary-card">
                    <span class="delivery-summary-card__label">Number of units</span>
                    <strong class="delivery-summary-card__value">{{ $numOfUnits }}</strong>
                    <span class="delivery-summary-card__meta">Units</span>
                </article>
            </div>

            <section class="list-results-shell delivery-results-shell">
                <p class="text-muted"></p>
                <div class="table-odd table-responsive" style="width: 100%;">

                                <table id="datatable" class="table table-bordered dataTable no-footer sunriseTable" role="grid" aria-describedby="datatable_info">
                                    <thead>
                                    <tr class="" style="left: 0px;  !important;">
                                        <th ><span >Doctor Name</span></th>
                                        <th ><span>Patient Name</span></th>
                                        <th ><span >Delivery Date</span></th>
                                        <th ><span >Delivery Time</span></th>
                                        <th ><span ># Of units</span></th>
                                        <th class="statusCol" ><span >Status</span></th>


                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($cases as $case)
                                        @php
                                            $status = $case->status();
                                            $color = '#595d6e';
                                            if(strtotime($case->initial_delivery_date) < strtotime('now'))
                                            $color='red';

                                        @endphp
                                        <tr data-row="{{$case->id}}" class="odd clickable" data-toggle="modal" data-target="#caseActionsModal{{$case->id}}">

                                            <td style="color:{{$color}} !important" ><span >{{$case->client->name }}</span></td>

                                            <td style="color:{{$color}} !important"><span >{{$case->patient_name}}</span></td>
                                            @php
                                                $date = explode('T', $case->initial_delivery_date);
                                            @endphp
                                            <td style="color:{{$color}} !important" ><span >{{isset($date[0]) ? $date[0] : "-"}}</span></td>
                                            <td style="color:{{$color}} !important"><span >{{isset($date[1]) ? date("g:i a", strtotime($date[1])) : "-"}}</span></td>
                                            <td class="statusCol" style="color:{{$color}} !important"><span >{{$case->unitsAmount(-2)}}</span></td>
                                            <td >
                                                    @if(str_contains($status, "Completed") )
                                                        <span style="font-size:12px !important;width: 160px; margin: auto; text-align: center" class="badge badge-success middle">Completed</span>
                                                    @elseif( str_contains($status, "Active"))
                                                        <span style="font-size:12px !important;width: 160px; margin: auto; text-align: center" class="badge badge-primary middle">{{$status}}</span>
                                                @elseif(str_contains($status, "In-Progress"))
                                                    <span style="font-size:12px !important;width: 160px; margin: auto; text-align: center" class="badge badge-primary middle">Active</span>
                                                    @elseif(str_contains($status, "Waiting"))
                                                        <span style="font-size:12px !important;width: 160px; margin: auto; text-align: center" class="badge badge-danger middle">{{$status}}</span>
                                                        @else
                                                        <span style="font-size:12px !important;width: 160px; margin: auto; text-align: center" class="badge badge-warning middle">{{$status}}</span>
                                                @endif</td>

                                        </tr>
                                        @if(($permissions && $permissions->contains('permission_id', 110)) || Auth()->user()->is_admin)
                                        <div class="modal" tabindex="-1" role="dialog" id="myModal{{$case->id}}">
                                            <form action="{{route('edit-delivery-date')}}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$case->id}}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delivery Date</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group row">
                                                                <div class="form-group col-6">
                                                                    <label for="milled">Case:</label>
                                                                    <h5>{{$case->client->name}} - {{$case->patient_name}}</h5>
                                                                    </br>
                                                                    <label for="milled">Delivery Date</label>
                                                                    @php
                                                                        $time = $case->initial_delivery_date;
                                                                        $time = str_replace(' ','T', $time);
                                                                    @endphp
                                                                    <input class="form-control SDTP" name="delivery_date"  type="text"   value="{{$time}}" required readonly/>

                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        @endif
                                        <div class="modal" tabindex="-1" role="dialog" id="actionsDialog{{$case->id}}">

                                            <input type="hidden" name="case_id" value="{{$case->id}}">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Case Actions</h5>

                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="form-group row" style="margin-bottom: 0px">
                                                            <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                <label for="doctor">Doctor: </label>
                                                                <h5 id="doctor"><b>{{$case->client->name}}</b></h5>
                                                            </div>
                                                            <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                <label for="pat">Patient: </label>
                                                                <h5 id="pat"><b>{{$case->patient_name}}</b></h5>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group row">
                                                            <div class=" col-12 ">
                                                                <label ><b>Jobs:</b></label><br>



                                                                @foreach( $case->jobs as $job)

                                                                    @php
                                                                        $unit = explode(', ',$job->unit_num);
                                                                    @endphp

                                                                    <span >{{$job->unit_num}} - {{$job->jobType->name ?? "No Job Type"}} - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}
                                                                        {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}<br>
                                                                        {{isset($job->abutmentR)  && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>
                                                                @endforeach
                                                            </div></div>
                                                        @if(count($case->notes)>0)
                                                            <hr>
                                                            <label ><b>Notes:</b></label><br>
                                                            @foreach($case->notes as $note)
                                                                <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px" disabled>

                                                                    <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer fullBtnsWidth" >
                                                        <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">


                                                                <div class="row">
                                                                    <!-------------------------
                                                                           ------ View Voucher ------
                                                                           -------------------------->
                                                                    <div class="col-6 padding5px" >
                                                                        <a  href="{{route('view-voucher',$case->id)}}">
                                                                            <button type="button" class="btn btn-info "><i
                                                                                        class="fas fa-print"></i> View Voucher </button>
                                                                        </a></div>

                                                                    <!-------------------------
                                                                    -------- View Case --------
                                                                    -------------------------->
                                                                    <div class="col-6 padding5px" >
                                                                        <a  href="{{route('view-case',['id' =>$case->id ,'stage' =>-2 ])}}">
                                                                            <button type="button" class="btn btn-info "><i
                                                                                        class="far fa-file-alt"></i> View Case </button>
                                                                        </a></div>
                                                                </div>
                                                                <div class="row">

                                                                                <!-------------------------
                                                                                  -------- Edit CASE --------
                                                                                  -------------------------->
                                                                    @if(Auth()->user()->is_admin ||
                                                                    ($permissions && ($permissions->contains('permission_id', 102))) ||
                                                                    ($permissions &&
                                                                    ((!isset($case->actual_delivery_date)&& $permissions->contains('permission_id', 115)))
                                                                    || ((isset($case->jobs[0]) && $case->jobs[0]->stage == 1) && $permissions->contains('permission_id', 1)))
                                                                    )
                                                                        @if(!$case->locked)

                                                                            <div class="col-6 padding5px" >
                                                                                <a  href="{{route('edit-case-view',$case->id)}}">
                                                                                    <button type="button" class="btn btn-warning "><i class="fa-solid fa-pen-to-square"></i> Edit Case</button>
                                                                                </a></div>
                                                                        @endif
                                                                    @endif
                                                                    @if(($permissions && $permissions->contains('permission_id', 110)) || Auth()->user()->is_admin)
                                                                        <div class="col-6 padding5px" >

                                                                            <button type="button" class="btn btn-danger "  data-dismiss="modal"  data-toggle="modal" data-target="#myModal{{$case->id}}"><i class="fa-solid fa-pen-to-square"></i> Edit Delivery Date</button>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                            <div class="col-12 padding5px" >
                                                                <button type="button" class="btn btn-secondary " data-dismiss="modal" style="width:100%">Cancel</button>
                                                            </div>
                                                        </div>


                                                    </div>



                                                </div>
                                            </div>

                                        </div>

                                    @endforeach
                                    </tbody>
                                </table>
                </div>
            </section>

            @foreach($cases as $case)
                <x-partiels.caseActionsModal
                    :case="$case"
                    :modalId="'caseActionsModal' . $case->id"
                    :allowDeliveryDateChange="true"
                    :deliveryDateModalId="'myModal' . $case->id" />
            @endforeach





    @endsection

@push('js')

    <script type="text/javascript">
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                return;
            }

            $('#datatable').DataTable({
                "order": [ [ 2, "asc" ],[ 3, 'asc' ]],
                "buttons": window.solentDataTableButtons ? window.solentDataTableButtons(true) : [],
                "pageLength": 25,
                "searching": true,
                "lengthChange": true,
                "responsive": true,
                "dom": "<'solent-datatable-toolbar'Bfl>rtip",
                "columnDefs": [
                    { "width": "20%", "targets": 5 }
                ]
            });
        } );


        function printResult() {
            const sourceTable = document.getElementById('datatable');
            const printWindow = window.open('', 'PRINT', 'height=650,width=900');

            if (!sourceTable || !printWindow) {
                return false;
            }

            const printableTable = sourceTable.cloneNode(true);
            printableTable.removeAttribute('id');
            printableTable.querySelectorAll('[data-toggle], [data-target]').forEach(function (element) {
                element.removeAttribute('data-toggle');
                element.removeAttribute('data-target');
            });

            const fromDate = @json($data['from'] ?? null);
            const toDate = @json($data['to'] ?? null);
            const rangeText = fromDate && toDate
                ? '<p>From <b>' + fromDate + '</b> to <b>' + toDate + '</b><br><b>{{ count($cases) }}</b> Cases</p>'
                : '';

            printWindow.document.open();
            printWindow.document.write(
                '<!doctype html><html><head><title>' + document.title + '</title>' +
                '<style>' +
                'body{font-family:Cairo,Arial,sans-serif;padding:40px;color:#0f172a;}' +
                'h1{text-align:center;font-size:22px;margin:0 0 16px;}' +
                'p{text-align:center;margin:0 0 20px;color:#64748b;}' +
                'table{width:100%;border-collapse:collapse;font-size:13px;}' +
                'th,td{border:1px solid #e2e8f0;padding:9px 10px;text-align:left;}' +
                'th{background:#0f172a;color:#ffffff;text-transform:uppercase;font-size:11px;}' +
                'tr:nth-child(even){background:#f8fafc;}' +
                '.badge{display:inline-block;color:#ffffff!important;border-radius:999px;padding:4px 10px;font-size:11px;}' +
                '</style></head><body><h1>Delivery Schedule</h1>' +
                rangeText +
                printableTable.outerHTML +
                '</body></html>'
            );
            printWindow.document.close();
            printWindow.focus();
            setTimeout(function () {
                printWindow.print();
                printWindow.close();
            }, 300);

            return true;
        }

    </script>
@endpush
