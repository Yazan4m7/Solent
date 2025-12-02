@extends('layouts.app' ,[ 'pageSlug' => "New Case"])

@section('content')
    <link rel="stylesheet" href="assets/css/jquery.imagesloader.css"/>
    @php
        $color= "#212529";
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp
    <style>

        @media screen and (max-width: 991px) {
            .modal-content .modal-footer button {
                margin: 0;
                /*padding-left: 0px;*/
                /*padding-right: 2px;*/
                width: auto;
                white-space: break-spaces;
            }
        }


        .fa, .fas {
            color: black;
        }


        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }


        .hidden {
            display: none;
        }


        h5 {
            font-weight: bold;
        }

        .slctUnitsBtn {
            margin: 0;
            width: 100%;
            height: 100%; /* change this from auto */
            display: block;
            padding: 10px 5px !important;
            white-space: break-spaces !important;
        }


        .btn:not(.unstyled) {

        }


        .modal.show .modal-dialog {
            -webkit-transform: translate(0, 0%);
            transform: translate(0, 0%);
        }


        .row {
            padding: 0
        }


        .xdsoft_time_box {

            width: 100px !important;
        }

        .xdsoft_datetimepicker {
            padding-right: 50px;
        }

        hr {
            border-color: rgba(28, 86, 88, 0.81);
            margin-top: 0px
        }
        #addJobBtn2{
            background-color: #ca0399;
            border-color: #970371;
        }
        .purpleBorder{
            border: 1px solid #e14eca !important;
            border-radius: 0.5rem;
            background-color: #f8f9fa;

        }
        img{
            max-width:unset;
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 400px;
                margin: 1.75rem auto;
            }

        }
    </style>
    <div class="card">
        @if (config('site_vars.environment') == 'testing')
            <form style="padding:0px" class="kt-form" method="POST" enctype="multipart/form-data"
                  action="{{route('create-and-send-case-to')}}">
                @else
                    <form style="padding:10px" class="kt-form" method="POST" enctype="multipart/form-data"
                          action="{{route('new-case-post')}}">
                        @endif
                        @csrf
                        <div class="portlet__head">
                            <div class="portlet__head-label">
                                <h5 class="portlet__head-title">
                                    <i class="fa-solid fa-folder-closed" style="height:3%;color:inherit"></i> Case
                                    information
                                </h5>
                            </div>
                        </div>
                        <hr>
                        <!-- ORDER INFO -->
                        <div class="row">
                            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                                <div class="col-md-12 col-xs-12 noBottomPadding"><label class="noBottomMargin  bold">Doctor:</label>
                                </div>
                                <div class="col-md-12 col-xs-12 padding5px">

                                    <div class="dropdown">
                                        <select class="selectpicker greyBG" name="doctor" data-live-search="true"
                                                required title="Select a doctor" data-tap-disabled="true">


                                        @foreach($doctors as $doctor)
                                                    <option value="{{$doctor->id}}">{{$doctor->name}}</option>
                                                @endforeach

                                        </select>
                                        <small class="mandatorySmallTag">* Mandatory</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5  col-xs-6 col-l-5  col-xl-4">
                                <div class="col-md-12 col-xs-12 noBottomPadding"><label class="noBottomMargin bold">Patient
                                        name:</label></div>
                                <div class="col-md-12 col-xs-12 ">
                                    <input class="form-control blueTBBorder" type="text" name="patient_name"  required/>
                                    <small class="mandatorySmallTag">* Mandatory</small>
                                </div>
                            </div>
                            <div class="col-md-4  col-xs-6 col-l-4  col-xl-3">
                                <div class="col-md-12 col-xs-12 noBottomPadding"><label class="noBottomMargin bold">Case
                                        ID:</label></div>
                                <div class="col-md-12 col-xs-12">

                                    <label>{{$tempCaseId}}</label>
                                    <input type="hidden" name="temp_case_id" value="{{$tempCaseId}}">

                                </div>

                            </div>


                        </div>


                        <div class="row">
                            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                                <div class="col-md-12 col-xs-12"><label class="noBottomMargin bold">Impression
                                        Type:</label></div>
                                <div class="col-md-12 col-xs-12"><select class="form-control" name="impression_type"
                                                                         type="text" data-container="body"
                                                                         data-live-search="true"
                                                                         title="Select impression"
                                                                         data-hide-disabled="true">
                                        @foreach($impressionTypes as $impression)
                                            <option value="{{$impression->id}}">
                                                {{$impression->name}}
                                            </option>
                                        @endforeach

                                    </select></div>
                            </div>
                            <div class="col-md-5  col-xs-6 col-l-5  col-xl-4">
                                <div class="col-md-12 col-xs-12"><label class="noBottomMargin bold">Delivery
                                        Date:</label></div>
                                @php
                                    $time = new DateTime("tomorrow 13:00");
                                   // $time = $time->format("Y-m-d\TH:i");
                                    $time = $time->format("d M, Y h:i a");
                                @endphp

                                <div class="col-md-12 col-xs-12">
                                    <input class="form-control SDTP" name="delivery_date" type="text" value="{{$time}}"
                                           required readonly/>
                                    <small class="mandatorySmallTag">* Mandatory</small>
                                </div>
                            </div>
                            <div class="col-md-4  col-xs-6 col-l-4  col-xl-3">
                                <div class="col-md-12 col-xs-12"><label class="noBottomMargin bold">Tags:</label></div>
                                <div class="col-md-12 col-xs-12">
                                    <select class="select selectpicker greyBG" name="tags[]" multiple
                                            data-mdb-placeholder="Tags">
                                        @foreach($tags as $tag)
                                            <option style="color:{{$tag->color}}"
                                                    value="{{$tag->id}}">{{$tag->text}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="verticalSpacing"></div>

                        <!-- JOB INFO ICON-->


                        <div class="portlet__head">
                            <div class="portlet__head-label">
                                <h5 class="portlet__head-title">
                                    <i class="fa-solid fa-boxes-stacked" style="height:3%;color:inherit"></i> Jobs
                                    information
                                </h5>
                            </div>
                        </div>
                        <hr>
                        <!-- JOBS REPEATER -->
                        <div id="" style="" class="repeater jobsRepeater">
                            <div data-repeater-list="repeat" >
                                <div data-repeater-item class="jobRow">
                                    <div class="form-group form-group ">
                                        <div data-repeater-list="repeat" class="col-12 padding5px">
                                            <div data-repeater-item class="form-group row align-items-center row-item"
                                                 style="border: 1px solid #ccc;border-radius: 16px;padding:5px">


                                                <div class="col-md-2">
                                                    <div class="kt-form__label">
                                                        <label class="kt-label m-label--single bold">Units:</label>
                                                    </div>
                                                    <input type="hidden" name="units" id="units"
                                                           class="hiddenUnitsInput" required>
                                                    <button type="button" class="btn btn-secondary slctUnitsBtn"
                                                            data-toggle="modal" data-target="#unitsDialog"
                                                            name="openDialogBtn"
                                                            onclick="preOpenDialog(this)">Select Units</button>

                                                </div>
                                                <div class="col-md-2">
                                                    <div class="kt-form__group--inline">
                                                        <div class="kt-form__label">
                                                            <label class="kt-label m-label--single">Job type:</label>
                                                        </div>
                                                        <div class="kt-form__control">
                                                            <select class="form-control" id="jobType" name="jobType"
                                                                    onchange="jobTypeChanged(this)">
                                                                @foreach($types as $type)
                                                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="kt-form__group--inline">
                                                        <div class="kt-form__label">
                                                            <label>Material:</label>
                                                        </div>
                                                        <div class="kt-form__control">
                                                            <select class="form-control" id="material_id"
                                                                    name="material_id">

                                                                @foreach($materials as $m)
                                                                    <option value="{{$m->id}}">
                                                                        {{$m->name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="kt-form__group--inline">
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
                                                <div class="col-md-2">
                                                    <div class="kt-form__group--inline">
                                                        <div class="kt-form__label">
                                                            <label>Style:</label>
                                                        </div>
                                                        <div class="kt-radio-inline">
                                                            <label class="kt-radio">
                                                                <input type="radio" class="single" checked="checked"
                                                                       name="style" value="Single"> Single
                                                                <span></span>
                                                            </label>
                                                            <label class="kt-radio">
                                                                <input type="radio" class="bridge" name="style"
                                                                       value="Bridge"> Bridge
                                                                <span></span>
                                                            </label>

                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- DELETE BUTTON -->
                                                <div class="col-md-2">
                                                    <div class="kt-form__group--inline">

                                                        <div class="kt-form__control">
                                                            <button data-repeater-delete class="btn deleteBtn btn-sm"
                                                                    type="button" value="Delete" style=""><i
                                                                        class="fa fa-trash " style=""></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 abutment abutmentsArea" style="display:none;">

                                                    <!-- inner repeater -->
                                                    <div class="abutments-repeater abutmentsRepeater">
                                                        <div data-repeater-list="abutments" class="dataRepeaterList">
                                                                    <div data-repeater-item class="abutmentsRow">
                                                                        <div class="row" style="align-items: flex-end;margin: 10px 0px;border: 1px solid #e14eca;border-radius: 0.5rem; padding: 10px 10px;">
                                                                        <div class="col-md-3">
                                                                                <label class="kt-label m-label--single">Abt./Implant Units:</label>
                                                                            <select class="select abutmentsUnitsPicker greyBG purpleBorder" name="abutmentUnits[]" multiple
                                                                                    data-mdb-placeholder="Tags">

                                                                            </select>
                                                                            </div>
                                                                            <div class="col-md-2" >
                                                                                <label class="kt-label m-label--single">Implant
                                                                                    type:</label>
                                                                                <select class="form-control purpleBorder"
                                                                                        id="implant" name="implant" >
                                                                                    <option value="0" selected>None
                                                                                    </option>
                                                                                    @foreach($implants as $implant)
                                                                                        <option value="{{$implant->id}}">{{$implant->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        <div class="col-md-2">
                                                                        <label class="kt-label m-label--single">Abutment
                                                                            type:</label>
                                                                        <select class="form-control purpleBorder" id="abutment"
                                                                                name="abutment">
                                                                            <option value="0" selected>None</option>
                                                                            @foreach($abutments as $abutment)
                                                                                <option value="{{$abutment->id}}">{{$abutment->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                                <label class="kt-label m-label--single">Code:</label>

                                                                                    <input type="text" name="abutmentCode" class="form-control purpleBorder">

                                                                            </div>
                                                                        <div class="col-md-1">
                                                                        <button data-repeater-delete class="btn deleteBtn2 btn-sm"
                                                                                type="button" value="Delete" style=""><i
                                                                                    class="fa fa-trash " style=""></i></span>
                                                                        </button>
                                                                        </div>
                                                                        </div>

                                                        </div>

                                                    </div>
                                                        <a href="javascript:" data-repeater-create="" class="btn btn-success btn-sm" id="addJobBtn2"  onClick = "addAbutmentJob(this)">
                                                            <i class="fa fa-plus-square" style="color:white"></i> Add Abutment
                                                        </a>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                            <a href="javascript:" data-repeater-create="" class="btn btn-success btn-sm" id="addJobBtn">
                                <i class="fa fa-plus-square" style="color:white"></i> Add
                            </a>
                        <div class="verticalSpacing"></div>
                        <!-- DISCOUNTS SECTION -->
                        @if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 114))))
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h5 class="kt-portlet__head-title">
                                        <i class="fa-regular fa-circle-down" style="height:3%"></i> Discount
                                    </h5>
                                </div>
                            </div>
                            <hr>
                            <label style="cursor: pointer">
                                <input type="checkbox" class="discountCB" name="discountCB"
                                       onclick='toggleDiscountPortion(this)'/>
                                Make a Discount
                            </label>
                            <br>
                            <div class="form-group form-group row discountPortion" style="display:none">
                                <div class="col-md-3 col-xs-6">
                                    <input class="form-control" type="number" name="discount_amount"
                                           placeholder="Amount (JOD)"/>
                                    <small>JOD</small>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <input class="form-control" type="text" name="discount_reason"
                                           placeholder="Explanation of discount"/></textarea>
                                </div>
                            </div>
                            <div class="verticalSpacing"></div>
                        @endif

                          <!-- NOTES SECTION -->
                        <br>
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h5 class="kt-portlet__head-title">
                                    <i class="fa fa-sticky-note" style="height:3%;color:inherit"></i> Additional
                                    information
                                </h5>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group form-group-last">
                            <label for="exampleTextarea">Note</label>
                            <textarea class="form-control" name="note" id="exampleTextarea"
                                      rows="3">{{old('note')}}</textarea>
                        </div>
                        <div class="verticalSpacing"></div>
                        <!-- Attachments SECTION -->
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h5 class="kt-portlet__head-title">
                                    <i class="fa fa-photo" style="height:3%;color:inherit"></i> Attachments
                                </h5>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group form-group-last">
                            <label for="images" style="cursor: pointer;"><h4><i class="fa-solid fa-circle-plus"></i>
                                </h4></label>
                            <input type="file" id="images" class="form-control" name="images[]" placeholder="Upload any file type (max 50MB each)"
                                   multiple style="cursor: pointer;">
                            <small class="form-text text-muted">Upload any file type. Maximum size: 50MB per file.</small>

                            <!-- File Upload Preview Container -->
                            <div id="file-preview-container" class="mt-3" style="display: none;">
                                <h6>Selected Files:</h6>
                                <div id="file-preview-list" class="row"></div>
                            </div>
                        </div>

                        <br>
                        @if (config('site_vars.environment') == 'testing')

                            <div class="col-md-3"
                                 style="border: 1px solid red;padding:10px;border-radius: 10px;margin:5px">
                                <div class="kt-form__actions"><label for="sendTo">Testing helpers:</label><br>
                                    <div class="btn-group show" role="group">
                                        <select class="form-control" id="stageToSendTo" name="stageToSendTo">

                                            <option value="1">Design</option>
                                            <option value="6">Finishing</option>
                                            <option value="7">QC</option>
                                            <option value="8">Delivery</option>
                                            <option value="10" style="color:green">Completed</option>
                                        </select>

                                    </div>
                                </div>
                            </div>

                        @endif

                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <button type="submit" class="btn btn-primary extraPadding">Submit</button>

                            </div>
                        </div>
                        </div>
                    </form>

    <!-- TEETH PICK DIALOG -->

    <div data-repeater-item class="modal fade" id="unitsDialog" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document" style="margin-top: 5px;">
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

                        {{--<img class="jaw lowerJaw" alt="lower" src="/assets/teethPics/lower-jaw.png" width=180px--}}
                             {{--style="position: absolute; top: 330px;left: 150px;">--}}

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
                <div class="modal-footer" name="model-footer" style="padding-top:45px">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog" onclick="">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- FILES DIALOG -->

    <div class="modal fade" id="filesDialog" tabindex="-1" role="dialog" aria-labelledby="fileDialog"
         style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle-1">Upload files </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer" name="model-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog" onclick="">Save changes</button>
                </div>
            </div>
        </div>
    </div>



@endsection
@push('js')

<script src="{{asset('assets/js/jquery.repeater3.min.js')}}" defer></script>
<script>

    $(document).ready(function () {
        $('.selectpicker').selectpicker();
        $('.selectpicker').selectpicker('refresh');
        $('.repeater').repeater({
            // (Required if there is a nested repeater)
            // Specify the configuration of the nested repeaters.
            // Nested configuration follows the same format as the base configuration,
            // supporting options "defaultValues", "show", "hide", etc.
            // Nested repeaters additionally require a "selector" field.
            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.abutments-repeater',
                show: function () {
                    $(this).slideDown();
                },

                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],


            defaultValues: {},

            show: function () {
                $(this).slideDown();
            },
            initEmpty: false,
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });

        // removing first job because it causes UI errors with the repeater
        $(".jobsRepeater").find(".jobRow").first().html("");
        $("#addJobBtn").click();
//        $(".abutmentsRepeater").find(".abutmentsRow").first().html("");
//        $("#addJobBtn2").click();


    });
</script>
<script>


    function toggleDiscountPortion(ele) {

        var discountPortion = $(".discountPortion");
        if (ele.checked) {
            discountPortion.show(200);
        } else {
            discountPortion.hide(200);
        }
    }

    var teethSelected = [];
    var lstSelectedJobUNName = "";
    var repeaterName = ""; // should be something like 'repeat[xx]'
    function jobTypeChanged(jobTypeDD) {
        var thisRowRepeaterName = $(jobTypeDD).attr("name").replace('[jobType]','');
        console.log($(jobTypeDD).val());
        var jobTypes = {!! json_encode($types->toArray()) !!};
        var materials = {!! json_encode($materials->toArray()) !!};
        var materialJobTypeRelations = {!! json_encode($jobTypeMaterials->toArray()) !!};
        var repeaterNumber = thisRowRepeaterName.replace('repeat[', '').replace(']', '');

        var colorsDDName = repeaterName + "[color]";
        if ($(jobTypeDD).val() == 14)
        {
            $("[name='" + colorsDDName + "']").parent().parent().parent().show();
        }

        if (repeaterNumber > 1){
            var implantBox = $("[name='repeat[" + (repeaterNumber -1)  + "][abutments][0][implant]']");
            var abutmentBox = $("[name='repeat[" + (repeaterNumber -1) + "][abutments][0][abutment]']");
            var abutUnitsBox = $("[name='repeat[" + (repeaterNumber -1) + "][abutments][0][abutmentUnits][]']");

          //  console.log("selector : " +"[name='repeat[" + (repeaterNumber -1) + "][abutments][0][abutmentUnits][]']");
        }

            else
        {
            var implantBox = $("[name='" + thisRowRepeaterName + "[abutments][0][implant]']");
            var abutmentBox = $("[name='" + thisRowRepeaterName + "[abutments][0][abutment]']");
            var abutUnitsBox = $("[name='" + thisRowRepeaterName + "[abutments][0][abutmentUnits][]']");
          //  console.log("selector : " + "[name='" + thisRowRepeaterName + "[abutments][0][abutmentUnits][]']");
        }

        var teethSelectedAsArr = $("[name='" + lstSelectedJobUNName + "']").val().split(',');

        var materialBox = $("[name='" + repeaterName + "[material_id]']");
        var openDialogBtn = $("[name='" + repeaterName + "[openDialogBtn]']");
        var jobTypeSelectedId = $(jobTypeDD).val();
        var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId
        );

        materialBox.empty();

        const options = jobTypeMaterials
            .map(v => {
                const mat = materials.find(x => x.id == v.material_id);
                if (!mat) return ''; // skip if missing
                return `<option value="${v.material_id}">${mat.name}</option>`;
            })
            .join('');

        materialBox.append(options);
        var abutmentsArea =  $(jobTypeDD).parent().parent().parent().parent().parent().find(".abutmentsArea");
        var abutmentUnitsBox =  $(abutmentsArea).find(".abutmentsUnitsPicker");
        var currentlySelectedUnits = $(jobTypeDD).parent().parent().parent().parent().parent().find(".hiddenUnitsInput").val().split(',');
        if ($(jobTypeDD).find(":selected").val() == 6) {

            // get to parent of the main repeater and find abutment units box

            $(abutmentBox).attr('required', '');
            $(implantBox).attr('required', '');

            $(abutmentsArea).css("display","block");
              // $(".abutmentsUnitsPicker").find('option').html('');
            // show the 6th parent of the box which has display none property
           // $(found).parent().parent().parent().parent().parent().parent().css("display","block");

            $.each(currentlySelectedUnits  , function( index, value ) {
                abutmentUnitsBox.append($("<option></option>")
                    .attr("value", value)
                    .text(value));});
            abutmentUnitsBox.selectpicker();
            $(jobTypeDD).attr("readonly","true");
            $(openDialogBtn).attr("disabled","true");
             }
        else {
            $(abutmentBox).removeAttr('required');
            $(implantBox).removeAttr('required');
            $(abutmentsArea).css("display","none");
             abutmentUnitsBox.val(0);
//            implantBox.val(0);
          // $(found).parent().parent().parent().parent().parent().parent().css("display","none");
        }
    }

    function addAbutmentJob(ele){
        // get units selected originally in the job
        var teethSelectedAsArr = $("[name='" + lstSelectedJobUNName + "']").val().split(',');
        // wait for new repeater row to populate then add unit selected to abutment units box
        setTimeout(function(){
            var lastAbutmentUnitsBox = $("select[name$='[abutmentUnits][]']").last();


            $.each(teethSelectedAsArr  , function( index, value ) {
                $(lastAbutmentUnitsBox).last().append($("<option></option>")
                    .attr("value", value)
                    .text(value));
            });
            lastAbutmentUnitsBox.selectpicker();
        }, 500);

    }

    $("#submitDialog").click(function () {

        var teethSelectedAsArr = $("[name='" + lstSelectedJobUNName + "']").val().split(',');
        var jobTypeBoxName = repeaterName + "[jobType]";
        var selectBtnName = repeaterName + "[openDialogBtn]";

        var jobTypeBox = $("[name='" + jobTypeBoxName + "']");
        var jobTypes = {!! json_encode($types->toArray()) !!};
        var colorsDDName = repeaterName + "[color]";
        var styleOptionsName = repeaterName + "[style]";
        /* Updating dropdowns according to teeth selection
         * First if is for jaws, second is for teeth
         * @Yazan -
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
            jobTypeChanged(jobTypeBox);
            $("[name='" + colorsDDName + "']").parent().parent().parent().hide();

            // set style to none (prevent back-end errors) and hide it
            $("[name='" + styleOptionsName + "']").val('None');
            $("[name='" + styleOptionsName + "']").parent().parent().parent().hide();

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
            if (teethSelectedAsArr.length > 1)
                $("[name='" + styleOptionsName + "'][value='Bridge']").prop("checked", true);
            else
                $("[name='" + styleOptionsName + "'][value='Single']").prop("checked", true);
            // Notify Job type changed function to update materials with which box changed
            jobTypeChanged(jobTypeBox);

        }

        // Change button label with selected teeth
        if (teethSelectedAsArr.length > 0)
            $("[name='" + selectBtnName + "']").html(teethSelectedAsArr.join(","));
        else
            $("[name='" + selectBtnName + "']").html("Select Units");


        $("[name='" + colorsDDName + "']").val($("[name='" + colorsDDName + "'] option:first").val());

        // close dialog
        $(".modal").modal('hide');

    });


    $(".teeth").click(function () {

        // Check if any jaws is selected, if any remove them from array
        if (jQuery.inArray("upper", teethSelected) !== -1) {
            const jawIndex = teethSelected.indexOf("upper");
            teethSelected.splice(jawIndex, 1);
        }
        if (jQuery.inArray("lower", teethSelected) !== -1) {
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

        $("[name='" + lstSelectedJobUNName + "']").val(teethSelected);
    });
    $(".jaw").click(function () {

        if ($(this).hasClass("checked")) {
            $(this).removeClass("checked");
            var jaw = $(this).attr("alt");
            const index = teethSelected.indexOf(jaw);

            if (index > -1) {
                teethSelected.splice(index, 1);
            }
            var unitNumsBox = $("[id=units]:last").attr("name");
            $("[name='" + unitNumsBox + "']").val(teethSelected);

        } else {

            var jaw = $(this).attr("alt");
            // add visuall selection to the jaw the selection
            $(this).addClass("checked");

            // remove visual selection of all teeth if a jaw is selected
            var list = $('.teeth');
            list.removeClass("checked");

            // remove all selected teeth
            for (var index = 0; index <= teethSelected.length; index++) {
                if (teethSelected[index] != "lower" && teethSelected[index] != "upper") {
                    teethSelected.splice(index);
                }
            }
            // add selected jaw to the array and update value
            teethSelected.push(jaw);


        }

        $("[name='" + lstSelectedJobUNName + "']").val(teethSelected);
    });

    function preOpenDialog(element) {
        // if repeater reached 2 digit or not
        if (element.name.length == 24) {
            lstSelectedJobUNName = element.name.substr(0, 9) + "[units]";
            repeaterName = element.name.substr(0, 9);
        }
        else {
            repeaterName = element.name.substr(0, 10);
            lstSelectedJobUNName = element.name.substr(0, 10) + "[units]";
        }
        var currentJobUnits = $("[name='" + lstSelectedJobUNName + "']");
        // console.log("Current job units box name :" + element.name.substr(0,9) +  "[units]");
        if (typeof currentJobUnits !== "undefined" && currentJobUnits.val()) {
            teethSelected = currentJobUnits.val().split(',');
            // console.log("is defined and its now : " + teethSelected);
        }
        else {
            // console.log("NOT defined,cleared");
            teethSelected = [];
        }
        if (teethSelected.length !== 0) {
            var teethPreSelected = currentJobUnits.val().split(',');
            // console.log("Lighting up : " + teethPreSelected);
            // light on and off according to the pre selected
            $(".teeth").each(function () {
                if (jQuery.inArray($(this).attr("alt"), teethPreSelected) !== -1) {
                    // console.log("true");
                    $(this).addClass("checked");
                }
                else
                    $(this).removeClass("checked");
            });
            $(".jaw").each(function () {
                if (jQuery.inArray($(this).attr("alt"), teethPreSelected) !== -1)
                    $(this).addClass("checked");
                else
                    $(this).removeClass("checked");
            });
        }
        else {
            $(".teeth").removeClass("checked");
            $(".jaw").removeClass("checked");
        }
    }


</script>
<script src="{{asset('assets/js/jquery.imagesloader-1.0.1.js')}}"></script>
{{--<script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script>--}}
{{--<script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script>--}}
{{--<script src="{{asset('assets/js/jquery.repeater3.min.js')}}" defer></script>--}}

<script src="{{asset('assets/js/lightgallery.js')}}"></script>

<script>
// File upload preview functionality
$(document).ready(function() {
    $('#images').on('change', function(e) {
        const files = e.target.files;
        const previewContainer = $('#file-preview-container');
        const previewList = $('#file-preview-list');

        // Clear previous previews
        previewList.empty();

        if (files.length > 0) {
            previewContainer.show();

            Array.from(files).forEach(function(file, index) {
                const fileName = file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                const isImage = file.type.startsWith('image/');

                let previewHtml = '';

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imagePreview = `
                            <div class="col-md-3 col-sm-4 mb-3">
                                <div class="file-preview-item" style="border: 1px solid #ddd; padding: 10px; border-radius: 5px; position: relative; background: #f8f9fa;">
                                    <img src="${e.target.result}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 3px;">
                                    <div class="file-info" style="margin-top: 5px; font-size: 11px;">
                                        <div title="${file.name}">${fileName}</div>
                                        <div class="text-muted">${fileSize}</div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-file" data-index="${index}"
                                            style="position: absolute; top: 5px; right: 5px; padding: 2px 6px; font-size: 10px;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        previewList.append(imagePreview);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const filePreview = `
                        <div class="col-md-3 col-sm-4 mb-3">
                            <div class="file-preview-item" style="border: 1px solid #ddd; padding: 10px; border-radius: 5px; position: relative; background: #f8f9fa; text-align: center;">
                                <i class="fa fa-file" style="font-size: 40px; color: #6c757d;"></i>
                                <div class="file-info" style="margin-top: 5px; font-size: 11px;">
                                    <div title="${file.name}">${fileName}</div>
                                    <div class="text-muted">${fileSize}</div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-file" data-index="${index}"
                                        style="position: absolute; top: 5px; right: 5px; padding: 2px 6px; font-size: 10px;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    previewList.append(filePreview);
                }
            });
        } else {
            previewContainer.hide();
        }
    });

    // Handle file removal
    $(document).on('click', '.remove-file', function() {
        const indexToRemove = $(this).data('index');
        const fileInput = document.getElementById('images');
        const dt = new DataTransfer();

        Array.from(fileInput.files).forEach(function(file, index) {
            if (index !== indexToRemove) {
                dt.items.add(file);
            }
        });

        fileInput.files = dt.files;
        $('#images').trigger('change');
    });
});
</script>

@endpush

