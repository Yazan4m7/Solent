@extends('layouts.app' ,[ 'pageSlug' => 'New ' . $failureCause ])
@section('content')
    <form method="POST" action="{{route('new-f-cause')}}" class="card" xmlns="http://www.w3.org/1999/html">
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
                    <input class="form-control" type="text" name="cause_text" required placeholder="Cause text">

                    <span class="help-block text-muted"><small></small></span>
                </div>

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">

        </div>

        </div>


        <br/>



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



    </script>

@endpush
