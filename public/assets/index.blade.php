@extends('layouts.app' ,[ 'pageSlug' => $clientTitle .'s List' ])

@section('content')
<style>
.dropdown-toggle::after {
    display: inline-block !important;
}
    .dropdown-menu{
        color:inherit;
    }
.modal-footer{
    padding: 0 !important;
}
@media screen and (max-width: 768px) {
    table {
        table-layout: fixed;
    }
}
</style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <form class="kt-form" method="GET" action="{{route('clients-index')}}">
            <div class="row">
                <div class="col-sm-3">
                    @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                        <label for="from">To:</label>
                        <input class="form-control SDTP" name="from" type="text" value="{{$from ?? ''}}"
                               required readonly/>
                    @endif
                </div>
                <div class="col-sm-3">
                    <label>Doctor:</label>
                    <select style="width:100%" class="selectpicker form-control clearOnAll" multiple
                            name="doctor[]" id="doctor"  data-live-search="true"
                            title="All" data-hide-disabled="true">
                        <option value="all" {{(isset($selectedClients) && in_array('all',$selectedClients) ? 'selected' : '')}}>All</option>
                        @foreach($allClients as $d)
                            <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                        <label> &nbsp; </label>
                        <a href="{{route('new-dentist-view')}}">
                            <button type="button" class="btn btn-secondary btn-lg btn-block"><i class="fa fa-plus-circle" style=""></i> Add
                                Doctor
                            </button>
                        </a>
                    @endif
                </div>
                <div class="col-sm-3">
                    <label> &nbsp; </label>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3" >
                    @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                        <h2 style="margin:0"><span style="font-weight: bold;color:#a13030">{{number_format($totalBalance)}}</span> <span
                                    style="font-weight: bold;font-size:18px;">JOD</span></h2>
                    @endif
            </div>
                <div class="col-md-6"> </div>

                <div class="col-md-3">
                    @if( Auth()->user()->is_admin)
                        <label> &nbsp; </label>
                        <a href="{{route('mobile-stats-configs')}}">
                            <button type="button" class="btn btn-secondary btn-lg btn-block">
                                 Mobile Access & Stats
                            </button>
                        </a>
                    @endif
                </div>
            </div>
            </form>


            <hr>
                    <div class="">
                        <table class="globalTable nowrap compact stripe sunriseTable " id="my-table">
                            <thead>
                            <tr >
                                <th  style="font-weight: bold">ID</th>
                                <th  style="font-weight: bold">Name</th>
                                <th  style="font-weight: bold">Personal Phone</th>
                                <th  style="font-weight: bold">Clinic Phone</th>
                                @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                <th>Balance</th>
                                @endif


                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr id="{{$client->id}}" class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$client->id}}">
                                    <td>
                                        <span class="tabledit-span tabledit-identifier">{{$client->id}}</span>
                                    </td>
                                    <td class="tabledit-view-mode"><span
                                                class="tabledit-span">{{$client->name}}</span><input
                                                class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled=""></td>
                                    <td class="tabledit-view-mode"><span
                                                class="tabledit-span">{{$client->phone}}</span><input
                                                class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled=""></td>
                                    <td class="tabledit-view-mode"><span
                                                class="tabledit-span">{{$client->clinic_phone}}</span><input
                                                class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled=""></td>
                                    @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                    <td class="tabledit-view-mode"><span
                                                class="tabledit-span">{{isset($from) ? $client->balanceAt($from) : $client->balance}}</span><input
                                                class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="Doe" style="display: none;" disabled=""></td>
                                        @endif

                                </tr>
                                @if(($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                                <div class="modal" tabindex="-1" role="dialog" id="myModal{{$client->id}}">
                                    <form action="{{route('new-payment')}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$client->id}}">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">New Payment balance</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4 style="color:#ff0000"><b>{{$client->name}}</b></h4>
                                                    <label>Payment amount</label>
                                                    <input type="number" class="form-control" name="amount" required>
                                                    <br/>
                                                    <label>Payment type:</label> <br/>

                                                    <input type="radio" id="cash{{$client->id}}"
                                                           onclick="paymentTypeChange({{$client->id}});"
                                                           name="payment_type" value="cash">
                                                    <label for="cash{{$client->id}}">دفعة نقدية</label><br>
                                                    <input type="radio" id="cheque{{$client->id}}"
                                                           onclick="paymentTypeChange({{$client->id}});"
                                                           name="payment_type" value="cheque">
                                                    <label for="cheque{{$client->id}}">شيك بنكي</label><br>
                                                    <input type="radio" id="transfer{{$client->id}}"
                                                           onclick="paymentTypeChange({{$client->id}});"
                                                           name="payment_type" value="transfer">
                                                    <label for="transfer{{$client->id}}">حوالة بنكية/ كليك</label><br>
                                                    <br/>
                                                    <div id="chequeDetailsInputs{{$client->id}}" style="display:none">
                                                        <label>Bank:</label>

                                                        <div class="kt-form__control">
                                                            <select class="form-control" id="bank" name="bank_id">
                                                                @foreach($banks as $bank)
                                                                    <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <br/>
                                                        <label>Cheque number:</label>
                                                        <input type="text" class="form-control" name="chequeNumber">
                                                        <br/>
                                                    </div>
                                                    <label>Extra details (Optional):</label>
                                                    <textarea name="note" class="form-control"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @endif
                                @if( Auth()->user()->is_admin)
                                    <div class="modal" tabindex="-1" role="dialog" id="accountDiscount{{$client->id}}">
                                        <form action="{{route('account-discount')}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$client->id}}">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Doctor balance</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label>Discount amount</label>
                                                        <input type="number" class="form-control" name="discountAmount" required>
                                                        <br/>
                                                        <label>Date of discount:  :</label>
                                                        <input type="datetime-local" name="discount_date" class="form-control"></input>
                                                        <br/>

                                                        <label>Details ( How it appears on account statement) :</label>
                                                        <input type="text" name="discount_title" class="form-control"></input>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <div class="modal" tabindex="-1" role="dialog" id="actionsDialog{{$client->id}}">

                                    <input type="hidden" name="case_id" value="{{$client->id}}">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Doctor Account</h5>

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="form-group row" style="margin-bottom: 0px">
                                                    <div class="form-group col-6 " style="margin-bottom: 0px">
                                                        <label for="doctor">Doctor: </label>
                                                        <h5 id="doctor"><b>{{$client->name}}</b></h5>
                                                    </div>
                                                    <div class="form-group col-6 " style="margin-bottom: 0px">
                                                        <label for="pat">Balance: </label>
                                                        <h5 id="pat"><b>{{isset($from) ? $client->balanceAt($from) : $client->balance}}</b></h5>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group row">


                                            </div>
                                            <div class="modal-footer fullBtnsWidth" >
                                                <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">

                                                        <div class="row">
                                                            <!-------------------------
                                                                   ------ View Voucher ------
                                                                   -------------------------->
                                                            {{--<div class="col-6 padding5px" >--}}
                                                                {{--<a  href="{{route('view-voucher',$case->id)}}">--}}
                                                                    {{--<button type="button" class="btn btn-info "><i--}}
                                                                                {{--class="fas fa-print"></i> View Voucher </button>--}}
                                                                {{--</a></div>--}}
                                                            @if(($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)

                                                                <div class="col-6 padding5px" >
                                                                    <a href="{{route('client-statement-admin', $client->id)}}">
                                                                        <button type="button" class="btn btn-primary ">
                                                                            Account Statement</button></a>
                                                                </div>

                                                                <div class="col-6 padding5px" >
                                                                    <a href="{{route('client-view-edit',['id' =>$client->id])}}">
                                                                        <button type="button" class="btn btn-danger ">
                                                                            Edit Record</button></a>
                                                                </div>

                                                            @endif
                                                            @if(($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                                                                <div class="col-6 padding5px" >
                                                                    <a data-toggle="modal" data-target="#myModal{{$client->id}} "
                                                                       >
                                                                        <button type="button" class="btn btn-warning " data-dismiss="modal" >
                                                                            Add a payment </button></a>
                                                                </div>


                                                            @endif
                                                            @if( Auth()->user()->is_admin)
                                                                <div class="col-6 padding5px" >
                                                                <a href="{{route('dentist-cases',['id' =>$client->id])}}">
                                                                    <button type="button" class="btn btn-info ">
                                                                    View Cases </button></a>
                                                                </div>
                                                                <div class="col-6 padding5px" >
                                                                    <a  href="{{route('dentist-invoices',['id' =>$client->id])}}">
                                                                    <button type="button" class="btn btn-info ">
                                                                        View Invoices </button></a>
                                                                </div>
                                                                <div class="col-6 padding5px" >
                                                                    <a href="{{route('dentist-payments',['id' =>$client->id])}}">
                                                                        <button type="button" class="btn btn-info ">
                                                                            View Payments </button></a>
                                                                </div>
                                                                <div class="col-6 padding5px" >
                                                                    <a data-toggle="modal" data-target="#accountDiscount{{$client->id}} ">
                                                                    <button type="button" class="btn btn-danger " data-dismiss="modal" >
                                                                            Create a discount </button></a>
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

@endsection
@push('js')
    <script>
        function paymentTypeChange(id) {
            if (document.getElementById('cheque'.concat(id)).checked) {
                document.getElementById('chequeDetailsInputs'.concat(id)).style.display = 'block';
            }
            else document.getElementById('chequeDetailsInputs'.concat(id)).style.display = 'none';

        }
    </script>
@endpush