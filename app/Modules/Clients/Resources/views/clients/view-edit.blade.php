@extends('layouts.app' ,[ 'pageSlug' => 'Edit ' .$clientTitle . ' Account' ])


@section('body-header')
    <!-- begin:: Content Head -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    Edit doctor
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
				<span class="kt-subheader__desc" id="kt_subheader_total">
					Enter doctor details and submit </span>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content Head -->
@endsection

@section('content')

    <!--begin::Portlet-->
    <div class="kt-portlet card " style="padding:15px">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Edit Doctor
                </h3>
            </div>
        </div>

        <!--begin::Form-->
        <form class="kt-form" method="POST" action="{{route('client-update')}}">
            <input type="hidden" name="id" value="{{$user->id}}">
            @csrf
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label for="name" class="col-2 col-form-label">Doctor full name</label>
                    <div class="col-10">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter the doctors full name" value="{{$user->name}}">
                    </div>
                    @if ($errors->has('first_name'))
                        <span class="help-block" style="color: red">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label for="address" class="col-2 col-form-label">Doctor Address</label>
                    <div class="col-10">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter the doctors address" value="{{$user->address}}">
                    </div>
                    @if ($errors->has('address'))
                        <span class="help-block" style="color: red">{{ $errors->first('address') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label for="example-tel-input" class="col-2 col-form-label">Personal Phone</label>
                    <div class="col-10">
                        <input class="form-control" type="tel" name="phone" id="example-tel-input" value="{{$user->phone}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="example-tel-input" class="col-2 col-form-label">Clinic Phone</label>
                    <div class="col-10">
                        <input class="form-control" type="tel" name="clinic_phone" id="example-tel-input" value="{{$user->clinic_phone}}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-group form-group-last row">
                        <div data-repeater-list="repeat" class="col-lg-10">
                            @foreach($user->discounts as $dis)
                                <input type="hidden" name="ids[]" value="{{$dis->id}}">
                                <div data-repeater-item class="form-group row align-items-center">
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Material:</label>
                                            </div>
                                            <div class="kt-form__control">
                                                <select class="form-control" id="materials" name="old_material_{{$dis->id}}[]">
                                                    @foreach($materials as $m)
                                                        <option value="{{$m->id}}" {{$dis->material_id == $m->id ? 'selected' : ''}}>
                                                            {{$m->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-md-none kt-margin-b-10"></div>
                                    </div>
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Discount:</label>
                                            </div>
                                            <div class="kt-form__control">
                                                <input type="number" class="form-control" name="old_discount_{{$dis->id}}[]" min="0" value="{{$dis->discount}}">
                                            </div>
                                        </div>
                                        <div class="d-md-none kt-margin-b-10"></div>
                                    </div>
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Style:</label>
                                            </div>
                                            <div class="kt-radio-inline">
                                                <label class="kt-radio">
                                                    <input type="radio" name="old_type_{{$dis->id}}[]" value="0" {{$dis->type == 0 ? 'checked' : ''}}> Fixed
                                                    <span></span>
                                                </label>
                                                <label class="kt-radio">
                                                    <input type="radio" name="old_type_{{$dis->id}}[]"  value="1" {{$dis->type == 1 ? 'checked' : ''}}> Percentage
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div id="kt_repeater_1">
                        <div class="form-group form-group-last row" id="kt_repeater_1">
                            <div data-repeater-list="repeat" class="col-lg-10">
                                <div data-repeater-item class="form-group row align-items-center">
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Material:</label>
                                            </div>
                                            <div class="kt-form__control">
                                                <select class="form-control" id="material" name="material">
                                                    @foreach($materials as $m)
                                                        @if(!$user->discounts->contains('material_id', $m->id))
                                                            <option value="{{$m->id}}">
                                                                {{$m->name}}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-md-none kt-margin-b-10"></div>
                                    </div>
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Discount:</label>
                                            </div>
                                            <div class="kt-form__control">
                                                <input type="number" class="form-control" name="discount" min="0">
                                            </div>
                                        </div>
                                        <div class="d-md-none kt-margin-b-10"></div>
                                    </div>
                                    <div class="col-2">
                                        <div class="kt-form__group--inline">
                                            <div class="kt-form__label">
                                                <label>Style:</label>
                                            </div>
                                            <div class="kt-radio-inline">
                                                <label class="kt-radio">
                                                    <input type="radio" name="type" value="0"> Fixed
                                                    <span></span>
                                                </label>
                                                <label class="kt-radio">
                                                    <input type="radio" checked="checked" name="type"  value="1"> Percentage
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <a href="javascript:" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                            <i class="la la-trash-o"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-last row">
                            <label class="col-lg-2 col-form-label"></label>
                            <div class="col-lg-4">
                                <a href="javascript:" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand" id="addLoL">
                                    <i class="la la-plus"></i> Add
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#openingBalanceModal">
                        Insert/Modify Opening Balance
                    </button>
                </div>
            </div>
        </form>

        <!--end::Form-->
    </div>

    <!-- Opening Balance Modal -->
    <div class="modal fade" id="openingBalanceModal" tabindex="-1" role="dialog" aria-labelledby="openingBalanceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="openingBalanceModalLabel">Insert/Modify Opening Balance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('clients.set-opening-balance') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="client_id" value="{{ $user->id }}">
                        <div class="form-group">
                            <label for="opening_balance">Opening Balance</label>
                            <input type="number" class="form-control" id="opening_balance" name="opening_balance" value="{{ $openingBalanceInvoice->amount ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="opening_balance_date">Date</label>
                            <input type="date" class="form-control" id="opening_balance_date" name="opening_balance_date" value="{{ $openingBalanceInvoice ? \Carbon\Carbon::parse($openingBalanceInvoice->date_applied)->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- end:: Content -->
@endsection
@push('js')
    <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>
    <script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script>
    <script>
        $('select[name="position"]').on('change', function() {
            var selected = $(this).find('option:selected');
            var extra = selected.data('content');
            if (extra == 'B') {
                $('#TypeB').removeAttr('hidden')
            } else {
                $('#TypeB').prop('hidden', true)
            }
            console.log(extra)
        })
    </script>
@endpush
