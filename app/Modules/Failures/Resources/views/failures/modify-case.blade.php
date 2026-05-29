@extends('layouts.app' ,[ 'pageSlug' => $modify . ' Case' ])

@section('content')
    <link rel="stylesheet" href="{{asset('assets/css/lightgallery.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/jquery.imagesloader.css')}}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/lightgallery/1.3.9/css/lightgallery.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>

        .slctUnitsBtn{
            margin:0;
            width:100%;
            height:100%; /* change this from auto */
            display:block;
        }
        #addJobBtn{
            background-color: #24c143 !important;
            border-color: #f3f4f5 !important;
            padding: 0.45rem 0.9rem;
            border-radius: 0.3rem;
        }
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) { overflow-y: visible !important; }
        .stage, .implant,.abutment{
            margin-top:10px;
            margin-bottom:5px;
        }
        .implant,.abutment {
            padding-left:0px !important;

        }
    </style>

    <style>
        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }
        .hidden{
            display:none;
        }
        .hideTeeth {
            filter:invert(5%) sepia(100%) saturate(50%) hue-rotate(228deg) brightness(96%) contrast(40%);
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 400px;
                margin: 0.75rem auto;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp


    <form style="padding:10px" class="kt-form card" method="POST" enctype="multipart/form-data" action="{{route('modify-case')}}">
    @csrf


        <input name="id" type="hidden" value="{{$case->id}}" />
    <!-- CASE INFO -->

        <div class="row">
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Doctor:</label></div>
                <div class="col-md-12 col-xs-12">


                    <select  class="selectpicker"  name="doctor"  data-container="body" data-live-search="true"  title="Select a doctor" readonly>
                        @foreach($clients as $client)
                            <option value="{{$client->id}}" {{$case->client->id == $client->id ? "selected" : ""}} >{{$client->name}}</option>
                        @endforeach

                    </select>

                </div> </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Patient name:</label></div>
                <div class="col-md-12 col-xs-12"><input class="form-control" type="text" name="patient_name" value="{{$case->patient_name}}" readonly /></div>
            </div>


            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-6 col-xs-12"><label>Case ID:</label></div>
                <div class="col-md-12 col-xs-12">

                    <label >{{substr($case->case_id,0,7)}}</label>
                    <input name="caseId1" type="hidden" value="{{substr($case->case_id,0,7)}}" />
                    <input name="caseId2" placeholder="Time" style="width:30px; border:1px solid #ced4da;height:30px" type="text" value="{{substr($case->case_id,7,2)}}" readonly />
                    <input name="caseId3" placeholder="Time" style="width:30px; border:1px solid #ced4da;height:30px" type="text" value="{{substr($case->case_id,9,2)}}" readonly />
                    <label>_</label>
                    <input name="caseId4" placeholder="0000" style="width:50px;border-top-right-radius:5px;border-bottom-right-radius:5px; border:1px solid #ced4da;height:30px" type="text" value="{{substr($case->case_id,12,4)}}" readonly />
                </div>

            </div>

        </div>

            <br/>
        <div class="row">

            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Delivery Date:</label></div>
                <div class="col-md-12 col-xs-12">
                    @php
                        $time = new DateTime("tomorrow 13:00");
                       // $time = $time->format("Y-m-d\TH:i");
                        $time = $time->format("d M, Y h:i a");
                    @endphp
                    <input class="form-control SDTP" name="delivery_date"  type="text"   value="{{$time}}" required />
                   </div>
            </div>
            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Tags:</label></div>
                <div class="col-md-12 col-xs-12">

                    <select class="select selectpicker" name="tags[]"  data-mdb-placeholder="Tags" multiple >

                        @foreach($tags as $tag)
                            <option style="color:{{$tag->color}}" value="{{$tag->id}}" {{in_array($tag->id ,$tagsAsArray) ? 'selected' : ''}}>{{$tag->text}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-4 col-xs-6 col-l-2 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Impression Type:</label></div>
                <div class="col-md-12 col-xs-12"> <select  class="form-control" name="impression_type" type="text"  data-container="body" data-live-search="true" title="Select impression" data-hide-disabled="true"  readonly >

                        @foreach($impressionTypes as $impression)
                            <option value="{{$impression->id}}" {{$impression->id == $case->impression_type ? 'selected' :''}}>
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


        <!-- EXISITING JOBS REPEATER -->

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
                                <input type="hidden" name="job_id"  value="{{$job->id}}" />

                                <div class="col-md-2"> <div class="">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single"></label>
                                        </div>
                                        <input type="hidden" name="r" id="repeaterID" class="repeaterName"  />

                                        <input type="hidden" name="old_units{{$job->id}}" id="old_units"  value="{{$job->unit_num}}" />
                                        <input type="hidden" name="units{{$job->id}}" id="units" class="hiddenUnitsInput" value="{{$job->unit_num}}" />
                                        <button type="button" class="btn btn-secondary slctUnitsBtn"  data-toggle="modal" data-target="#unitsDialog" name="openDialogBtn{{$job->id}}" onclick="preOpenDialog(this,{{$job->id}})">
                                            {{$job->unit_num}}
                                        </button>

                                    </div>

                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Job type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="jobType" name="jobType{{$job->id}}" onchange="jobTypeChanged(this,{{$job->id}})" readonly>

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
                                            <select class="form-control" id="material_id" name="material_id{{$job->id}}" readonly>

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
                                            <select class="form-control" id="color" name="color{{$job->id}}" readonly>
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
                                <div class="col-md-4">
                                    <div class="row">
                                <div class="col-md-4">
                                    <div class="kt-form__group--inline" style="display:{{$job->style == 'None' ? 'None' : 'Block'}}">
                                        <div class="kt-form__label">
                                            <label>Style:</label>
                                        </div>
                                        <div class="kt-radio-inline">
                                            <label class="kt-radio">
                                                <input type="radio" class="bridge" name="style{{$job->id}}" value="Bridge" {{$job->style == "Bridge" ? 'checked' : '' }} readonly /> Bridge
                                                <span></span>
                                            </label>
                                            <label class="kt-radio">
                                                <input type="radio" class="single" {{$job->style == "Single" ? 'checked' : '' }} name="style{{$job->id}}"  value="Single" readonly /> Single
                                                <span></span>
                                            </label>
                                        </div>


                                    </div>

                                </div>

                                 <div class="col-md-6" style="margin: auto;">
                                            <div class="kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    <label></label>
                                                </div>
                                                <div class="kt-form__control">
                                                    <b style="color:#2b7b7d">{{$job->status()}}</b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2" style="padding:0;margin: auto;">
                                            <div class="kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    <label></label>
                                                </div>
                                                <div class="kt-form__control">
                                                    <button data-repeater-delete  class="btn btn-danger  btn-sm" type="button" value="Delete" style="width:70%;height:100%"> <i class="fa fa-trash"></i></span> </button>
                                                </div>
                                            </div>
                                        </div>

                                </div>

                                </div>
                                <br>  <br> <br>  <br>

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


        <!-- NEW JOBS REPEATER -->
        <div  id="kt_repeater_2" style="padding-left: 15px; padding-right: 15px">
            <div  data-repeater-list="repeat2">
                <div data-repeater-item style="display:none;">
                    <div class="form-group form-group ">
                        <div data-repeater-list="repeat2" class="col-12">
                            <div data-repeater-item class="form-group row align-items-center row-item" style="border: 1px solid #ccc;border-radius: 16px;padding:5px">


                                <div class="col-md-2">
                                    <div class="kt-form__label">
                                        <label class="kt-label m-label--single"></label>
                                    </div>

                                    <input type="hidden" name="units" id="units" class="hiddenUnitsInput" required>
                                    <button type="button" class="btn btn-secondary slctUnitsBtn" data-toggle="modal" data-target="#unitsDialog2" name="openDialogBtn" onclick="preOpenDialog2(this)">
                                        Select Units
                                    </button>

                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Job type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="jobType" name="jobType" onchange="jobTypeChanged2(this)" readonly>
                                                @foreach($types as $type)
                                                    <option value="{{$type->id}}">{{$type->name}}</option>
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
                                            <select class="form-control" id="material_id" name="material_id" onchange="materialChanged()" readonly>

                                                @foreach($materials as $m)
                                                    <option value="{{$m->id}}">
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
                                            <select class="form-control" id="color" name="color">
                                                <option value="0" selected>None</option>
                                                <option value="A1">A1</option>
                                                <option value="A2">A2</option>
                                                <option value="A3">A3</option>
                                                <option value="A3.5">A3.5</option>
                                                <option value="A4">A4</option>
                                                <option value="B1">B1</option>
                                                <option value="B2">B2</option>
                                                <option value="B3">B3</option>
                                                <option value="B4">B4</option>
                                                <option value="C1">C1</option>
                                                <option value="C2">C2</option>
                                                <option value="C3">C3</option>
                                                <option value="C4">C4</option>
                                                <option value="D2">D2</option>
                                                <option value="D3">D3</option>
                                                <option value="D4">D4</option>
                                                <option value="BL1">BL1</option>
                                                <option value="BL2">BL2</option>
                                                <option value="BL3">BL3</option>
                                                <option value="BL4">BL4</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label style="color:red">Stage:</label>
                                        </div>
                                        <div style="color:red" class="kt-form__control">
                                            <select class="form-control" style="color:red" id="stage" name="repeatedJobStage">
                                                <option value="1" selected>Design</option>
                                                <option value="2">Milling</option>
                                                <option value="3">3D Printing</option>
                                                <option value="4">Sintering</option>
                                                <option value="5">Press Furnace</option>
                                                <option value="6">Finishig</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="kt-form__group--inline" style="">
                                                <div class="kt-form__label">
                                                    <label>Style:</label>
                                                </div>
                                                <div class="kt-radio-inline">
                                                    <label class="kt-radio">
                                                        <input type="radio" class="bridge" name="style" value="Bridge"  /> Bridge
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio">
                                                        <input type="radio" class="single"  name="style"  value="Single" /> Single
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div >

                                                <div class="kt-form__control">

                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-5" style="padding:0;margin: auto;">
                                            <div class="kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    <label></label>
                                                </div>
                                                <div class="kt-form__control">
                                                    <button data-repeater-delete  class="btn btn-danger  btn-sm" type="button" value="Delete" style="width:70%;height:100%"> <i class="fa fa-trash"></i></span> </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <br>  <br> <br>  <br>




                                <div class="col-md-3 abutment" style="display:none"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Abutment type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="abutment" name="abutment" >
                                                <option value="0" selected >None</option>
                                                @foreach($abutments as $abutment)
                                                    <option value="{{$abutment->id}}">{{$abutment->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3 implant" style="display:none"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Implant type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="implant" name="implant" >
                                                <option value="0" selected >None</option>
                                                @foreach($implants as $implant)
                                                    <option value="{{$implant->id}}">{{$implant->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


            {{--<a href="javascript:addJobBtnPressed()" data-repeater-create="" class="btn btn-info  btn-sm" id="addJobBtn">--}}
                {{--<i class="fa fa-plus-square"></i> Add--}}
            {{--</a>--}}
        </div>
        <br>
            <br>


        <!-- NOTES SECTION -->
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
            <div class="kt-form__control">
                <label style="color:red">Failure Cause:</label>
                <select class="form-control" id="failure_cause_id" name="failure_cause_id" required>

                    @foreach($failureCauses as $cause)
                        <option value="{{$cause->id}}">{{$cause->text}}</option>
                    @endforeach
                </select>
                <br/>
                <textarea class="form-control" type="text" name="failure_explanation"  placeholder="Extra Details about the failure"/></textarea>

            </div>
        </div>
        <hr>
        <br><br>
    @if(count($case->notes) > 0)
        <!-- Notes SECTION -->
        <div class="form-group form-group">
            <label >Notes:</label>
            @foreach($case->notes as $note)

                <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black" readonly>

                    <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                </div>
            @endforeach


                    </div>
            <br><br>
    @endif

        <!-- Photos SECTION -->
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h5 class="kt-portlet__head-title">
                        <i class="fa fa-photo" style="width:2%"></i> Attachments
                    </h5>
                </div>
            </div>
            <hr>
        <div class="container" style="margin-top:10px;">

            <div class="demo-gallery">
                <ul id="lightgallery" class="list-unstyled row">
                    @if(!$case->photos)
                       <b>No Attachments</b>
                    @else
                    @foreach($case->photos as $photo)
                        <li class="col-xs-6 col-sm-4 col-md-2 col-lg-2" data-responsive="{{asset($photo->path)}}" data-src="{{asset($photo->path)}}">
                            <a href="">
                                <img class="img-responsive" src="{{asset($photo->path)}}">
                            </a>
                        </li>
                    @endforeach
                    @endif

                </ul>
            </div>
        </div>
        <br>
        <div class="form-group form-group-last">
            <label for="images">Add Attachments:</label>
            <input type="file" id="images" class="form-control" name="images[]" placeholder="address" multiple />
        </div>
        <br>

        <!-- Form Footer -->
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <button type="submit" class="btn btn-primary" >Submit</button>
                <button type="reset" class="btn btn-danger" >Reset</button>
            </div>
        </div>
    </form>


    <!-- Existing TEETH PICK DIALOG -->
    <div data-repeater-item class="modal fade" id="unitsDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <input type="hidden" value="success" name="dialogNum" class="dialogTag">
                    @php
                        $startingPosition = 290;
                     $imageSize = 50;
                     $decrement = 45;
                     $teeth = 0;
                     $imageSizeL = 49;
                     $imageSizeM = 35;
                     $leftPadding=66;
                    @endphp
                    <div class="main-body" style="padding-top: 30px;width:200px;/*height:500px*/">


                    <img class="jaw upperJaw" alt="upper"  src="/assets/teethPics/v2/upper_jaw.png" height=265px
                             style="position: absolute; top: 17px;left: 0px;">
                        <img class="jaw lowerJaw" alt="lower"  src="/assets/teethPics/v2/lower_jaw.png" height=280px
                             style="position: absolute; top: 295px;left: 17px;">

                        <img class="teeth" alt="18" src="/assets/teethPics/v2/18.png" height={{$imageSizeM +8}}px
                             style="  position: absolute; top: 226px;left: 55px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="17" src="/assets/teethPics/v2/17.png" height={{$imageSizeL}}px
                             style="  position: absolute; top:183px;left:59px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="16" src="/assets/teethPics/v2/16.png" height={{$imageSizeL +3}}px
                             style="  position: absolute; top: 139px;left:67px;">
                        @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                        <img class="teeth" alt="15" src="/assets/teethPics/v2/15.png" height={{$imageSizeM +1}}px
                             style="  position: absolute; top: 111px;left:79px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="14" src="/assets/teethPics/v2/14.png" height={{$imageSizeM +2}}px
                             style="  position: absolute; top:82px;left:92px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="13" src="/assets/teethPics/v2/13.png" height={{$imageSizeM +6}}px
                             style="  position: absolute; top:53px;left:110px;">
                        @php $teeth = 6; @endphp
                        <img class="teeth" alt="12" src="/assets/teethPics/v2/12.png" height={{$imageSizeM +4}}px
                             style="  position: absolute; top: 36px;left: 135px;">
                        @php $teeth = 7; @endphp
                        <img class="teeth" alt="11" src="/assets/teethPics/v2/11.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23.5px;left: 162px;">
                        @php $teeth = 8; @endphp
                        <img class="teeth" alt="21" src="/assets/teethPics/v2/21.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23px;left:200px;">
                        @php $teeth = 9; @endphp
                        <img class="teeth" alt="22" src="/assets/teethPics/v2/22.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top:35px;left: 231px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="23" src="/assets/teethPics/v2/23.png" height={{$imageSizeM +3}}px
                             style="  position: absolute; top: 55px;left: 254px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="24" src="/assets/teethPics/v2/24.png" height={{$imageSizeM}}px
                             style="  position: absolute; top: 84px;left: 266px;">
                        @php $teeth = 3; @endphp
                        <img class="teeth" alt="25" src="/assets/teethPics/v2/25.png" height={{$imageSizeM}}px
                             style="  position: absolute; top:112px;left:272px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="26" src="/assets/teethPics/v2/26.png" height={{$imageSizeL +1 }}px
                             style="  position: absolute; top: 141px;left: 280px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="27" src="/assets/teethPics/v2/27.png" height={{$imageSizeL }}px
                             style="  position: absolute; top:182px;left: 291px;">
                        @php $teeth = 0; @endphp
                        <img class="teeth" alt="28" src="/assets/teethPics/v2/28.png" height={{$imageSizeL }}px
                             style="  position: absolute; top:227px;left: 291px;">
                        @php $teeth = 16; @endphp


                        @php
                            $startingPosition = 330;
                            $imageSize = 50;
                            $decrement = 45;
                            $teeth = 0;
                            $imageSizeL = 43;
                            $imageSizeM = 35;
                            $leftPadding=70;
                        @endphp
                        <div class="main-body" style="padding-top: 50px;width:200px;height:500px">
                            <h2 style="padding-left:300%" id="teethSelectedH2"></h2>

                            <img class="teeth" alt="38" src="/assets/teethPics/v2/38.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top:326px;left: 309px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="37" src="/assets/teethPics/v2/37.png" height={{$imageSizeL+6}}px
                                 style="  position: absolute; top:367px;left:299px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="36" src="/assets/teethPics/v2/36.png" height={{$imageSizeL+5}}px
                                 style="  position: absolute; top:412px;left:285px;">
                            @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                            <img class="teeth" alt="35" src="/assets/teethPics/v2/35.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 454px;left:275px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="34" src="/assets/teethPics/v2/34.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 484px;left:263px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="33" src="/assets/teethPics/v2/33.png" height={{$imageSizeM+1}}px
                                 style="  position: absolute; top: 508px;left:247px;">
                            @php $teeth = 6; @endphp
                            <img class="teeth" alt="32" src="/assets/teethPics/v2/32.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 527px;left: 229px;">
                            @php $teeth = 7; @endphp
                            <img class="teeth" alt="31" src="/assets/teethPics/v2/31.png" height={{$imageSizeM-3}}px
                                 style="position: absolute; top:538px;left: 203px;">
                            @php $teeth = 8; @endphp
                            <img class="teeth" alt="41" src="/assets/teethPics/v2/41.png" height={{$imageSizeM-2}}px
                                 style="position: absolute; top: 534px;left:176px;">
                            @php $teeth = 9; @endphp
                            <img class="teeth" alt="42" src="/assets/teethPics/v2/42.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top:524px;left: 150px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="43" src="/assets/teethPics/v2/43.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 510px;left: 127px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="44" src="/assets/teethPics/v2/44.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 485px;left: 108px;">
                            @php $teeth = 3; @endphp
                            <img class="teeth" alt="45" src="/assets/teethPics/v2/45.png" height={{$imageSizeM+2}}px
                                 style="  position: absolute; top: 455px;left: 88px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="46" src="/assets/teethPics/v2/46.png" height={{$imageSizeL+4.5 }}px
                                 style="  position: absolute; top: 415px;left: 68px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="47" src="/assets/teethPics/v2/47.png" height={{$imageSizeL+5 }}px
                                 style="  position: absolute; top: 371px;left: 55px;">
                            @php $teeth = 0; @endphp
                            <img class="teeth" alt="48" src="/assets/teethPics/v2/48.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top: 331px;left:44px;">
                            @php $teeth = 16; @endphp

                        </div>
                    </div>

                    <script
                            src="https://code.jquery.com/jquery-3.6.0.min.js"
                            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
                            crossorigin="anonymous"></script>


                </div>
                <div class="modal-footer" name ="model-footer"  style="padding-top:45px">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog"   onclick="submitDialogFun(this)" >Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW TEETH PICK DIALOG -->
    <div data-repeater-item class="modal fade" id="unitsDialog2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true" name="dialog2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <input type="hidden" value="success" name="dialogNum" class="dialogTag">
                    @php
                        $startingPosition = 290;
                       $imageSize = 50;
                       $decrement = 45;
                       $teeth = 0;
                       $imageSizeL = 49;
                       $imageSizeM = 35;
                       $leftPadding=66;
                    @endphp
                    <div class="main-body" style="padding-top: 30px;width:200px;/*height:500px*/">


                        <img class="jaw upperJaw" alt="upper"  src="/assets/teethPics/v2/upper_jaw.png" height=265px
                             style="position: absolute; top: 17px;left: 0px;">
                        <img class="jaw lowerJaw" alt="lower"  src="/assets/teethPics/v2/lower_jaw.png" height=280px
                             style="position: absolute; top: 295px;left: 17px;">

                        <img class="teeth" alt="18" src="/assets/teethPics/v2/18.png" height={{$imageSizeM +8}}px
                             style="  position: absolute; top: 226px;left: 55px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="17" src="/assets/teethPics/v2/17.png" height={{$imageSizeL}}px
                             style="  position: absolute; top:183px;left:59px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="16" src="/assets/teethPics/v2/16.png" height={{$imageSizeL +3}}px
                             style="  position: absolute; top: 139px;left:67px;">
                        @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                        <img class="teeth" alt="15" src="/assets/teethPics/v2/15.png" height={{$imageSizeM +1}}px
                             style="  position: absolute; top: 111px;left:79px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="14" src="/assets/teethPics/v2/14.png" height={{$imageSizeM +2}}px
                             style="  position: absolute; top:82px;left:92px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="13" src="/assets/teethPics/v2/13.png" height={{$imageSizeM +6}}px
                             style="  position: absolute; top:53px;left:110px;">
                        @php $teeth = 6; @endphp
                        <img class="teeth" alt="12" src="/assets/teethPics/v2/12.png" height={{$imageSizeM +4}}px
                             style="  position: absolute; top: 36px;left: 135px;">
                        @php $teeth = 7; @endphp
                        <img class="teeth" alt="11" src="/assets/teethPics/v2/11.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23.5px;left: 162px;">
                        @php $teeth = 8; @endphp
                        <img class="teeth" alt="21" src="/assets/teethPics/v2/21.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23px;left:200px;">
                        @php $teeth = 9; @endphp
                        <img class="teeth" alt="22" src="/assets/teethPics/v2/22.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top:35px;left: 231px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="23" src="/assets/teethPics/v2/23.png" height={{$imageSizeM +3}}px
                             style="  position: absolute; top: 55px;left: 254px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="24" src="/assets/teethPics/v2/24.png" height={{$imageSizeM}}px
                             style="  position: absolute; top: 84px;left: 266px;">
                        @php $teeth = 3; @endphp
                        <img class="teeth" alt="25" src="/assets/teethPics/v2/25.png" height={{$imageSizeM}}px
                             style="  position: absolute; top:112px;left:272px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="26" src="/assets/teethPics/v2/26.png" height={{$imageSizeL +1 }}px
                             style="  position: absolute; top: 141px;left: 280px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="27" src="/assets/teethPics/v2/27.png" height={{$imageSizeL }}px
                             style="  position: absolute; top:182px;left: 291px;">
                        @php $teeth = 0; @endphp
                        <img class="teeth" alt="28" src="/assets/teethPics/v2/28.png" height={{$imageSizeL }}px
                             style="  position: absolute; top:227px;left: 291px;">
                        @php $teeth = 16; @endphp


                        @php
                            $startingPosition = 330;
                            $imageSize = 50;
                            $decrement = 45;
                            $teeth = 0;
                            $imageSizeL = 43;
                            $imageSizeM = 35;
                            $leftPadding=70;
                        @endphp
                        <div class="main-body" style="padding-top: 50px;width:200px;height:500px">
                            <h2 style="padding-left:300%" id="teethSelectedH2"></h2>

                            <img class="teeth" alt="38" src="/assets/teethPics/v2/38.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top:326px;left: 309px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="37" src="/assets/teethPics/v2/37.png" height={{$imageSizeL+6}}px
                                 style="  position: absolute; top:367px;left:299px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="36" src="/assets/teethPics/v2/36.png" height={{$imageSizeL+5}}px
                                 style="  position: absolute; top:412px;left:285px;">
                            @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                            <img class="teeth" alt="35" src="/assets/teethPics/v2/35.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 454px;left:275px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="34" src="/assets/teethPics/v2/34.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 484px;left:263px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="33" src="/assets/teethPics/v2/33.png" height={{$imageSizeM+1}}px
                                 style="  position: absolute; top: 508px;left:247px;">
                            @php $teeth = 6; @endphp
                            <img class="teeth" alt="32" src="/assets/teethPics/v2/32.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 527px;left: 229px;">
                            @php $teeth = 7; @endphp
                            <img class="teeth" alt="31" src="/assets/teethPics/v2/31.png" height={{$imageSizeM-3}}px
                                 style="position: absolute; top:538px;left: 203px;">
                            @php $teeth = 8; @endphp
                            <img class="teeth" alt="41" src="/assets/teethPics/v2/41.png" height={{$imageSizeM-2}}px
                                 style="position: absolute; top: 534px;left:176px;">
                            @php $teeth = 9; @endphp
                            <img class="teeth" alt="42" src="/assets/teethPics/v2/42.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top:524px;left: 150px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="43" src="/assets/teethPics/v2/43.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 510px;left: 127px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="44" src="/assets/teethPics/v2/44.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 485px;left: 108px;">
                            @php $teeth = 3; @endphp
                            <img class="teeth" alt="45" src="/assets/teethPics/v2/45.png" height={{$imageSizeM+2}}px
                                 style="  position: absolute; top: 455px;left: 88px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="46" src="/assets/teethPics/v2/46.png" height={{$imageSizeL+4.5 }}px
                                 style="  position: absolute; top: 415px;left: 68px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="47" src="/assets/teethPics/v2/47.png" height={{$imageSizeL+5 }}px
                                 style="  position: absolute; top: 371px;left: 55px;">
                            @php $teeth = 0; @endphp
                            <img class="teeth" alt="48" src="/assets/teethPics/v2/48.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top: 331px;left:44px;">
                            @php $teeth = 16; @endphp


                        </div>
                    </div>

                    <script
                            src="https://code.jquery.com/jquery-3.6.0.min.js"
                            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
                            crossorigin="anonymous"></script>


                </div>
                <div class="modal-footer" name ="model-footer"  style="padding-top:45px">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog2"  >Save changes</button>
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
                    <button type="button" class="btn btn-primary"    onclick="">Save changes</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@push('js')
    // Existing jobs js:
    <script>
        $(document).ready(function () {
            $('#lightgallery').lightGallery();
            $('.repeater').repeater({
                // (Optional)
                // start with an empty list of repeaters. Set your first (and only)
                // "data-repeater-item" with style="display:none;" and pass the
                // following configuration flag
                initEmpty: true,
                show: function () {
                    $(this).slideDown();
                },
                // (Optional)
                // "hide" is called when a user clicks on a data-repeater-delete
                // element.  The item is still visible.  "hide" is passed a function
                // as its first argument which will properly remove the item.
                // "hide" allows for a confirmation step, to send a delete request
                // to the server, etc.  If a hide callback is not given the item
                // will be deleted.
                hide: function (deleteElement) {
                    if(confirm('Are you sure you want to delete this job?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: true
            });
        });

        var teethSelected = [];
        var lstSelectedJobUNName = "";
        var repeaterName = ""; // should be something like 'repeat[xx]'
        var jobId =0;




        function submitDialogFun(Btn) {
            var teethSelectedAsArr = $("[name='" + lstSelectedJobUNName + "']").val().split(',');
            var jobTypeBoxName = repeaterName+ "[jobType"+jobId+"]";
            var selectBtnName = repeaterName + "[openDialogBtn"+jobId+"]";
            var jobTypeBox = $("[name='" + jobTypeBoxName + "']");

            var jobTypes = {!! json_encode($types->toArray()) !!};
            var colorsDDName = repeaterName+ "[color"+jobId+"]";
            var styleOptionsName = repeaterName  +"[style"+jobId+"]";
            /* Updating dropdowns according to teeth selection
             * First if is for jaws, second is for teeth
             * @Yazan - Korvex
             */

            if (jQuery.inArray("lower", teethSelectedAsArr) !== -1 || jQuery.inArray("upper", teethSelectedAsArr) !== -1) {
                // clear all options
                jobTypeBox.empty();
                // filter all job types to only jaws.
                var jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 1);
                // fill up the options with the array above.
                $.each(jawOnlyTypes, function (key, value) {
                    jobTypeBox.append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name));
                });
                // Notify Job type changed function to update materials with which box changed
                // jobTypeChanged(jobTypeBox);
                $("[name='"+colorsDDName+"']").parent().parent().parent().hide();

                // set style to none (prevent back-end errors) and hide it
                $("[name='"+styleOptionsName+"']").val('None');
                $("[name='"+styleOptionsName+"']").parent().parent().parent().hide();


            }

            // No jaws selected
            else {
                jobTypeBox.empty();
                const jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 0
                    )
                ;
                $.each(jawOnlyTypes, function (key, value) {
                    jobTypeBox.append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name));
                });

                // Notify Job type changed function to update materials with which box changed
                $("[name='"+styleOptionsName+"']").parent().parent().parent().show();
                $("[name='"+colorsDDName+"']").parent().parent().parent().show();
                if(teethSelectedAsArr.length >1)
                    $("[name='"+styleOptionsName+"'][value='Bridge']").prop("checked",true);
                else
                    $("[name='"+styleOptionsName+"'][value='Single']").prop("checked",true);

                // jobTypeChanged(jobTypeBox);

            }

            // Change button label with selected teeth
            if (teethSelectedAsArr.length > 0)
                $("[name='" + selectBtnName + "']").html(teethSelectedAsArr.join(","));
            else
                $("[name='" + selectBtnName + "']").html("Select Units");


            $("[name='" + colorsDDName + "']").val($("[name='" + colorsDDName + "'] option:first").val());
            // close dialog
            $(".modal").modal('hide');

        }


        $(".teeth").click(function() {
            if($(this).hasClass("hideTeeth"))
                return;
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
            if($(this).hasClass("hideTeeth"))
                return;
            else if ($(this).hasClass("checked")) {
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

        function preOpenDialog(element,unitNum) {
            jobId = unitNum;
            var lengthOfJobId =  jobId.toString().length;
            var lengthOfBtnName = parseInt(element.name.length) + parseInt(lengthOfJobId);

            if (parseInt(element.name.length)  == 24 + parseInt(lengthOfJobId) ) {
                lstSelectedJobUNName = element.name.substr(0, 9) + "[units" +unitNum+ "]";
                repeaterName = element.name.substr(0, 9);
                console.log(repeaterName);
                console.log(lstSelectedJobUNName);
            }
            else {
                console.log("repeater reached 2 digits");
                repeaterName = element.name.substr(0, 10);
                lstSelectedJobUNName = element.name.substr(0, 10) + "[units" +unitNum+ "]";
                console.log(lengthOfJobId);
            }
            var selector = "[name='"+ lstSelectedJobUNName+"']";
            var currentJobUnits = $(selector);
            var oldUnitsSelector = selector.replace("units","old_units");
            console.log(selector);
            console.log(oldUnitsSelector);
            var originalSelectedTeeth = $(oldUnitsSelector).val().split(',');

            // console.log("Current job units box name :" + element.name.substr(0,9) +  "[units]");
            if(typeof currentJobUnits !==  "undefined" && currentJobUnits.val()) {
                teethSelected = currentJobUnits.val().split(',');
                // console.log("is defined and its now : " + teethSelected);
            }
            else {
                console.log("didnt find previously selected units . err007, units box name selector= " + selector + " ,legnth of btn name : " + parseInt(element.name.length) + " ,length of job id string : " + parseInt(lengthOfJobId) + " || btn name : "+ element.name);
                teethSelected = [];
            }


            $(".teeth").removeClass("checked");
            $(".jaw").removeClass("checked");
            $(".teeth").removeClass("hideTeeth");
            $(".jaw").removeClass("hideTeeth");
            console.log("disablinh up : " + originalSelectedTeeth);
            // light on and off according to the pre selected
            $(".teeth").each(function(){

                if (jQuery.inArray($(this).attr("alt"), originalSelectedTeeth ) !== -1){
                    // console.log("true");
                    $(this).removeClass("hideTeeth");}
                else{
                    $(this).addClass("hideTeeth");}
            });
            $(".jaw").each(function(){

                if (jQuery.inArray($(this).attr("alt"), originalSelectedTeeth ) !== -1 )
                    $(this).removeClass("hideTeeth");
                else{
                    $(this).addClass("hideTeeth");}
            });



            if (teethSelected.length !== 0 ){
                var teethPreSelected = currentJobUnits.val().split(',');
                // console.log("Lighting up : " + teethPreSelected);
                // light on and off according to the pre selected
                $(".teeth").each(function(){
                    console.log("Teeth pre selected" + teethPreSelected);
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1){
                        // console.log("true");
                        $(this).addClass("checked");}
                    else{
                        $(this).removeClass("checked");}
                });
                $(".jaw").each(function(){
                    console.log("Jaws pre selected " + teethPreSelected);
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1 )
                        $(this).addClass("checked");
                    else{
                        $(this).removeClass("checked");}
                });}
            else
            {
                $(".teeth").removeClass("checked");
                $(".jaw").removeClass("checked");

            }
        }


        function setRepeaterNameByJobTypeDD(element,jobId){
            var lengthOfJobId =  jobId.toString().length;
            if (parseInt(element.name.length)  == 18 + parseInt(lengthOfJobId) ) {
                lstSelectedJobUNName = element.name.substr(0, 9) + "[units" +jobId+ "]";
                repeaterName = element.name.substr(0, 9);
            }
            else {
                console.log("Existing repeater reached 2 digits");
                repeaterName = element.name.substr(0, 10);
                lstSelectedJobUNName = element.name.substr(0, 10) + "[units" +jobId+ "]";
                console.log(lengthOfJobId);
            }
        }


    </script>

    // new Jobs js :
    <script>
        var teethSelected2 = [];
        var lstSelectedJobUNName2 = "";
        var repeaterName2 = ""; // should be something like 'repeat[xx]'
        function addJobBtnPressed(){
            var autoDetectStageCB = $(".autoStageCB:last").prop('checked', true);
            // Where repeater name ends :
            var lastIndex = autoDetectStageCB.attr('name').lastIndexOf('[auto');
            // splice it and save it in our variable
            repeaterName2 = autoDetectStageCB.attr('name').substr(0, lastIndex);
        }
        function detectNewJobStage() {
            var jobType = $("[name='"+repeaterName2  +"[jobType]']").val();
            if(typeof jobType == 'undefined' || jobType == '' || jobType == null) {Swal.fire(
                'Not yet',
                'Select units OR job type and material first',
                'error'
            );return;}
            var materialId = $("[name='"+repeaterName2  +"[material_id]']");
            if(typeof materialId == 'undefined' || materialId == '' || materialId == null) {alert("Select Material");return;}

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            console.log("material box : " + materialId.val());
            console.log("Posting, case id : " + {{$case->id}} + " job type : "+ jobType  + "Material id : " + materialId.val());
            $.ajax({
                type:'POST',
                url:'/detect-new-job-stage',
                data: { case_id: '{{$case->id}}',jobType: jobType,materialId: materialId.val(), _token: '{{csrf_token()}}' },
                success: function (data) {
                    Swal.fire(
                        'Good job!',
                        'New Job will be set at stage : ' + data['msg'],
                        'success',
                        {}
                    )
                },
                error: function (data, textStatus, errorThrown) {
                    console.log(data);console.log(errorThrown);
                    Swal.fire(
                        'Bad news :(',
                        'System failed to find suitable Stage, please specify one. <br>'+
                        'Err: '+errorThrown,

                        'error',
                        {}
                    )
                },
            });
        }
        function materialChanged() {
            console.log("populating stages DD");
            var stagesCheckBox = $("[name='" + repeaterName2 + "[autoStageDetect][]']");
            stagesCheckBox.parent().parent().show(250);
            var stagesDropDown = $("[name='" + repeaterName2 + "[newJobStage]']");
            var materialDropDown = $("[name='" + repeaterName2 + "[material_id]']");
            var materials = {!! json_encode($materials->toArray()) !!};
            var materialSelected = materials.find(x => x.id == $(materialDropDown).val());
            stagesDropDown.empty();
            stagesDropDown.append($("<option></option>")
                .attr("value", 0)
                .text("Select Stage"));

            if (materialSelected.design ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 1)
                    .text("Design"));
            if (materialSelected.mill ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 2)
                    .text("Milling"));
            if (materialSelected.print_3d ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 3)
                    .text("3D Printing"));
            if (materialSelected.sinter_furnace ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 4)
                    .text("Sintering Furnace"));
            if (materialSelected.press_furnace ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 5)
                    .text("Pressing Furnace"));
            if (materialSelected.metal_work ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 9)
                    .text("Metal Work"));
            if (materialSelected.finish ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 6)
                    .text("Finishing"));
            if (materialSelected.qc ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 7)
                    .text("QC"));
            if (materialSelected.delivery ==1 )
                stagesDropDown.append($("<option></option>")
                    .attr("value", 8)
                    .text("Delivery"));
        }

        function stageDetectCheckBox(ele) {
            console.log(repeaterName2);
            var stagesDropDown = $("[name='" + repeaterName2 + "[newJobStage]']");
            if(ele.checked) {
                stagesDropDown.hide(200);
            } else{
                stagesDropDown.show(200);
            }
        }

        function toggleDiscountPortion(ele) {

            var discountPortion = $(".discountPortion");
            if(ele.checked) {
                discountPortion.show(200);
            } else{
                discountPortion.hide(200);
            }
        }
        function jobTypeChanged2(jobTypeDD){
            var jobTypes = {!! json_encode($types->toArray()) !!};
            var materials = {!! json_encode($materials->toArray()) !!};
            var materialJobTypeRelations = {!! json_encode($jobTypeMaterials->toArray()) !!};
            var abutmentBox =  $("[name='" + repeaterName2 + "[abutment]']");
            var implantBox =  $("[name='" + repeaterName2 + "[implant]']");
            var materialBox = $("[name='"+repeaterName2  +"[material_id]']");
            var jobTypeSelectedId = $(jobTypeDD).val();
            var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId);
            materialBox.empty();
            $.each(jobTypeMaterials, function(key, value) {
                materialBox.append($("<option></option>")
                    .attr("value", value.material_id)
                    .text( materials.find(x => x.id === value.material_id).name));
            });
            console.log("New job type changed " +$(jobTypeDD).find(":selected").val() );
            materialChanged();

            if($(jobTypeDD).find(":selected").val() == 6 ){
                abutmentBox.parent().parent().parent().show();
                implantBox.parent().parent().parent().show();
            }
            else {
                abutmentBox.val(0);
                implantBox.val(0);
                abutmentBox.parent().parent().parent().hide();
                implantBox.parent().parent().parent().hide();
            }
        }

        $("#submitDialog2").click(function() {

            var teethSelectedAsArr = $("[name='"+lstSelectedJobUNName2+"']").val().split(',');
            var jobTypeBoxName = repeaterName2 + "[jobType]";
            var selectBtnName =  repeaterName2 + "[openDialogBtn]";

            var jobTypeBox=  $("[name='"+jobTypeBoxName+"']");
            var jobTypes = {!! json_encode($types->toArray()) !!};
            var colorsDDName = repeaterName2  +"[color]";
            var styleOptionsName = repeaterName2  +"[style]";
            /* Updating dropdowns according to teeth selection
             * First if is for jaws, second is for teeth
             * @Yazan - Korvex
             */
            console.log("[name='" + repeaterName2 + "[abutment]']");
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
                jobTypeChanged2(jobTypeBox);
                $("[name='"+colorsDDName+"']").parent().parent().parent().hide();

                // set style to none (prevent back-end errors) and hide it
                $("[name='"+styleOptionsName+"']").val('None');
                $("[name='"+styleOptionsName+"']").parent().parent().parent().hide();

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
                if(teethSelectedAsArr.length >1)
                    $("[name='"+styleOptionsName+"'][value='Bridge']").prop("checked",true);
                else
                    $("[name='"+styleOptionsName+"'][value='Single']").prop("checked",true);
                // Notify Job type changed function to update materials with which box changed
                jobTypeChanged2(jobTypeBox);
                $("[name='"+colorsDDName+"']").val($("[name='"+colorsDDName+"'] option:first").val());
                $("[name='"+colorsDDName+"']").parent().parent().parent().show();
                $("[name='"+styleOptionsName+"']").parent().parent().parent().show();
            }

            // Change button label with selected teeth
            if(teethSelectedAsArr.length >0)
                $("[name='"+selectBtnName+"']").html(teethSelectedAsArr.join(","));
            else
                $("[name='"+selectBtnName+"']").html("Select Units");




            // close dialog
            $(".modal").modal('hide');

        });


        $(".teeth2").click(function() {

            // Check if any jaws is selected, if any remove them from array
            if(jQuery.inArray("upper", teethSelected2) !== -1)
            {
                const jawIndex = teethSelected2.indexOf("upper");
                teethSelected2.splice(jawIndex, 1);
            }
            if(jQuery.inArray("lower", teethSelected2) !== -1)
            {
                const jawIndex = teethSelected2.indexOf("lower");
                teethSelected2.splice(jawIndex, 1);
            }

            // remove the light of the jaws buttons
            var list = $('.jaw2');
            list.removeClass("checked");


            //if not pre selected light up the teeth and add it to array
            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                var teethNumber = $(this).attr("alt");
                const index = teethSelected2.indexOf(teethNumber);

                if (index > -1) {
                    teethSelected2.splice(index, 1);
                }

                // remove the selection if previously selected
            } else {
                var teethNumber = $(this).attr("alt");
                teethSelected2.push(teethNumber);
                $(this).addClass("checked");
                // console.log("Added a teeth" + teethSelected);
            }

            //console.log("Updating units input : "  + teethSelected);

            $("[name='"+lstSelectedJobUNName2+"']").val(teethSelected2);
        });


        $(".jaw2").click(function() {

            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                var jaw = $(this).attr("alt");
                const index = teethSelected2.indexOf(jaw);

                if (index > -1) {
                    teethSelected2.splice(index, 1);
                }
                var unitNumsBox = $("[id=units]:last").attr("name");
                $("[name='"+unitNumsBox+"']").val(teethSelected2);

            } else {

                var jaw = $(this).attr("alt");
                // add visuall selection to the jaw the selection
                $(this).addClass("checked");

                // remove visual selection of all teeth if a jaw is selected
                var list = $('.teeth2');
                list.removeClass("checked");

                // remove all selected teeth
                for (var index = 0; index <= teethSelected2.length ; index++)
                {
                    if(teethSelected2[index] != "lower" && teethSelected2[index] != "upper" ){
                        teethSelected2.splice(index);
                    }
                }
                // add selected jaw to the array and update value
                teethSelected2.push(jaw);


            }

            $("[name='"+lstSelectedJobUNName2+"']").val(teethSelected2);
        });

        function preOpenDialog2(element) {
            // if repeater reached 2 digit or not
            if (element.name.length == 24) {
                lstSelectedJobUNName2 = element.name.substr(0, 9) + "[units]";
                repeaterName2 = element.name.substr(0, 9);

            }
            else {
                repeaterName2 = element.name.substr(0, 10);
                lstSelectedJobUNName2 = element.name.substr(0, 10) + "[units]";
            }
            console.log("reapter name set : " + repeaterName2);
            var currentJobUnits = $("[name='"+ lstSelectedJobUNName2+"']");
            // console.log("Current job units box name :" + element.name.substr(0,9) +  "[units]");
            if(typeof currentJobUnits !==  "undefined" && currentJobUnits.val()) {
                teethSelected2 = currentJobUnits.val().split(',');
                // console.log("is defined and its now : " + teethSelected);
            }
            else {
                // console.log("NOT defined,cleared");
                teethSelected2 = [];
            }
            if (teethSelected2.length !== 0 ){
                var teethPreSelected = currentJobUnits.val().split(',');
                // console.log("Lighting up : " + teethPreSelected);
                // light on and off according to the pre selected
                $(".teeth2").each(function(){
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1){
                        // console.log("true");
                        $(this).addClass("checked");}
                    else
                        $(this).removeClass("checked");
                });
                $(".jaw2").each(function(){
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected ) !== -1 )
                        $(this).addClass("checked");
                    else
                        $(this).removeClass("checked");
                });}
            else
            {
                $(".teeth2").removeClass("checked");
                $(".jaw2").removeClass("checked");
            }
        }


    </script>
    <script src="{{asset('assets/js/jquery.imagesloader-1.0.1.js')}}"></script>
    <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>
    <script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script>
    <script src="{{asset('assets/js/lightgallery.js')}}"></script>
@endpush

