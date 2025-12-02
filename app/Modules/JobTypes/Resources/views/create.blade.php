@extends('layouts.app' ,[ 'pageSlug' => 'New Job Type' ])
@section('content')
    <form  method="POST" action="{{route('new-job-type')}}" class="card">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Job Type Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Job type name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="jobtype_name" required placeholder="Job Type name"/>
                    <span class="help-block text-muted"><small>E.g. : Crown, Veneer</small></span>
                </div>

            </div>

        </div>

        <br/>

        <hr style="margin-top: 0;">


        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Section:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="teeth">
                        <input type="radio" id="teeth" name="teeth_or_jaw" value="0">
                        <i class="fa"></i>
                        Teeth
                    </label>
                </div>
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="jaw">
                        <input type="radio" id="jaw" name="teeth_or_jaw" value="1" required>
                        <i class="fa"></i>
                        Jaw
                    </label>
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
    <script type="text/javascript">

        $(document).ready(function() {
            $('form').parsley();
        });

    </script>
    <script type="text/javascript" src="{{asset('assets/plugins/parsleyjs/dist/parsley.min.js')}}"></script>
@endpush
