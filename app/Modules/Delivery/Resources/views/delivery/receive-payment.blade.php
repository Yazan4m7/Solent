@extends('layouts.app' ,[ 'pageSlug' =>'Receive Payments'])

@section('content')


       <form  class="kt-form" method="GET" action="{{route('payments-with-collectors')}}">

                <div class="row" style="padding-left: 10px;padding-top: 10px">

                    <div class="col-lg-3 col-md-3 mb-3">
                        <div class="kt-subheader__search" style="">
                            <label>From:</label>

                            <input class="form-control SDTP" name="from"  type="text"   value="{{$from ?? ''}}" required readonly/>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 mb-3">
                        <div class="kt-subheader__search" style="">
                            <label>To:</label>
                            <input class="form-control SDTP" name="to"  type="text"   value="{{$to ?? ''}}" required readonly/>

                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-3">

                        @if(isset($clients))
                        <div class="kt-subheader__search" style="width:100%">
                            <label>Doctor:</label>
                            <select style="width:100%"  class="selectpicker form-control clearOnAll" multiple name="doctor[]" id="doctor"  data-live-search="true" title="All" data-hide-disabled="true">

                                <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>All</option>
                                @foreach($clients as $d)
                                    <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        @endif
                    </div>
                    <div class="col-lg-3 col-md-3 mb-3">

                        @if(isset($collectors))
                            <div class="kt-subheader__search" style="width:100%">
                                <label>Collector:</label>
                                <select style="width:100%"  class="selectpicker form-control clearOnAll" multiple name="collectors[]" id="collectors"  data-live-search="true" title="All" data-hide-disabled="true">

                                    <option value="all" {{(isset($selectedCollectors) && $selectedCollectors== 'all') ? 'selected' : ''}}>All</option>
                                    @foreach($collectors as $d)
                                        <option value="{{$d->id}}" {{(isset($selectedCollectors) && in_array($d->id ,$selectedCollectors)) ? 'selected' : ''}}>{{$d->first_name . ' ' . $d->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                        @endif
                    </div>
                    <div class="col-lg-3 col-md-3 mb-3">

                        <div class="kt-subheader__search" style="width:100%">
                            <label>&nbsp; &nbsp; </label>

                            <div class="kt-form__actions">
                                <button type="submit" class="btn btn-primary">Submit</button>

                            </div>
                        </div>

                    </div>


        </form>
        <div class="col-lg-12 col-sm-12">
            <div class=" m-b-30">
                <div class=" table-responsive">
                    <h5 class="header-title">Total Amount</h5>
                    <h2 style=""><span style="font-weight: bold;color:#a13030">{{number_format($payments->sum('amount'))}}</span> <span style="font-weight: bold;font-size:18px;">JOD</span></h2>
                    <p class="text-muted"></p>
                    <div class="table-odd">
                        <div id="datatable_wrapper" class=""><div class="row"><div class="col-sm-12">
 <table id="datatable" class="table sunriseTable order-column  display nowrap compact cell-border dataTable no-footer" role="grid" aria-describedby="datatable_info">
                                        <thead>
                                        <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 50.93px;">ID</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Collector</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 240px;">Doctor</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 148.32px;">Amount</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 126.035px;">Type</th>
                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 160.664px;">Paid on</th>


                                        </tr>
                                        </thead>

                                        <tbody>
                                        @php
                                            $permissions = Cache::get('user'.Auth()->user()->id);
                                        @endphp
                                        @foreach($payments as $payment)
                                            <tr role="row" class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$payment->id}}">
                                                <td class="sorting_1">{{$payment->id}}</td>
                                                <td>{{$payment->collectorUserRecord->name_initials }}</td>
                                                <td>{{$payment->client->name}}</td>
                                                <td>{{$payment->amount}} JOD</td>
                                                <td>{{$payment->from_bank ? $payment->notes : "Cash"}}</td>

                                                <td>{{substr($payment->created_at,0,16) }}</td>



                                            </tr>



                                            <div class="modal" tabindex="-1" role="dialog" id="actionsDialog{{$payment->id}}">
                                                <input type="hidden" name="case_id" value="{{$payment->id}}">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Payment Actions</h5>

                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group row" style="margin-bottom: 0px">
                                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                    <label for="doctor">Doctor: </label>
                                                                    <h5 id="doctor"><b>{{$payment->client->name}}</b></h5>
                                                                </div>
                                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                    <label for="pat">Price: </label>
                                                                    <h5 id="pat"><b>{{$payment->amount}}</b></h5>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="margin-bottom: 0px">
                                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                    <label for="doctor">Collector: </label>
                                                                    <h5 id="doctor"><b>{{$payment->collectorUserRecord->name_initials }}</b></h5>
                                                                </div>
                                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                    <label for="pat">Paid On: </label>
                                                                    <h5 id="pat"><b>{{substr($payment->created_at,0,16) }}</b></h5>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="margin-bottom: 0px">
                                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                                    <label for="doctor">Type: </label>
                                                                    <h5 id="doctor"><b>{{$payment->from_bank ? $payment->notes : "Cash"}}</b></h5>
                                                                </div>

                                                            </div>
                                                            <hr>

                                                        </div>
                                                        <div class="modal-footer fullBtnsWidth" >
                                                            <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">


                                                                <div class="row">
                                                                    <!-----------------------
                                                                     -------------------------->
                                                                    @if(!isset($payment->recieved_on))
                                                                    <div class="col-12 padding5px" >
                                                                        <a href="{{route('receive-payment',$payment->id )}}">
                                                                            <button type="button" class="btn btn-success "><i class="fa-solid fa-hand-holding-dollar"></i> Receive </button>
                                                                        </a></div>
                                                                    @endif
                                                                    @if(Auth()->user()->is_admin)
                                                                        <div class="col-12 padding5px" >
                                                                            <a  onclick="confirmation(event)"  href="{{route('delete-payment',$payment->id )}}"  style="color:red">
                                                                                <button type="button" class="btn btn-danger "><i class="fa-solid fa-trash-can"></i> Delete Payment</button>
                                                                            </a>
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

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')

    <!-- Responsive and datatable js -->
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatable').DataTable(
                {
                    "pageLength": 25,
                    "searching": false,
                    "lengthChange": false,
                    "order": [[ 4, "desc" ]],
                }
            );
        } );
        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal.fire({
                title: "Are you sure?",
                text: "Once deleted, Doctor balance will be updated accordingly!",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: 'Delete Payment',
                denyButtonText: `Cancel`,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete.isConfirmed) {
                        window.location = urlToRedirect;
                        //swal.fire("Poof! Your imaginary file has been deleted!");
                    } else {
                       swal.fire("Payment not deleted.");
                    }
                });
        }
    </script>
@endpush
