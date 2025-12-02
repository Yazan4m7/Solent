@extends('layouts.app' ,[ 'pageSlug' => 'New Tag' ])
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/GianlucaChiarani/FontAwesomeBrowser@0.3/fontawesome-browser.css" rel="stylesheet">
    <style>
        .fa-browser-container{z-index:999999}
    </style>
    <form  method="POST" action="{{route('new-tag')}}" class="card">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Tag Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Tag text:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="tag_text" required placeholder="Tag name"/>
                    <span class="help-block text-muted"><small></small></span>
                </div>

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Tag color:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input type="color" id="cc" name="tag_color" />
                    <span class="help-block text-muted"><small></small></span>
                </div>

            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Tag icon:</label></div>
                <div class="col-md-12 col-xs-12">
            <div>
                <input class="form-control" name ="tag_icon" type="text" placeholder="Select icon" data-fa-browser  autocomplete="off" />

            </div>
        </div>

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
    <script src="{{asset('assets/js/fontawesome-browser.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $.fabrowser();
        });

    </script>

@endpush
