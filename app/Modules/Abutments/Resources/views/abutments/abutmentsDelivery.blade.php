@extends('layouts.app' ,[ 'pageSlug' => "Abutments Delivery"])
@section('content')

    <style>
        .tooltiptext {
            display: none;
        }
        th{
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            .content {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
            .caseDeliTime , .doctor{
                display:none;
            }

            .row {
                padding: 3px;
            }
            table{
                table-layout: fixed;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);

    @endphp
    <form class="kt-form" method="GET" action="{{route('abutments-delivery-index')}}">
        <div class="container full-width">
            <div class="row " style="padding-bottom:0">
                <div class=" col-sm-6 col-md-3 mb-3">
                    <div class="kt-subheader__search" style="">
                        <label>From (Start of):</label>
                        <input type="date" class="form-control" name="from" value="{{$from}}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 mb-3">
                    <div class="kt-subheader__search" style="">
                        <label>To (End of):</label>
                        <input type="date" class="form-control" name="to" value="{{$to}}">
                    </div>
                </div>


            </div>
        </div>
        <div class="container full-width">
            <div class="row justify-content-between">
                <div class="col-6 col-sm-6 col-md-3  mb-3">
                    <button type="submit" class="btn btn-primary ">Submit</button>
                </div>
            </div>


        </div>
    </form>

    <div class="container full-width">
        <div class="row" style="justify-content: flex-end; border-radius: 4px;">
            <div class="col-12">
                <br>
                <table id="datatable"
                       class="table-striped compact sunriseTable"
                       role="grid"
                       style="width:100%">
                    <thead>
                    <tr role="row">
                        <th class="doctor">Doctor</th>
                        <th>Patient</th>
                        <th class="caseDeliTime">Case Delivery Time</th>
                        <th>Implant</th>
                        <th>Abut.</th>
                        <th >Code</th>

                        <th>Qty</th>
                        <th>Status</th>

                    </tr>
                    </thead>

                    <tbody>
                    @foreach($deliveries  as $item)
                        @php
                            if(!$item->case) continue;
                        @endphp

                        <tr role="row" class="odd clickable" data-toggle="modal"
                            data-target="#actionsDialog{{$item->id}}">

                            <td class="sorting_1 doctor">{{$item->case->client->name}}</td>
                            <td>{{$item->case->patient_name}}</td>
                            @php
                               $time = explode("T",$item->case->initial_delivery_date);
                               $date = explode("-", $time[0]);
                               $monthName = date('M', mktime(0, 0, 0, $date[1], 10));
                              $timePMAM= date("g:i a", strtotime($time[1]));
                            @endphp

                            <td class="caseDeliTime">{{$monthName.'-'.$date[2] . ' ' . $timePMAM}}</td>
                            <td>{{$item->implant ? $item->implant->name :"None"}}</td>
                            <td>{{$item->abutment ? $item->abutment->name :"None"}}</td>
                            <td>{{$item->code }}</td>

                            <td><span>{{$item->qty-$item->remaining_qty}}</span>/<span>{{$item->qty }}</span></td>
                            <td>

                                @if($item->status==0)
                               <span style="color:darkred">  Not Ordered </span>
                                @endif
                                   @if($item->status==1)
                                    <span style="color:lightseagreen"> Ordered by <b>{{$item->orderedBy->name_initials}}</b>  </span>
                                    @endif
                                  @if($item->status ==2)
                                        <span style="color:lawngreen">Partially Received </span>
                                        @endif
                                  @if($item->status ==3)
                                        <span style="color:green">Fully Received by {{$item->logs->first()->by->first_name}}</span>
                                   @endif
                            </td>


                        </tr>
                        <!-- ACTIONS DIALOG -->
                        <div class="modal fade" tabindex="-1" role="dialog" id="actionsDialog{{$item->id}}">

                            <input type="hidden" name="case_id" value="{{$item->id}}">
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
                                                <h5 id="doctor"><b>{{$item->case->client->name}}</b></h5>
                                            </div>
                                            <div class="form-group col-6 " style="margin-bottom: 0px">
                                                <label for="pat">Patient: </label>
                                                <h5 id="pat"><b>{{$item->case->patient_name}}</b></h5>
                                            </div>

                                        </div>
                                        <div class="form-group row" style="margin-bottom: 0px">
                                            <div class="form-group col-12 " style="margin-bottom: 0px">
                                                <label for="doctor">Abutment: </label>
                                                <h5 id="doctor">
                                                    <b> {{$item->implant ? $item->implant->name :"No Implant"}}
                                                        {{$item->abutment ? $item->abutment->name :"No Abutment" }}
                                                        {{$item->code }}</b>
                                                </h5>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="form-group row" style="margin-bottom: 0px">
                                            <div class="form-group col-6 " style="margin-bottom: 0px">
                                                <label for="doctor">Ordered by: </label>
                                                <h5 id="doctor">
                                                    <b>{{$item->orderedBy ? $item->orderedBy->fullName() : "None"}}</b>
                                                </h5>
                                            </div>
                                            <div class="form-group col-6 " style="margin-bottom: 0px">
                                                <label for="pat">Ordered On: </label>
                                                <h5 id="pat">
                                                    <b>{{$item->ordered_on ? substr($item->ordered_on,0,16):  "Not yet"}}</b></h5>
                                            </div>
                                        </div>
                                        @if ($item->status == 3 )
                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-12 " style="margin-bottom: 0px">
                                                    <label for="doctor">Received by: </label>
                                                    <h5 id="doctor">

                                                        @foreach ($item->logs as $log)
                                                            <b>{{$log->qty}}</b> Units by <b>{{$log->by->fullName()}}</b> on {{substr($log->created_at,0,16)}}
                                                       <br>
                                                        @endforeach

                                                    </h5>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer fullBtnsWidth">
                                        <div class="row" style=" margin-right: 0px; margin-left: 0px;width:100%">
                                            <div class="row">

                                                <!-------------------------
                                                -------- View Case --------
                                                -------------------------->
                                                <div class="col-6 padding5px">
                                                    <a href="{{route('view-case',['id' =>$item->case->id ,'stage' =>-2 ])}}">
                                                        <button type="button" class="btn btn-info "><i
                                                                    class="far fa-file-alt"></i> View Case
                                                        </button>
                                                    </a></div>

                                            <!-------------------------
                                                                                   ------ View Voucher ------
                                                                                   -------------------------->
                                                @if ($item->status ==0 )
                                                    <div class="col-12 padding5px">
                                                        <a href="{{route('order-abutments',$item->id)}}">
                                                            <button type="button" class="btn btn-primary "><i
                                                                        class="fa-solid fa-clipboard-check"></i> Mark as
                                                                ordered
                                                            </button>
                                                        </a></div>
                                                @endif
                                                @if ($item->status ==1 || $item->status ==2 )

                                                    <div class="col-12 padding5px">
                                                        <a href="{{route('order-abutments',$item->id)}}">
                                                            <button type="button"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal"
                                                                    data-toggle="modal"
                                                                    data-target="#receiveAbuts{{$item->id}}"
                                                            >
                                                                <i class="fa-solid fa-clipboard-check"></i>
                                                                Receive
                                                            </button>
                                                        </a></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RECEIVE ABUTMENTS DIALOG -->
                        <div class="modal fade" tabindex=" " role="dialog" id="receiveAbuts{{$item->id}}">
                            <input type="hidden" name="case_id" value="{{$item->id}}">
                            <form action="{{route('receive-abutments')}}"
                                  method="POST">
                                @csrf
                                <input type="hidden" name="abutment_id"
                                       value="{{$item->id}}">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Receive Abutments</h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Doctor: </label>
                                                    <h5 id="doctor"><b>{{$item->case->client->name}}</b></h5>
                                                </div>
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="pat">Patient: </label>
                                                    <h5 id="pat"><b>{{$item->case->patient_name}}</b></h5>
                                                </div>
                                                <div class="form-group row" style="margin-bottom: 0px;background-color:transparent">
                                                    <div class="form-group col-6 " style="margin-bottom: 0px">
                                                        <label for="doctor">Abutment: </label>
                                                        <h5 id="doctor">
                                                            <b> {{$item->implant ? $item->implant->name :"No Implant"}}
                                                                {{$item->abutment ? $item->abutment->name :"No Abutment" }}
                                                                {{$item->code }}</b>
                                                        </h5>
                                                    </div>

                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-group row" style="margin-bottom: 0px;">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Ordered by: </label>
                                                    <h5 id="doctor">
                                                        <b>{{$item->orderedBy ? $item->orderedBy->fullName() : "None"}}</b>
                                                    </h5>
                                                </div>
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="pat">Ordered On: </label>
                                                    <h5 id="pat">
                                                        <b>{{$item->ordered_on ? $item->ordered_on:  "Not yet"}}</b>
                                                    </h5>
                                                </div>
                                            </div>
                                            <hr>
                                            @if ($item->status == 2 )
                                                <div class="form-group row" style="margin-bottom: 0px;background-color:transparent">
                                                    <div class="form-group col-12 " style="margin-bottom: 0px">
                                                        <label for="doctor">Received by: </label>
                                                        <h5 id="doctor">

                                                            @foreach ($item->logs as $log)
                                                                <b>{{$log->qty}}</b> Units by <b>{{$log->by->fullName()}}</b> on {{$log->created_at}}
                                                            <br>
                                                            @endforeach

                                                        </h5>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-2 " style="margin-bottom: 0px">
                                                    <label for="doctor" style="color:red">Quantity</label>

                                                </div>
                                                <div class="form-group col-3 " style="margin-bottom: 0px">

                                                    <input type="number" name="qty" class="form=control" style="width: 100%;"
                                                           max="{{$item->remaining_qty}}">
                                                </div>
                                                <div class="form-group col-4 " style="margin-left: 5px">

                                                    <button type="submit"  class="btn btn-primary">
                                                        <i class="fa-solid fa-clipboard-check"></i> Receive
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer fullBtnsWidth">
                                            <div class="row" style=" margin-right: 0px; margin-left: 0px;width:100%">
                                                <div class="row">

                                                    <div class="col-12 padding5px">
                                                        <button type="button"
                                                                class="btn btn-secondary"
                                                                data-dismiss="modal">
                                                            Close
                                                        </button>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>


                    @endforeach
                    </tbody>

                </table>

            </div>
            <div style="text-align:right">
                {{--{{$deliveries->onEachSide(3)->links()}}--}}
            </div>
        </div>
    </div>

    </div>
    @push('js')
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <!-- Responsive and datable js -->
    <script type="text/javascript">
        $(document).ready(function () {


                                $('#datatable').DataTable({
                                    "pageLength": 25,
                                    "searching": true,
                                    "lengthChange": false,


                                    "order":  []
                                    // "scrollX":       true,
                                    //stateSave: true,
                                });

        });

        function toggleColumnVisibilty(colNumber) {

            var selector = 'td:nth-child(' + colNumber + '),th:nth-child(' + colNumber + ')';
            console.log(selector);
            $(selector).toggle();
            // Get the column API object
//                                var column = table.column($(this).attr('data-column'));
//
//                                // Toggle the visibility
//                                column.visible(!column.visible());
        }
        function toggleCheckBox(ele) {
            var $tc = $(ele).find('input:checkbox:first'),
                tv = $tc.attr('checked');

            $tc.attr('checked', !tv);
        }

        function caseDelConfirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            var clientName = ev.currentTarget.getAttribute('data-clientName');
            var patientName = ev.currentTarget.getAttribute('data-patientName');

            //console.log(urlToRedirect); // verify if this is the right URL
            swal.fire({
                title: "You sure You want to delete.. </br>" + clientName + " - " + patientName,
                text: "This will also delete related info. (invoice, photos .. etc)?",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Delete Case',
                denyButtonText: 'Cancel'
            })
                .then((willDelete) => {
                // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                if (willDelete.isConfirmed
        )
            {
                window.location = urlToRedirect;
                //swal.fire("Poof! Your imaginary file has been deleted!");
            }
        else
            {
                swal.fire("Case NOT deleted.");
            }
        })
            ;
        }
    </script>
    @endpush


@endsection

