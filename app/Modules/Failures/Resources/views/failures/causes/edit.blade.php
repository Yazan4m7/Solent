@extends('layouts.app' ,[ 'pageSlug' => 'Edit ' . $failureCause  ])
@section('content')
    <form  method="POST" action="{{route('edit-f-cause')}}" class="card">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Cause text:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input  value="{{$cause->id}}" type="hidden" name="cause_id" />
                    <input class="form-control" value="{{$cause->text}}" type="text" name="cause_text" required placeholder="Failure Cause Text"/>
                    <span class="help-block text-muted"><small></small></span>
                </div>

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">


            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">

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
    <script src="{{asset('assets/js/fontawesome-browser.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $.fabrowser();
        });

    </script>
@endpush
