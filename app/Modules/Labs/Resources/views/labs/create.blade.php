@extends('layouts.app' ,[ 'pageSlug' => 'New Lab'])
@section('content')
    <form  method="POST" action="{{route('new-lab')}}" class="card">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Lab Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Lab name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="lab_name" required placeholder="Lab Name"/>
                    <span class="help-block text-muted"><small></small></span>
                </div>
            </div>
                <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                    <div class="col-md-12 col-xs-12"><label >Lab Phone:</label></div>
                    <div class="col-md-12 col-xs-12">
                        <input class="form-control" type="text" name="lab_phone"  placeholder="Lab Phone Number"/>
                        <span class="help-block text-muted"><small>Optional</small></span>
                    </div>

                </div>

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Lab Address:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="lab_address"  placeholder="Lab Address"/>
                    <span class="help-block text-muted"><small>Optional</small></span>
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
