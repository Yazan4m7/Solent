@extends('layouts.app' ,[ 'pageSlug' => "Edit Case"])

@section('content')
    @php
        $currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
    @endphp
    <link rel="stylesheet" href="{{asset('assets/css/lightgallery.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/jquery.imagesloader.css')}}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/lightgallery/1.3.9/css/lightgallery.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Albasma Case Form Styling -->
    <style>
        /* ========================================
           Albasma DENTAL LAB - CASE FORM STYLING
           ======================================== */

        :root {
            --Albasma-primary: #6A94AF;
            --Albasma-secondary: #1E4157;
            --Albasma-accent: #043c4d;
            --Albasma-light: #E1D9D5;
            --Albasma-dark: #000C19;
            --Albasma-gray: #9FA5AD;
            --Albasma-success: #2dce89;
            --Albasma-warning: #ff8d72;
        }

        /* Main Form Card */
        .kt-form.card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(106, 148, 175, 0.15);
            border: none;
            overflow: hidden;
        }

        /* Form Header Sections */
        .kt-portlet__head {
            background: linear-gradient(135deg, var(--Albasma-primary) 0%, var(--Albasma-secondary) 100%);
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(106, 148, 175, 0.2);
        }

        .kt-portlet__head-title {
            color: white !important;
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        .kt-portlet__head i {
            color: white;
            margin-right: 12px;
            font-size: 1.3rem;
        }

        /* Form Labels */
        label, .kt-label {
            color: var(--Albasma-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Form Inputs */
        .form-control,
        .selectpicker,
        select.form-control {
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus,
        .selectpicker:focus,
        select.form-control:focus {
            border-color: var(--Albasma-primary);
            box-shadow: 0 0 0 3px rgba(106, 148, 175, 0.15);
            outline: none;
        }

        /* Job Repeater Items */
        .row-item {
            background: linear-gradient(135deg, rgba(225, 217, 213, 0.3) 0%, rgba(255, 255, 255, 0.9) 100%);
            border: 2px solid var(--Albasma-light) !important;
            border-radius: 16px !important;
            padding: 20px !important;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .row-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(106, 148, 175, 0.15);
            border-color: var(--Albasma-primary) !important;
        }

        /* Unit Selection Button */
        .slctUnitsBtn {
            margin: 0;
            width: 100%;
            height: 100%;
            display: block;
            background: linear-gradient(135deg, var(--Albasma-primary) 0%, var(--Albasma-secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(106, 148, 175, 0.3);
        }

        .slctUnitsBtn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 148, 175, 0.4);
            background: linear-gradient(135deg, var(--Albasma-secondary) 0%, var(--Albasma-accent) 100%);
        }

        .slctUnitsBtn:disabled {
            background: #e1e8ed;
            color: #9FA5AD;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Add Job Button */
        #addJobBtn {
            background: linear-gradient(135deg, var(--Albasma-success) 0%, #20a86b 100%) !important;
            border: none !important;
            padding: 14px 28px !important;
            border-radius: 10px !important;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(45, 206, 137, 0.3);
            transition: all 0.3s ease;
        }

        #addJobBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(45, 206, 137, 0.4);
        }

        /* Select Pickers */
        .bootstrap-select .dropdown-toggle {
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 16px;
            background: white;
        }

        .bootstrap-select .dropdown-toggle:focus {
            border-color: var(--Albasma-primary) !important;
            box-shadow: 0 0 0 3px rgba(106, 148, 175, 0.15);
        }

        /* Green/Grey Background Classes */
        .greenBG {
            background-color: rgba(45, 206, 137, 0.1) !important;
            border-color: var(--Albasma-success) !important;
        }

        .greyBG {
            background-color: rgba(225, 217, 213, 0.3) !important;
        }

        /* Horizontal Rules */
        hr {
            border-top: 2px solid var(--Albasma-light);
            margin: 25px 0;
        }

        /* Row Spacing */
        .row {
            margin-bottom: 15px;
        }

        .case-summary-row {
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .case-summary-row > [class*="col-"] {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .case-summary-row > [class*="col-"] > .col-md-12,
        .case-summary-row > [class*="col-"] > .col-md-6 {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .case-summary-row .form-control,
        .case-summary-row select.form-control,
        .case-summary-row .bootstrap-select,
        .case-summary-row .bootstrap-select > .dropdown-toggle {
            width: 100% !important;
            margin: 0;
        }

        /* Modal Adjustments */
        @media screen and (max-width: 991px) {
            .modal-content .modal-footer button {
                margin: 0;
                width: auto;
                white-space: break-spaces;
            }
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 400px;
                margin: 1.75rem auto;
            }
        }

        /* Stage and Implant Fields */
        .stage, .implant, .abutment {
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .implant, .abutment {
            padding-left: 0px !important;
        }

        /* Image Styling */
        img {
            max-height: 100%;
            max-width: 100%;
            border-radius: 8px;
        }

        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }

        .hidden {
            display: none;
        }

        /* Sweet Alert Override */
        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow-y: visible !important;
        }

        /* Submit/Action Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--Albasma-primary) 0%, var(--Albasma-secondary) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(106, 148, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 148, 175, 0.4);
            background: linear-gradient(135deg, var(--Albasma-secondary) 0%, var(--Albasma-accent) 100%);
        }

        /* Form Section Spacing */
        .kt-form {
            padding: 30px !important;
        }

        /* Input Group Inline */
        .kt-form__group--inline {
            margin-bottom: 15px;
        }

        /* Color Select Enhancement */
        select#color option {
            padding: 8px;
        }

        /* Secondary Buttons (Close, Cancel) */
        .btn-secondary {
            background: linear-gradient(135deg, var(--Albasma-gray) 0%, #8a9199 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(159, 165, 173, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(159, 165, 173, 0.4);
            background: linear-gradient(135deg, #8a9199 0%, #6b717a 100%);
            color: white;
        }

        /* Danger Buttons (Delete, Reset, Remove) */
        .btn-danger,
        .deleteBtn,
        .deleteBtn2 {
            background: linear-gradient(135deg, #fd5d93 0%, #e53e3e 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(253, 93, 147, 0.3);
        }

        .btn-danger:hover,
        .deleteBtn:hover,
        .deleteBtn2:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(253, 93, 147, 0.4);
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }

        .btn-danger.btn-sm,
        .deleteBtn.btn-sm,
        .deleteBtn2.btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        /* Delete Attachment Button */
        .delete-attachment {
            border-radius: 6px;
        }

        /* Extra Padding Class */
        .extraPadding {
            padding: 14px 32px !important;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Data Repeater Delete Buttons */
        [data-repeater-delete] {
            transition: all 0.3s ease;
        }

        [data-repeater-delete]:hover {
            transform: scale(1.05);
        }

        /* Modal Buttons */
        .modal-footer .btn {
            min-width: 120px;
        }

        /* Submit Dialog Buttons */
        #submitDialog,
        #submitDialog2 {
            min-width: 150px;
        }

        /* Form Action Buttons Container */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid var(--Albasma-light);
        }

        /* Image Upload Area Enhancement */
        .image-upload-area {
            border: 2px dashed var(--Albasma-primary);
            border-radius: 12px;
            padding: 25px;
            background: rgba(106, 148, 175, 0.05);
            transition: all 0.3s ease;
        }

        .image-upload-area:hover {
            background: rgba(106, 148, 175, 0.1);
            border-color: var(--Albasma-secondary);
        }

        /* Notes Section */
        .notes-section {
            background: rgba(225, 217, 213, 0.2);
            border-left: 4px solid var(--Albasma-primary);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        /* Tags Enhancement */
        .select.selectpicker.greenBG {
            transition: all 0.3s ease;
        }

        .select.selectpicker.greenBG:focus {
            box-shadow: 0 0 0 3px rgba(45, 206, 137, 0.2);
        }

        /* Case ID Input Group */
        input[name^="caseId"] {
            border: 2px solid #e1e8ed;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        input[name^="caseId"]:focus {
            border-color: var(--Albasma-primary);
            box-shadow: 0 0 0 3px rgba(106, 148, 175, 0.15);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .kt-form {
                padding: 15px !important;
            }

            .row-item {
                padding: 15px !important;
            }

            .kt-portlet__head {
                padding: 15px 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp


    <form style="padding:10px" class="kt-form card" method="POST" enctype="multipart/form-data" action="{{route('edit-case')}}">
    @csrf


        <input name="id" type="hidden" value="{{$case->id}}" />
    <!-- CASE INFO -->

        <div class="row case-summary-row">
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Doctor:</label></div>
                <div class="col-md-12 col-xs-12">


                    <select class="selectpicker greyBG" name="doctor" data-live-search="true"
                            required title="Select a doctor" data-tap-disabled="true">


                    @foreach($clients as $client)
                            <option value="{{$client->id}}" {{$case->client->id == $client->id ? "selected" : ""}} >{{$client->name}}</option>
                        @endforeach

                    </select>

                </div> </div>
            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-12 col-xs-12"><label >Patient name:</label></div>
                <div class="col-md-12 col-xs-12"><input class="form-control" type="text" name="patient_name" value="{{$case->patient_name}}"  /></div>
            </div>


            <div class="col-md-3  col-xs-6 col-l-3  col-xl-3">
                <div class="col-md-6 col-xs-12"><label>Case ID:</label></div>
                <div class="col-md-12 col-xs-12">

                    <label >{{$case->case_id}}</label>

                </div>

            </div>

        </div>

            <br/>
        <div class="row case-summary-row">

            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Delivery Date:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control SDTP" name="delivery_date"  type="text"   value="{{$case->initial_delivery_date}}" required readonly/>
                   </div>
            </div>
            <div class="col-md-4  col-xs-6 col-l-2  col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Tags:</label></div>
                <div class="col-md-12 col-xs-12">

                    <select class="select selectpicker greenBG" name="tags[]"  data-mdb-placeholder="Tags" multiple >

                        @foreach($tags as $tag)
                            <option style="color:{{$tag->color}}" value="{{$tag->id}}" {{in_array($tag->id ,$tagsAsArray) ? 'selected' : ''}}>{{$tag->text}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-4 col-xs-6 col-l-2 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Impression Type:</label></div>
                <div class="col-md-12 col-xs-12"> <select  class="form-control" name="impression_type" type="text"  data-container="body" data-live-search="true" title="Select impression" data-hide-disabled="true"  >

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

                                        <input type="hidden"  name="units{{$job->id}}" id="units" class="hiddenUnitsInput" value="{{$job->unit_num}}" />
                                        <button {{$job->jobType->id == 6 ? 'disabled' : ''}} type="button" class="btn btn-secondary slctUnitsBtn"  data-toggle="modal" data-target="#unitsDialog" name="openDialogBtn{{$job->id}}" onclick="preOpenDialog(this,{{$job->id}})">
                                            {{$job->unit_num}}
                                        </button>

                                    </div>

                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Job type:</label>
                                        </div>
                                        <div class="kt-form__control">

                                            <select {{$job->jobType->id == 6 ? 'disabled' : ''}} class="form-control" id="jobType" name="jobType{{$job->id}}" onchange="jobTypeChanged(this,{{$job->id}})">

                                                @foreach($types as $type)
                                                    <option value="{{$type->id}}" {{$type->id == $job->type ? 'selected' : ''}}>{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                            @if($job->jobType->id == 6)
                                                <input type="hidden"  name="jobType{{$job->id}}" value="{{$job->jobType->id}}">
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label>Material:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select {{$job->jobType->id == 6 ? 'disabled' : ''}} class="form-control" id="material_id" name="material_id{{$job->id}}">

                                                @foreach($materials as $m)
                                                    <option value="{{$m->id}}" {{$job->material_id == $m->id ? 'selected' : ''}}>
                                                        {{$m->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($job->jobType->id == 6)
                                                <input type="hidden"  name="material_id{{$job->id}}" value="{{$job->material->id}}">
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2"><div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label>Color:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select {{$job->jobType->id == 6 ? 'disabled' : ''}} class="form-control" id="color" name="color{{$job->id}}">
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
                                            @if($job->jobType->id == 6)
                                                <input type="hidden"  name="color{{$job->id}}" value="{{$job->color}}">
                                            @endif
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
                                                <input {{$job->jobType->id == 6 ? 'disabled' : ''}} type="radio" class="bridge" name="style{{$job->id}}" value="Bridge" {{$job->style == "Bridge" ? 'checked' : '' }} /> Bridge
                                                <span></span>
                                            </label>
                                            <label class="kt-radio">
                                                <input {{$job->jobType->id == 6 ? 'disabled' : ''}} type="radio" class="single" {{$job->style == "Single" ? 'checked' : '' }} name="style{{$job->id}}"  value="Single" /> Single
                                                <span></span>
                                            </label>
                                            @if($job->jobType->id == 6)
                                                <input type="hidden"   name="style{{$job->id}}" value="{{$job->style}}">
                                            @endif
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
                                                    <button data-repeater-delete  class="btn btn-danger  btn-sm" type="button" value="Delete" style="height:100%"> <i class="fa fa-trash"></i></span> </button>
                                                </div>
                                            </div>
                                        </div>

                                </div>

                                </div>

                                @if(isset($job->abutmentDelivery))
                                    <div class="col-md-4">
                                    @foreach($job->abutmentDelivery as $delivery)
                                        <p style="margin-bottom: 2px;">{{$delivery->implant->name?? "None"  }} -
                                         {{  $delivery->abutment->name?? "None" }} -
                                         {{  $delivery->code?? "None"}} </p>

                                    @endforeach
                                    </div>
                                @endif
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

        <br>

        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h5 class="kt-portlet__head-title">
                    <i class="fa-solid fa-square-plus"></i>  New Jobs:
                </h5>
            </div>
        </div>
        <hr>
        <!-- NEW JOBS REPEATER -->
        <div id="" style="" class="repeater jobsRepeater">
            <div data-repeater-list="repeat2" class="jobDataRepeaterList" >
                <div data-repeater-item class="jobRow" >
                    <div class="form-group form-group ">
                        <div data-repeater-list="repeat2" class="col-12 padding5px">
                            <div data-repeater-item class="form-group row align-items-center row-item"
                                 style="border: 1px solid #ccc;border-radius: 16px;padding:5px">


                                <div class="col-md-2">
                                    <div class="kt-form__label">
                                        <label class="kt-label m-label--single bold">Units:</label>
                                    </div>
                                    <input type="hidden" name="units" id="units"
                                           class="hiddenUnitsInput" required>
                                    <button type="button" class="btn btn-secondary slctUnitsBtn"
                                            data-toggle="modal" data-target="#unitsDialog2"
                                            name="openDialogBtn"
                                            onclick="preOpenDialog2(this)">Select Units</button>

                                </div>
                                <div class="col-md-2">
                                    <div class="kt-form__group--inline">
                                        <div class="kt-form__label">
                                            <label class="kt-label m-label--single">Job type:</label>
                                        </div>
                                        <div class="kt-form__control">
                                            <select class="form-control" id="jobType" name="jobType"
                                                    onchange="jobTypeChanged2(this)">
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
                                                                id="implant" name="implant">
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

            @php
                $discountExists = $case->discount != null;
                @endphp

                <label style="cursor: pointer">
                    <input type="checkbox" class="discountCB" name="discountCB"
                           value="on"
                           {{$discountExists ? 'checked' : ''}}
                           onclick='toggleDiscountPortion(this)'
                   />
                    Make a Discount
                </label>

                <br>
                <div class="form-group form-group row discountPortion" style="{{$discountExists ? '' : 'display:none'}}" >
                    <div class="col-md-3 col-xs-6">
                        <input class="form-control" type="number" name="discount_amount"
                               placeholder="Amount ({{ $currencyLabel }})" value="{{$discountExists ? $case->discount->discount : ""}}"/>
                        <small>{{ $currencyLabel }}</small>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <input class="form-control" type="text" name="discount_reason"
                               value=" {{$discountExists ? $case->discount->reason : ""}}"
                               placeholder="Description"/>
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

            <div class="form-group form-group">
                <label >Notes:</label>

                @foreach($case->notes as $note)

                    <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black" disabled>

                        <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                    </div>
                @endforeach

                <form  style="" class="noteform " method="POST" enctype="multipart/form-data"   action="{{route('new-note')}}">
                    @csrf
                    <div class="row" style="padding:0px">
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
                            <i class="fa fa-photo" style="height:3%;color:inherit"></i> Attachments
                        </h5>
                    </div>
                </div>
                <hr>
                <!-- Photos SECTION -->
                <div class="container" style="margin-top:10px;">
                    <h6>Existing Attachments:</h6>
                    <div class="attachments-grid">
                        <div class="row">
                            @foreach($case->photos as $photo)
                                @php
                                    $extension = pathinfo($photo->path, PATHINFO_EXTENSION);
                                    $filename = basename($photo->path);
                                    $shortFilename = strlen($filename) > 10 ? substr($filename, 0, 10) . '...' : $filename;
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                @endphp
                                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" style="margin-bottom: 15px;">
                                    <div class="attachment-item" style="border: 1px solid #ddd; padding: 10px; text-align: center; background: #f8f9fa; position: relative;">
                                        @if($isImage)
                                            <div style="position: relative;">
                                                <img src="{{asset($photo->path)}}" style="max-height: 100px; width: 100%; object-fit: cover; border-radius: 4px;">
                                                <div style="position: absolute; top: 5px; right: 5px;">
                                                    <a href="{{asset($photo->path)}}" target="_blank" class="btn btn-sm btn-success" style="padding: 2px 6px; margin-right: 2px;">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-attachment" data-photo-id="{{$photo->id}}" style="padding: 2px 6px;">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <i class="fa fa-file" style="font-size: 40px; color: #6c757d;"></i>
                                            <div style="position: absolute; top: 5px; right: 5px;">
                                                <a href="{{asset($photo->path)}}" target="_blank" class="btn btn-sm btn-success" style="padding: 2px 6px; margin-right: 2px;">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm delete-attachment" data-photo-id="{{$photo->id}}" style="padding: 2px 6px;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                        <div style="margin-top: 5px; font-size: 12px;" title="{{$filename}}">
                                            {{$shortFilename}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <br>
                {{--<div class="form-group form-group-last">--}}
                {{--<label for="images">Add Photos:</label>--}}
                {{--<input required type="file" id="images" class="form-control" name="images[]" placeholder="address" multiple disabled>--}}
                {{--</div>--}}
{{--                <br>--}}
{{--                <div class="kt-portlet__foot">--}}
{{--                    <div class="kt-form__actions">--}}
{{--                        <button type="submit" class="btn btn-primary" disabled>Submit</button>--}}
{{--                        <button type="reset" class="btn btn-danger" disabled>Reset</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>

            <!-- Attachments SECTION -->

            <div class="form-group form-group-last">
                <label for="images" style="cursor: pointer;padding: 10px 18px 10px 18px;"><h4><i class="fa-solid fa-circle-plus"></i>
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
                    <button type="reset" class="btn btn-danger extraPadding">Reset</button>
                </div>
            </div>
        </div>

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
                <div class="modal-footer" name ="model-footer">

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

                        {{--<img class="jaw lowerJaw" alt="lower" src="/assets/teethPics/lower-jaw.png" width=180px--}}
                        {{--style="position: absolute; top: 330px;left: 150px;">--}}

                        <img class="jaw2 upperJaw" alt="upper"  src="/assets/teethPics/v2/upper_jaw.png" height=265px
                             style="position: absolute; top: 17px;left: 0px;">
                        <img class="jaw2 lowerJaw" alt="lower"  src="/assets/teethPics/v2/lower_jaw.png" height=280px
                             style="position: absolute; top: 295px;left: 17px;">

                        <img class="teeth2" alt="18" src="/assets/teethPics/v2/18.png" height={{$imageSizeM +8}}px
                             style="  position: absolute; top: 226px;left: 55px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth2" alt="17" src="/assets/teethPics/v2/17.png" height={{$imageSizeL}}px
                             style="  position: absolute; top:183px;left:59px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth2" alt="16" src="/assets/teethPics/v2/16.png" height={{$imageSizeL +3}}px
                             style="  position: absolute; top: 139px;left:67px;">
                        @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                        <img class="teeth2" alt="15" src="/assets/teethPics/v2/15.png" height={{$imageSizeM +1}}px
                             style="  position: absolute; top: 111px;left:79px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth2" alt="14" src="/assets/teethPics/v2/14.png" height={{$imageSizeM +2}}px
                             style="  position: absolute; top:82px;left:92px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth2" alt="13" src="/assets/teethPics/v2/13.png" height={{$imageSizeM +6}}px
                             style="  position: absolute; top:53px;left:110px;">
                        @php $teeth = 6; @endphp
                        <img class="teeth2" alt="12" src="/assets/teethPics/v2/12.png" height={{$imageSizeM +4}}px
                             style="  position: absolute; top: 36px;left: 135px;">
                        @php $teeth = 7; @endphp
                        <img class="teeth2" alt="11" src="/assets/teethPics/v2/11.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23.5px;left: 162px;">
                        @php $teeth = 8; @endphp
                        <img class="teeth2" alt="21" src="/assets/teethPics/v2/21.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top: 23px;left:200px;">
                        @php $teeth = 9; @endphp
                        <img class="teeth2" alt="22" src="/assets/teethPics/v2/22.png" height={{$imageSizeM +5}}px
                             style="  position: absolute; top:35px;left: 231px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth2" alt="23" src="/assets/teethPics/v2/23.png" height={{$imageSizeM +3}}px
                             style="  position: absolute; top: 55px;left: 254px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth2" alt="24" src="/assets/teethPics/v2/24.png" height={{$imageSizeM}}px
                             style="  position: absolute; top: 84px;left: 266px;">
                        @php $teeth = 3; @endphp
                        <img class="teeth2" alt="25" src="/assets/teethPics/v2/25.png" height={{$imageSizeM}}px
                             style="  position: absolute; top:112px;left:272px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth2" alt="26" src="/assets/teethPics/v2/26.png" height={{$imageSizeL +1 }}px
                             style="  position: absolute; top: 141px;left: 280px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth2" alt="27" src="/assets/teethPics/v2/27.png" height={{$imageSizeL }}px
                             style="  position: absolute; top:182px;left: 291px;">
                        @php $teeth = 0; @endphp
                        <img class="teeth2" alt="28" src="/assets/teethPics/v2/28.png" height={{$imageSizeL }}px
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

                            <img class="teeth2" alt="38" src="/assets/teethPics/v2/38.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top:326px;left: 309px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth2" alt="37" src="/assets/teethPics/v2/37.png" height={{$imageSizeL+6}}px
                                 style="  position: absolute; top:367px;left:299px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth2" alt="36" src="/assets/teethPics/v2/36.png" height={{$imageSizeL+5}}px
                                 style="  position: absolute; top:412px;left:285px;">
                            @php $teeth = 3; $decrement = $decrement-1.5; @endphp
                            <img class="teeth2" alt="35" src="/assets/teethPics/v2/35.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 454px;left:275px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth2" alt="34" src="/assets/teethPics/v2/34.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 484px;left:263px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth2" alt="33" src="/assets/teethPics/v2/33.png" height={{$imageSizeM+1}}px
                                 style="  position: absolute; top: 508px;left:247px;">
                            @php $teeth = 6; @endphp
                            <img class="teeth2" alt="32" src="/assets/teethPics/v2/32.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 527px;left: 229px;">
                            @php $teeth = 7; @endphp
                            <img class="teeth2" alt="31" src="/assets/teethPics/v2/31.png" height={{$imageSizeM-3}}px
                                 style="position: absolute; top:538px;left: 203px;">
                            @php $teeth = 8; @endphp
                            <img class="teeth2" alt="41" src="/assets/teethPics/v2/41.png" height={{$imageSizeM-2}}px
                                 style="position: absolute; top: 534px;left:176px;">
                            @php $teeth = 9; @endphp
                            <img class="teeth2" alt="42" src="/assets/teethPics/v2/42.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top:524px;left: 150px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth2" alt="43" src="/assets/teethPics/v2/43.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 510px;left: 127px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth2" alt="44" src="/assets/teethPics/v2/44.png" height={{$imageSizeM}}px
                                 style="  position: absolute; top: 485px;left: 108px;">
                            @php $teeth = 3; @endphp
                            <img class="teeth2" alt="45" src="/assets/teethPics/v2/45.png" height={{$imageSizeM+2}}px
                                 style="  position: absolute; top: 455px;left: 88px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth2" alt="46" src="/assets/teethPics/v2/46.png" height={{$imageSizeL+4.5 }}px
                                 style="  position: absolute; top: 415px;left: 68px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth2" alt="47" src="/assets/teethPics/v2/47.png" height={{$imageSizeL+5 }}px
                                 style="  position: absolute; top: 371px;left: 55px;">
                            @php $teeth = 0; @endphp
                            <img class="teeth2" alt="48" src="/assets/teethPics/v2/48.png" height={{$imageSizeL+1}}px
                                 style="  position: absolute; top: 331px;left:44px;">
                            @php $teeth = 16; @endphp


                        </div>
                    </div>

                    <script
                            src="https://code.jquery.com/jquery-3.6.0.min.js"
                            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
                            crossorigin="anonymous"></script>


                </div>
                <div class="modal-footer" name ="model-footer">

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
    <script>
        // File upload preview functionality for edit case
        $(document).ready(function() {
            // Handle new file uploads preview
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
                                            <button type="button" class="btn btn-danger btn-sm remove-new-file" data-index="${index}"
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
                                        <button type="button" class="btn btn-danger btn-sm remove-new-file" data-index="${index}"
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

            // Handle new file removal from preview
            $(document).on('click', '.remove-new-file', function() {
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

            // Handle existing attachment deletion
            $(document).on('click', '.delete-attachment', function() {
                const photoId = $(this).data('photo-id');
                const attachmentItem = $(this).closest('.col-xs-6, .col-sm-4, .col-md-3, .col-lg-3');

                if (confirm('Are you sure you want to delete this attachment?')) {
                    $.ajax({
                        url: '/attachment/delete/' + photoId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                attachmentItem.fadeOut(300, function() {
                                    $(this).remove();
                                });
                                alert('Attachment deleted successfully');
                            } else {
                                alert('Error deleting attachment: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error deleting attachment. Please try again.');
                        }
                    });
                }
            });
        });
    </script>



    <script>
        $(document).ready(function () {
            $('#lightgallery').lightGallery();
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
//            $(".jobsRepeater").find(".jobDataRepeaterList").first().html("");
//            $("#addJobBtn").click();
        });

        var teethSelected = [];
        var lstSelectedJobUNName = "";
        var repeaterName = ""; // should be something like 'repeat[xx]'
        var jobId =0;

        function jobTypeChanged(jobTypeDD,jobId){
            if(repeaterName == "")
            {
            setRepeaterNameByJobTypeDD(jobTypeDD,jobId);
            console.log("Setting repeater name");
            }
            if(jobId == 0){
                jobId =  $("[name='" + repeaterName + "[abutment"+jobId+"]']");
            }

            var jobTypes = {!! json_encode($types->toArray()) !!};
            var materials = {!! json_encode($materials->toArray()) !!};
            var materialJobTypeRelations = {!! json_encode($jobTypeMaterials->toArray()) !!};
            var abutmentBox =  $("[name='" + repeaterName + "[abutment"+jobId+"]']");
            var implantBox =  $("[name='" + repeaterName + "[implant"+jobId+"]']");
            var materialBox = $("[name='"+repeaterName  +"[material_id"+jobId+"]']");
            var jobTypeSelectedId = $(jobTypeDD).val();
            var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId);
            materialBox.empty();
            $.each(jobTypeMaterials, function(key, value) {
                materialBox.append($("<option></option>")
                    .attr("value", value.material_id)
                    .text( materials.find(x => x.id === value.material_id).name));
            });
            console.log("Exisiting job type changed " + abutmentBox.attr('name') + "Selector : " + "[name='" + repeaterName + "[abutment"+jobId+"]']");
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
                    jobTypeChanged(jobTypeBox);
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

            }


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

        function preOpenDialog(element,unitNum) {
            jobId = unitNum;
            var lengthOfJobId =  jobId.toString().length;
            var lengthOfBtnName = parseInt(element.name.length) + parseInt(lengthOfJobId);

            if (parseInt(element.name.length)  == 24 + parseInt(lengthOfJobId) ) {
                lstSelectedJobUNName = element.name.substr(0, 9) + "[units" +unitNum+ "]";
                repeaterName = element.name.substr(0, 9);
            }
            else {
                console.log("repeater reached 2 digits");
                repeaterName = element.name.substr(0, 10);
                lstSelectedJobUNName = element.name.substr(0, 10) + "[units" +unitNum+ "]";
                console.log(lengthOfJobId);
            }
            var selector = "[name='"+ lstSelectedJobUNName+"']";
            var currentJobUnits = $(selector);
            // console.log("Current job units box name :" + element.name.substr(0,9) +  "[units]");
            if(typeof currentJobUnits !==  "undefined" && currentJobUnits.val()) {
                teethSelected = currentJobUnits.val().split(',');
                // console.log("is defined and its now : " + teethSelected);
            }
            else {
                 console.log("didnt find previously selected units . err007, units box name selector= " + selector + " ,legnth of btn name : " + parseInt(element.name.length) + " ,length of job id string : " + parseInt(lengthOfJobId) + " || btn name : "+ element.name);
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

        function showAbutImpBoxes(){
            var jobsTypeBoxes = $("#jobType");
            // fill up the options with the array above.
            $.each(jobsTypeBoxes, function (key, value) {
                jobTypeChanged(value);
            });
        }
    </script>


    <script>
        var teethSelected2 = [];
        var lstSelectedJobUNName2 = "";
        var repeaterName2 = ""; // should be something like 'repeat[xx]'
        function addJobBtnPressed(){
//                var autoDetectStageCB = $(".autoStageCB:last").prop('checked', true);
//                // Where repeater name ends :
//                var lastIndex = autoDetectStageCB.attr('name').lastIndexOf('[auto');
//                // splice it and save it in our variable
//                repeaterName2 = autoDetectStageCB.attr('name').substr(0, lastIndex);
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
        function addAbutmentJob(ele){
            // get units selected originally in the job
            var teethSelectedAsArr = $("[name='" + lstSelectedJobUNName2 + "']").val().split(',');
            // wait for new repeater row to populate then add unit selected to abutment units box
            setTimeout(function(){
                var lastAbutmentUnitsBox = $("select[name$='[abutmentUnits][]']").last();


                $.each(teethSelectedAsArr  , function( index, value ) {
                    $(lastAbutmentUnitsBox).last().append($("<option></option>")
                        .attr("value", value)
                        .text(value));
                });
                lastAbutmentUnitsBox.selectpicker();


                if (!(lstSelectedJobUNName2.substring(8, 9) > 1))
                $(".deleteBtn2").eq(-2).click();
                }, 500);
            $

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
                ele.value="on";
                discountPortion.show(200);
            } else{
                ele.value="false";
                discountPortion.hide(200);
            }
        }
        function jobTypeChanged2(jobTypeDD){
            var thisRowRepeaterName = $(jobTypeDD).attr("name").replace('[jobType]','');
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
            var abutmentsArea =  $(jobTypeDD).parent().parent().parent().parent().parent().find(".abutmentsArea");
            var abutmentUnitsBox =  $(abutmentsArea).find(".abutmentsUnitsPicker");
            var currentlySelectedUnits = $(jobTypeDD).parent().parent().parent().parent().parent().find(".hiddenUnitsInput").val().split(',');
            var openDialogBtn = $("[name='" + repeaterName2 + "[openDialogBtn]']");
            console.log("New job type changed " +$(jobTypeDD).find(":selected").val() );
            materialChanged();

            if($(jobTypeDD).find(":selected").val() == 6 ){

                // get to parent of the main repeater and find abutment units box
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
                $(abutmentsArea).css("display","none");
                abutmentUnitsBox.val(0);
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
