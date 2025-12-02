@extends('layouts.app' ,[ 'pageSlug' => 'Edit ' . $device ])
@section('content')
    <form  method="POST" action="{{route('edit-device')}}" class="card">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Device Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Device name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input  value="{{$device->id}}" type="hidden" name="device_id" />
                    <input class="form-control" value="{{$device->name}}" type="text" name="device_name" required placeholder="Device name"/>
                    <span class="help-block text-muted"><small></small></span>
                </div>

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Device Type:</label></div>
                <div class="col-md-12 col-xs-12">
                    <select class="form-control selectpicker" id="dev" name="device_type">
                        <option value="1" {{$device->type == '1' ? 'selected' : '' }}>3D Printer</option>
                        <option value="2" {{$device->type == '2' ? 'selected' : '' }}>Milling Machine</option>
                        <option value="3" {{$device->type == '3' ? 'selected' : '' }}>Furnace</option>
                    </select>
                </div>
            </div>
        </div>

        <br/>

        <hr style="margin-top: 0;">



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
    <script type="text/javascript">

        $(document).ready(function() {
            $('form').parsley();
        });

    </script>
    <script type="text/javascript" src="{{asset('assets/plugins/parsleyjs/dist/parsley.min.js')}}"></script>
@endpush
