@extends('layouts.app' ,[ 'pageSlug' => 'Edit Material' ])
@section('content')
    <form  method="POST" class="card" style="padding:20px"  action="{{route('edit-material')}}">
        @csrf
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6  class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Material Info:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="row">

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Material Name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input  value="{{$material->id}}" type="hidden" name="mat_id" />
                    <input class="form-control" value="{{$material->name}}" type="text" name="mat_name" required placeholder="Material Name"/>
                    <span class="help-block text-muted"><small>E.g. : Zircon, E.max ..etc</small></span>
                </div>
F
            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Price:</label></div>
                <div class="col-md-12 col-xs-12"><input class="form-control" value="{{$material->price}}" type="number" name="price"  required placeholder="Price (JOD)" />
                    <span class="help-block text-muted"><small>in JOD</small></span>
                </div>
            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Job Type:</label></div>
                <div class="col-md-12 col-xs-12">

                    <select class="select selectpicker" id="jobTypes" name="jobTypes[]" multiple required>
                        @foreach($jobTypes as $type)
                            <option value="{{$type->id}}"  {{ in_array($type->id,$matJobTypes)  ? 'selected' : '' }}  >{{$type->name}}</option>
                        @endforeach
                    </select>
                    <span class="help-block text-muted"><small></small></span>
                </div>
            </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Count as unit:</label></div>
                <div class="col-md-12 col-xs-12">
                    <label class="switch">
                        <input type="checkbox" name="count_as_unit" {{$material->count_as_unit == 1 ? 'checked' : ''}}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

        </div>
        <br/>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6 class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Stages:
                </h6>
            </div>
        </div>
        <hr style="margin-top: 0;">
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Design:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="design">
                        <input type="checkbox" id="design" name="design" value="1"  {{$material->design ? 'checked' : '' }} >
                        <i class="fa"></i>

                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Manufacturing:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="noMilling">
                        <input type="radio" id="noMilling" name="manufacturing" value="0"{{ !$material->mill ? 'checked' : '' }}   >
                        <i class="fa"></i>
                        None
                    </label>
                </div>
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="milling">
                        <input type="radio" id="milling" name="manufacturing" value="2" {{$material->mill ? 'checked' : '' }}  >
                        <i class="fa"></i>
                        Milling
                    </label>
                </div>
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="3dPrinting">
                        <input type="radio" id="3dPrinting" name="manufacturing" value="3" required {{$material->print_3d ? 'checked' : '' }} >
                        <i class="fa"></i>
                        3D Printing
                    </label>
                </div>

            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Furnace:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="furnace0">
                        <input type="radio" id="furnace0" name="furnace" value="0" {{!$material->sinter_furnace ? 'checked' : '' }}>
                        <i class="fa"></i>
                        None
                    </label>
                </div>
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="furnace1">
                        <input type="radio" id="furnace1" name="furnace" value="4" {{$material->sinter_furnace ? 'checked' : '' }} >
                        <i class="fa"></i>
                        Sintering Furnace
                    </label>
                </div>
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="furnace2">
                        <input type="radio" id="furnace2" name="furnace" value="5" required  {{$material->press_furnace ? 'checked' : '' }} >
                        <i class="fa"></i>
                        Press Furnace
                    </label>
                </div>

            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Finishing:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="finishing">
                        <input type="checkbox" id="finishing" name="finishing" value="6"  {{$material->finish ? 'checked' : '' }}>
                        <i class="fa"></i>

                    </label>
                </div>


            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Quality Control:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="qc">
                        <input type="checkbox" id="qc" name="qc" value="7"  {{$material->qc ? 'checked' : '' }}>
                        <i class="fa"></i>

                    </label>
                </div>


            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 my-1 control-label">Delivery:</label>
            <div class="col-md-9">
                <div class="form-check-inline my-1">
                    <label class="cr-styled" for="delivery">
                        <input type="checkbox" id="delivery" name="delivery" value="8"  {{$material->delivery ? 'checked' : '' }}>
                        <i class="fa"></i>

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

@section('scripts')
    <script type="text/javascript">

        $(document).ready(function() {

        });

    </script>
    <script type="text/javascript" src="{{asset('assets/plugins/parsleyjs/dist/parsley.min.js')}}"></script>
@endsection
