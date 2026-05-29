@extends('layouts.app' ,[ 'pageSlug' => $editCase])

@section('content')
    <link rel="stylesheet" href="{{asset('assets/css/lightgallery.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/jquery.imagesloader.css')}}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/lightgallery/1.3.9/css/lightgallery.min.css" rel="stylesheet">


    <style>
        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }
        .hidden{
            display:none;
        }
        .noteHeader{color: #525252; font-size: 12px;}
        .noteText{color:black;font-weight: 500;}

    </style>



    <form style="padding:10px" class="kt-form card" method="POST" enctype="multipart/form-data"   action="#">
    @csrf
    <div>

    <!-- CASE INFO -->
        <div class="row">
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Doctor:</label></div>
                <div class="col-md-12 col-xs-12">


                    <select  class="selectpicker"  name="doctor"  data-container="body" data-live-search="true"  title="Select a doctor" disabled  >
                        @foreach($clients as $client)
                            <option value="{{$client->id}}" {{$case->client->id == $client->id ? "selected" : ""}} >{{$client->name}}</option>
                        @endforeach

                    </select>

                </div> </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Patient name:</label></div>
                <div class="col-md-12 col-xs-12"><input class="form-control" type="text" name="patient_name" value="{{$case->patient_name}}" disabled /></div>
            </div>

            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-6 col-xs-12"><label>Case ID:</label></div>
                <div class="col-md-12 col-xs-12">

                    <label >{{$case->case_id}}</label>

                </div>

            </div>


        </div>

<br/>
        <div class="row">

            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Delivery Date:</label></div>
                <div class="col-md-12 col-xs-12"><input class="form-control"  name="delivery_date" type="datetime-local" value="{{str_replace(' ', 'T',$case->initial_delivery_date)}}" disabled/></div>
            </div>
            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Tags:</label></div>
                <div class="col-md-12 col-xs-12">
                    <select class="select selectpicker" name="tags[]" multiple data-mdb-placeholder="Tags" multiple disabled>
                        @foreach($tags as $tag)
                            <option style="color:{{$tag->color}}" value="{{$tag->id}}" {{in_array($tag->id ,$tagsAsArray) ? 'selected' : ''}}>{{$tag->text}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-4 col-xs-6 col-l-2 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Impression Type:</label></div>
                <div class="col-md-12 col-xs-12"> <select  class="form-control" name="impression_type" type="text"  data-container="body" data-live-search="true" title="Select impression" data-hide-disabled="true" disabled >

                        @foreach($impressionTypes as $impression)
                            <option value="{{$impression->id}}">
                                {{$impression->name}}
                            </option>
                        @endforeach
                    </select></div>
            </div>
        </div>


        <!-- JOB INFO ICON-->
        <br>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h5 class="kt-portlet__head-title">
                    <i class="fa  fa-suitcase"  style="width:3%"></i> Job information
                </h5>
            </div>
        </div>
        <hr>


        <!-- JOBS REPEATER -->

        <div  id="kt_repeater_1" style="padding-left: 15px; padding-right: 15px">
            <div  data-repeater-list="repeat">
                <div data-repeater-item>
                    <div class="form-group form-group ">
                        <div data-repeater-list="repeat" class="col-12">
                            @php
                            if($stage == -2 || $stage >5)
                            $jobs = $case->jobs;
                            else
                            $jobs = $case->jobs->where('stage',$stage);
                            @endphp

                            @foreach($jobs as $job)

                                @php
                                    $unit = explode(', ',$job->unit_num);
                                @endphp
                            <div data-repeater-item class="form-group row align-items-center row-item" style="border: 1px solid #ccc;border-radius: 16px;padding:5px">


                                <div class="col-md-2"> <div class="">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single"></label>
                                        </div>
                                        <input type="hidden" name="units" id="units" class="hiddenUnitsInput" value="{{$job->unit_num}}" >
                                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#unitsDialog" name="openDialogBtn" onclick="preOpenDialog(this)" disabled>
                                            {{$job->unit_num}}
                                        </button>

                                    </div>

                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Job type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="jobType" name="jobType" onchange="jobTypeChanged(this)" disabled>

                                                @foreach($types as $type)
                                                    <option value="{{$type->id}}" {{$type->id == $job->type ? 'selected' : ''}}>{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label>Material:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="material_id" name="material_id" disabled>

                                                @foreach($materials as $m)
                                                    <option value="{{$m->id}}" {{$job->material_id == $m->id ? 'selected' : ''}}>
                                                        {{$m->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label>Color:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="color" name="color" disabled>
                                                <option value="0" {{$job->color == '0' ? 'selected' : ''}}>None</option>
                                                <option value="A1" {{$job->color == 'A1' ? 'selected' : ''}}>A1</option>
                                                <option value="A2" {{$job->color == 'A2' ? 'selected' : ''}}>A2</option>
                                                <option value="A3" {{$job->color == 'A3' ? 'selected' : ''}}>A3</option>
                                                <option value="A3.5" {{$job->color == 'A3.5' ? 'selected' : ''}}>A3.5</option>
                                                <option value="A4" {{$job->color == 'A4' ? 'selected' : ''}}>A4</option>
                                                <option value="B1" {{$job->color == 'B1' ? 'selected' : ''}}>B1</option>
                                                <option value="B2" {{$job->color == 'B2' ? 'selected' : ''}}>B2</option>
                                                <option value="B3" {{$job->color == 'B3' ? 'selected' : ''}}>B3</option>
                                                <option value="B4" {{$job->color == 'B4' ? 'selected' : ''}}>B4</option>
                                                <option value="C1" {{$job->color == 'C1' ? 'selected' : ''}}>C1</option>
                                                <option value="C2" {{$job->color == 'C2' ? 'selected' : ''}}>C2</option>
                                                <option value="C3" {{$job->color == 'C3' ? 'selected' : ''}}>C3</option>
                                                <option value="C4" {{$job->color == 'C4' ? 'selected' : ''}}>C4</option>
                                                <option value="D2" {{$job->color == 'D2' ? 'selected' : ''}}>D2</option>
                                                <option value="D3" {{$job->color == 'D3' ? 'selected' : ''}}>D3</option>
                                                <option value="D4" {{$job->color == 'D4' ? 'selected' : ''}}>D4</option>
                                                <option value="BL1" {{$job->color == 'BL1' ? 'selected' : ''}}>BL1</option>
                                                <option value="BL2" {{$job->color == 'BL2' ? 'selected' : ''}}>BL2</option>
                                                <option value="BL3" {{$job->color == 'BL3' ? 'selected' : ''}}>BL3</option>
                                                <option value="BL4" {{$job->color == 'BL4' ? 'selected' : ''}}>BL4</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label>Style:</label>
                                        </div>
                                        <div class="kt-radio-inline">
                                            @if($job->style == "None")
                                                <label class="kt-radio" style="font-weight: 800">None</label>
                                            @else
                                            <label class="kt-radio">
                                                <input type="radio" class="bridge" name="style" value="Bridge" {{$job->style == "Bridge" ? 'checked' : '' }} disabled> Bridge
                                                <span></span>
                                            </label>
                                            <label class="kt-radio">
                                                <input type="radio" class="single" {{$job->style == "Single" ? 'checked' : '' }} name="style"  value="Single" disabled> Single
                                                <span></span>
                                            </label>
                                                @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-2">
                                    <div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label></label>
                                        </div>
                                        <div class="kt-form__control">
                                            <b style="color:#2b7b7d">{{$job->status()}}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                        </div>
                    </div>
                </div>
            </div>
           <!-- <a href="javascript:;" data-repeater-create="" class="btn btn-info  btn-sm" id="addJobBtn" >
                <i class="fa fa-plus-square"></i> Add
            </a> -->
        </div>



        <!-- NOTES SECTION -->
        <br>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h5 class="kt-portlet__head-title">
                    <i class="fa fa-sticky-note" style="width:2%"></i> Additional information
                </h5>
            </div>
        </div>
        <hr>
        <br>
        <div class="form-group form-group">
            <label >Notes:</label>

            @foreach($case->notes as $note)

                <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black" disabled>

                    <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                </div>
            @endforeach

            <form></form>
            <form  style="padding:10px" class=" " method="POST" enctype="multipart/form-data"   action="{{route('new-note')}}">
                @csrf
                <div class="row">
                    <input type="hidden" name="case_id_for_note" value ="{{$case->id}}">
                    <div class="col-md-6 col-xs-6">
                        <input class="form-control" type="text" name="newNote"  placeholder="Add a note"  />
                    </div>

                    <div class="col-md-3 col-xs-3" style="margin: 0px">
                    <button type="submit" class="btn btn-primary">Add note</button>
                    </div>


                </div>
                </form>
            <br><br>
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h5 class="kt-portlet__head-title">
                        <i class="fa fa-sticky-note" style="width:2%"></i> Photos:
                    </h5>
                </div>
            </div>
            <hr>
        <!-- Photos SECTION -->
        <div class="container" style="margin-top:10px;">

            <div class="demo-gallery">
                <ul id="lightgallery" class="list-unstyled row">
                    @foreach($case->photos as $photo)
                        <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="{{asset($photo->path)}}" data-src="{{asset($photo->path)}}">
                            <a href="">
                                <img class="img-responsive" src="{{asset($photo->path)}}">
                            </a>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>

        <br>
        <div class="form-group form-group-last">
            <label for="images">Add Photos:</label>
            <input required type="file" id="images" class="form-control" name="images[]" placeholder="address" multiple disabled>
        </div>
        <br>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <button type="submit" class="btn btn-primary" disabled>Submit</button>
                <button type="reset" class="btn btn-danger" disabled>Reset</button>
            </div>
        </div>
        </div>
    </form>


    <!-- TEETH PICK DIALOG -->
    <div data-repeater-item class="modal fade" id="unitsDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle-1">Modal title </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" value="success" name="dialogNum" class="dialogTag">
                    @php
                        $startingPosition = 310;
                        $imageSize = 50;
                        $decrement = 45;
                        $teeth = 0;
                        $imageSizeL = 43;
                        $imageSizeM = 35;
                        $leftPadding=70;
                    @endphp
                    <div class="main-body" style="padding-top: 50px;width:200px;height:500px">
                        <h2 style="padding-left:300%" id="teethSelectedH2"></h2>



                        <img class="teeth" alt="38" src="/assets/teethPics/17.png" height={{$imageSizeL}}px style="  position: absolute; top: {{$startingPosition + 0 }}px;left: {{272+  $leftPadding}}px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt ="37" src="/assets/teethPics/18.png" height={{$imageSizeL+2}}px style="  position: absolute; top: {{$startingPosition+ 41 }}px;left:{{ 272+  $leftPadding}}px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt ="36" src="/assets/teethPics/19.png" height={{$imageSizeL+2}}px style="  position: absolute; top: {{$startingPosition+ 80 }}px;left:{{ 268+ $leftPadding}}px;">
                        @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                        <img class="teeth" alt ="35" src="/assets/teethPics/20.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 120 }}px;left:{{ 258+  $leftPadding}}px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt ="34" src="/assets/teethPics/21.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+153 }}px;left:{{ 245+  $leftPadding}}px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt ="33" src="/assets/teethPics/22.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 182}}px;left:{{ 227+  $leftPadding}}px;">
                        @php $teeth = 6; @endphp
                        <img class="teeth" alt ="32" src="/assets/teethPics/23.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition + 195 }}px;left: {{203+  $leftPadding}}px;">
                        @php $teeth = 7; @endphp
                        <img class="teeth" alt="31" src="/assets/teethPics/24.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition +  200 }}px;left: {{168+  $leftPadding}}px;">
                        @php $teeth = 8; @endphp
                        <img class="teeth" alt ="41" src="/assets/teethPics/25.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition + 200}}px;left: {{134+  $leftPadding}}px;">
                        @php $teeth = 9; @endphp
                        <img class="teeth" alt ="42" src="/assets/teethPics/26.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 197 }}px;left: {{104+  $leftPadding}}px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt ="43" src="/assets/teethPics/27.png" height={{$imageSizeL-3}}px style="  position: absolute; top: {{$startingPosition+ 185 }}px;left: {{68+  $leftPadding}}px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt ="44" src="/assets/teethPics/28.png" height={{$imageSizeL-3}}px style="  position: absolute; top: {{$startingPosition+ 158}}px;left: {{46+  $leftPadding}}px;">
                        @php $teeth = 3; @endphp
                        <img class="teeth" alt ="45" src="/assets/teethPics/29.png" height={{$imageSizeM}}px style="  position: absolute; top: {{$startingPosition+ 125}}px;left: {{39+  $leftPadding}}px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt ="46" src="/assets/teethPics/30.png" height={{$imageSizeL+4 }}px style="  position: absolute; top: {{$startingPosition+ +80 }}px;left: {{34+  $leftPadding}}px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt ="47" src="/assets/teethPics/31.png" height={{$imageSizeL+6 }}px style="  position: absolute; top: {{$startingPosition+ $decrement *$teeth -10 }}px;left: {{29+  $leftPadding}}px;">
                        @php $teeth = 0; @endphp
                        <img class="teeth" alt ="48" src="/assets/teethPics/32.png" height={{$imageSizeL+3 }}px style="  position: absolute; top: {{$startingPosition+ $decrement *$teeth -5 }}px;left: {{25+  $leftPadding}}px;">
                        @php $teeth = 16; @endphp

                        <img class="jaw upperJaw" alt ="upper" src="/assets/teethPics/upper-jaw.png" width=180px style="position: absolute; top: 180px;left: 150px;">
                        <img class="jaw lowerJaw" alt ="lower" src="/assets/teethPics/lower-jaw.png" width=180px style="position: absolute; top: 330px;left: 150px;">

                    </div>
                </div>

                <script
                            src="https://code.jquery.com/jquery-3.6.0.min.js"
                            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
                            crossorigin="anonymous"></script>


                </div>
                <div class="modal-footer" name ="model-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog"   onclick="">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <!-- FILES DIALOG -->
    <div  class="modal fade" id="filesDialog" tabindex="-1" role="dialog" aria-labelledby="fileDialog" style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle-1">Modal title </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">





                </div>
                <div class="modal-footer" name ="model-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog"   onclick="">Save changes</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@push('js')
    <script>
        $(document).ready(function () {
            $('#lightgallery').lightGallery();
        });

        var teethSelected = [];
        var lstSelectedJobUNName = "";

        function jobTypeChanged(jobTypeDD){
            var jobTypes = {!! json_encode($types->toArray()) !!};
            var materials = {!! json_encode($materials->toArray()) !!};
            var materialJobTypeRelations = {!! json_encode($jobTypeMaterials->toArray()) !!};

            var materialBox = $("[name='"+$(jobTypeDD).attr("name").substr(0,9)  +"[material_id]']");
            var jobTypeSelectedId = $(jobTypeDD).val();
            var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId);
            materialBox.empty();
            $.each(jobTypeMaterials, function(key, value) {
                materialBox.append($("<option></option>")
                    .attr("value", value.material_id)
                    .text( materials.find(x => x.id === value.material_id).name));
            });
        }

        $("#submitDialog").click(function() {

            var teethSelectedAsArr = $("[name='"+lstSelectedJobUNName+"']").val().split(',');
            var jobTypeBoxName = lstSelectedJobUNName.substr(0,9) + "[jobType]";
            var selectBtnName =  lstSelectedJobUNName.substr(0,9) + "[openDialogBtn]";
            var jobTypeBox=  $("[name='"+jobTypeBoxName+"']");
            var jobTypes = {!! json_encode($types->toArray()) !!};
            var colorsDDName = lstSelectedJobUNName.substr(0,9)  +"[color]";
            /* Updating dropdowns according to teeth selection
             * First if is for jaws, second is for teeth
             * @Yazan - Korvex
             */
            if (jQuery.inArray("lower",teethSelectedAsArr)  !== -1|| jQuery.inArray("upper",teethSelectedAsArr) !== -1) {
                // clear all options
                jobTypeBox.empty();
                // filter all job types to only jaws.
                var jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 1);
                // fill up the options with the array above.
                $.each(jawOnlyTypes, function(key, value) {
                    jobTypeBox.append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name));
                });
                // Notify Job type changed function to update materials with which box changed
                jobTypeChanged(jobTypeBox);
            }

            // No jaws selected
            else{
                jobTypeBox.empty();
                const jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 0);
                $.each(jawOnlyTypes, function(key, value) {
                    jobTypeBox.append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name));
                });
                // Notify Job type changed function to update materials with which box changed
                jobTypeChanged(jobTypeBox);
            }

            // Change button label with selected teeth
            if(teethSelectedAsArr.length >0)
                $("[name='"+selectBtnName+"']").html(teethSelectedAsArr.join(","));
            else
                $("[name='"+selectBtnName+"']").html("Select Units");


            $("[name='"+colorsDDName+"']").val($("[name='"+colorsDDName+"'] option:first").val());
            // close dialog
            $(".modal").modal('hide');

        });


        $(".teeth").click(function() {

            // Check if any jaws is selected, if any remove them from array
            if(jQuery.inArray("upper", teethSelected) !== -1)
            {
                const jawIndex = teethSelected.indexOf("upper");
                teethSelected.splice(jawIndex, 1);
            }
            if(jQuery.inArray("lower", teethSelected) !== -1)
            {
                const jawIndex = teethSelected.indexOf("lower");
                teethSelected.splice(jawIndex, 1);
            }

            // remove the light of the jaws buttons
            var list = $('.jaw');
            list.removeClass("checked");


            //if not pre selected light up the teeth and add it to array
            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                var teethNumber = $(this).attr("alt");
                const index = teethSelected.indexOf(teethNumber);

                if (index > -1) {
                    teethSelected.splice(index, 1);
                }

                // remove the selection if previously selected
            } else {
                var teethNumber = $(this).attr("alt");
                teethSelected.push(teethNumber);
                $(this).addClass("checked");
                // console.log("Added a teeth" + teethSelected);
            }

            //console.log("Updating units input : "  + teethSelected);

            $("[name='"+lstSelectedJobUNName+"']").val(teethSelected);
        });


        $(".jaw").click(function() {

            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                var jaw = $(this).attr("alt");
                const index = teethSelected.indexOf(jaw);

                if (index > -1) {
                    teethSelected.splice(index, 1);
                }
                var unitNumsBox = $("[id=units]:last").attr("name");
                $("[name='"+unitNumsBox+"']").val(teethSelected);

            } else {

                var jaw = $(this).attr("alt");
                // add visuall selection to the jaw the selection
                $(this).addClass("checked");

                // remove visual selection of all teeth if a jaw is selected
                var list = $('.teeth');
                list.removeClass("checked");

                // remove all selected teeth
                for (var index = 0; index <= teethSelected.length ; index++)
                {
                    if(teethSelected[index] != "lower" && teethSelected[index] != "upper" ){
                        teethSelected.splice(index);
                    }
                }
                // add selected jaw to the array and update value
                teethSelected.push(jaw);


            }

            $("[name='"+lstSelectedJobUNName+"']").val(teethSelected);
        });

        function preOpenDialog(element) {
            lstSelectedJobUNName = element.name.substr(0,9) + "[units]";
            var currentJobUnits = $("[name='" +element.name.substr(0,9) +  "[units]"+"']");
            // console.log("Current job units box name :" + element.name.substr(0,9) +  "[units]");
            if(typeof currentJobUnits !==  "undefined" && currentJobUnits.val()) {
                teethSelected = currentJobUnits.val().split(',');
                // console.log("is defined and its now : " + teethSelected);
            }
            else {
                // console.log("NOT defined,cleared");
                teethSelected = [];
            }
            if (teethSelected.length !== 0 ){
                var teethPreSelected = currentJobUnits.val().split(',');
                // console.log("Lighting up : " + teethPreSelected);
                // light on and off according to the pre selected
                $(".teeth").each(function(){
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1){
                        // console.log("true");
                        $(this).addClass("checked");}
                    else
                        $(this).removeClass("checked");
                });
                $(".jaw").each(function(){
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1 )
                        $(this).addClass("checked");
                    else
                        $(this).removeClass("checked");
                });}
            else
            {
                $(".teeth").removeClass("checked");
                $(".jaw").removeClass("checked");
            }
        };


    </script>
    <script src="{{asset('assets/js/jquery.imagesloader-1.0.1.js')}}"></script>
    <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>
    <script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script>
    <script src="{{asset('assets/js/lightgallery.js')}}"></script>
@endpush

