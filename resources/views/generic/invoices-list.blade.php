@extends('layouts.app' ,[ 'pageSlug' => 'Invoices List' ])


@section('content')
    <head>

    </head>
<style>
    @media screen and (max-width: 991px){
        #datatable_wrapper {
            overflow: auto;
        }
    }
    .card-body{
        padding: 0;
    }
    .row, .container-fluid{
        padding-left:0px;
        padding-right:0px;
    }
    /*.col-sm-12 {*/
        /*padding-right:0px;*/
        /*padding-left:0px;*/
    /*}*/
    tr { cursor: pointer; }
    td {border : 0 !important;}
</style>
<div class="bg-white">
    @if(isset($clients))
        <form  class="kt-form" method="GET" action="{{route('invoices-index')}}">
            @else

     <form  class="kt-form" method="GET" action="{{route('dentist-invoices',['id' =>$id])}}">
     <input type="hidden" class="form-control" name="id" value="{{$id}}">
     @endif

    <div class="col-lg-12 col-sm-12 ">

        <div class="row" style="">

            <div class="col-lg-2 col-md-3 ">
                <div class="kt-subheader__search" style="">
                    <label>From:</label>
                    <input type="date" class="form-control" name="from" value="{{$from}}">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 ">
                <div class="kt-subheader__search" style="">
                    <label>To:</label>
                    <input type="date" class="form-control" name="to" value="{{$to}}">
                </div>
            </div>

            <div class="col-lg-3 col-md-3 ">
                @if(isset($clients))
                    <div class="dropdown">
                        <label>Doctor:</label>
                        <select style="width:100%"  class="selectpicker clearOnAll" multiple name="doctor[]" id="doctor" data-live-search="true" title="All" data-hide-disabled="true">

                            <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>All</option>
                            @foreach($clients as $d)
                                <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                            @endforeach

                        </select>

                    </div>
                    @endif

            </div>
            {{--<div class="col-lg-2 col-md-3 ">--}}
                {{--<div class="kt-subheader__search">--}}
                    {{--<label>Patient Name:</label>--}}
                    {{--<br>--}}
                    {{--<input type="text" name="patient_name" value="{{$patientName ?? ''}}"--}}
                           {{--class="form-control">--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="col-lg-3 col-md-3 ">

                  <div class="kt-subheader__search" style="width:100%">
                      <label>&nbsp; &nbsp; </label>

                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                  </div>

        </div>


</form>

        </div>
<hr>
            <div class="card-body table-responsive">
                <h5 class="header-title">Total Amount:</h5>
                <h2 style=""><span style="font-weight: bold;color:#a13030">{{number_format($invoices->sum('amount'))}}</span> <span style="font-weight: bold;font-size:18px;">JOD</span></h2>
                <p class="text-muted"></p>
                <div class="table-odd">
                    <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"><div class="row"><div class="col-sm-12" style="padding:5px">

                                <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border sunriseTable" role="grid" aria-describedby="datatable_info">
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 50.93px;">ID</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 240px;">Doctor</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 148.32px;">Patient name</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Amount</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 160.664px;">Delivered on</th>

                      </tr>
                                    </thead>


                                    <tbody>
                                    @foreach($invoices as $invoice)
                                        @if(isset($invoice->case))
                                        <tr role="row" class="odd" onclick="window.location='{{route('view-invoice', $invoice->case->id)}}';">
                                            @else
                                            <tr role="row" class="odd" onclick="alert('This case has no invoice.');">
                                            @endif
                                            <td class="sorting_1">{{$invoice->id}}</td>
                                            <td>{{$invoice->client->name}}</td>
                                            <td>{{isset($invoice->case) ? $invoice->case->patient_name :$invoice->discount_title }}</td>
                                            <td>{{$invoice->amount}} JOD</td>
                                            @if (isset($invoice->case) && isset($invoice->case->actual_delivery_date) )
                                            <td> {{$invoice->case->actualDeliveryDate()}}&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->case->actualDeliveryTime()}}</td>
                                            @else
                                            <td>-</td>
                                            @endif

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table></div></div></div>
                </div>
            </div>

    </div>





    @endsection




@push('js')

    <script type="text/javascript">
//        $(document).ready(function() {
//            $('#datatable').DataTable({
//                dom: 'Bfrtip',
//                buttons: [ 'csv', 'excel', 'pdf', 'print' ],
//                "pageLength": 25,
//                "searching": false,
//                "lengthChange": false,
//                "order": [[ 4, "desc" ]]
//            });
//        });
$(document).ready(function() {

        $('#datatable').dataTable({
            "fixedHeader": true,
            "colReorder": true,
            "responsive": true,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
            "iDisplayLength": 20,
            "order": [[ 4, "desc" ]],
            "dom": 'Bfrtip',
            "bProcessing": true,
            buttons: [
                {extend: 'excel',text: 'Export Excel'}

            ]
            //{ dom: 'Bfrtip', buttons: ['colvis', 'excel', 'print'] }
            //  "bJQueryUI": true
            // "sDom": 'l<"H"Rf>t<"F"ip>'
        });
});
    </script>
    @endpush
