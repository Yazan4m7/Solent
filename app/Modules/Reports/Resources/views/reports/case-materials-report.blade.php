@extends('layouts.app' ,[ 'pageSlug' => 'Materials Report' ])


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

            <form  class="kt-form" method="GET" action="{{route('materials-report')}}">


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
                            </div>
                        </div>
                    </form>

    </div>
    <hr>
    <div class="card-body table-responsive">
        <h5 class="header-title">Total Amount:</h5>
        <h2 style=""><span style="font-weight: bold;color:#a13030">{{number_format($totalAmount)}}</span> <span style="font-weight: bold;font-size:18px;">JOD</span></h2>
        <p class="text-muted"></p>
        <div class="table-odd">
            <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"><div class="row"><div class="col-sm-12" style="padding:5px">

                        <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border sunriseTable" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 50.93px;">Doctor</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 100px;">Patient</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 83.1445px;">Zircon</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Emax</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Acrylic</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Model</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Amount</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Delivered On</th>
                            </tr>
                            </thead>


                            <tbody>
                            @foreach($cases as $case)
                                    <tr role="row" class="odd" onclick="window.location='{{route('view-invoice', $case->id)}}';">

                                        <td class="sorting_1">{{$case->client->name}}</td> <!--bold -->
                                        <td>{{$case->patient_name}}</td>
                                        <td>{{$case->materialUsed([1,20])}}</td>
                                        <td>{{$case->materialUsed([2])}}</td>
                                        <td>{{$case->materialUsed([3,4,6,7])}}</td>
                                        <td>{{$case->materialUsed([9,10])}}</td>
                                        <td>{{isset($case->invoice) ? $case->invoice->amount : 0}} </td> <!--JOD -->
                                        <td>{{substr($case->actual_delivery_date,0,10)}} </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table></div></div></div>
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
            "order": [[ 7, "desc" ]],
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
