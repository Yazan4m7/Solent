@extends('layouts.app' ,[ 'pageSlug' => "New" . $clientTitle])
@section('content')
    <form class="card" style="padding:20px" method="POST" action="{{route('new-dentist')}}">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Dentist Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Dentist Name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="dentist_name" required placeholder="Dentist Name" />
                    <span class="help-block text-muted"><small>In Arabic</small></span>
                </div>

            </div>

        </div>

        <br/>
        <hr style="margin-top: 0;">
        <div class="row">

        <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
            <div class="col-md-12 col-xs-12"><label >Phone Number:</label></div>
            <div class="col-md-12 col-xs-12">
                <input class="form-control" type="text" name="phone_number" placeholder="Phone Number" required/>
                <span class="help-block text-muted"><small>07xxxxxxxx</small></span>
            </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
            <div class="col-md-12 col-xs-12"><label >Address:</label></div>
            <div class="col-md-12 col-xs-12">
                <input class="form-control" type="text" name="address" placeholder="Address" required/>
                <span class="help-block text-muted"><small>Town/ Hospital/ Neighborhood name</small></span>
            </div>

        </div>
        </div>
        <br>
        <h6  class="kt-portlet__head-title">
            <i class="fa fa-minus-square"  style="width:3%"></i> Dentist Discount:
        </h6>
        <hr>
        <div class="form-group">
            <div id="kt_repeater_1">
                <div class="form-group form-group-last row" id="kt_repeater_1">
                    <div data-repeater-list="repeat" class="col-lg-10 col-md-12">
                        <div data-repeater-item class="form-group row align-items-center">
                            <div class="col-sm-3 col-6">
                                <div class="kt-form__group--inline">
                                    <div class="kt-form__label">
                                        <label>Material:</label>
                                    </div>
                                    <div class="kt-form__control">
                                        <select class="form-control" id="material" name="material">
                                            @foreach($materials as $m)
                                                <option value="{{$m->id}}">
                                                    {{$m->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none kt-margin-b-10"></div>
                            </div>
                            <div class="col-sm-2 col-6">
                                <div class="kt-form__group--inline">
                                    <div class="kt-form__label">
                                        <label>Discount:</label>
                                    </div>
                                    <div class="kt-form__control">
                                        <input type="number" class="form-control" name="discount" min="0" max="100">
                                    </div>
                                </div>
                                <div class="d-md-none kt-margin-b-10"></div>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">
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
                            <div class="col-sm-3">
                                <button data-repeater-delete  class="btn btn-danger  btn-sm" type="button" value="Delete" style="margin-top:10px"> <i class="fa fa-trash"></i></span> DELETE</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-last row">

                    <div class="col-lg-4">
                        <a href="javascript:" data-repeater-create="" class="btn btn-info  btn-sm" id="addJobBtn">
                            <i class="fa fa-plus-square"></i> Add
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <br/>
        <div class=" form-group ">
            <div class="form-group mb-0">
                <div>
                    <button type="submit" class="btn btn-info waves-effect waves-light">
                        Submit
                    </button>
                    <button type="reset" class="btn btn-secondary waves-effect m-l-5">
                        Cancel
                    </button>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('js')
    <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>
    <script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('form').parsley();
        });

    </script>
    <script type="text/javascript" src="{{asset('assets/plugins/parsleyjs/dist/parsley.min.js')}}"></script>
@endpush
