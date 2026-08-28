@extends('layouts.app' ,[ 'pageSlug' => $viewCase])

@section('content')
    <link rel="stylesheet" href="{{asset('assets/css/lightgallery.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/jquery.imagesloader.css')}}" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/lightgallery/1.3.9/css/lightgallery.min.css" rel="stylesheet">

    <style>
        #kt_repeater_1 {padding-left:15px}
        .noteform{padding:10px}
        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }
        .hidden{
            display:none;
        }
        .noteHeader{color: #525252; font-size: 12px;}
        .noteText{color:black;font-weight: 500;}
        .bootstrap-select>.dropdown-toggle.bs-placeholder{
            color: #1a000d !important;

        }
        .historyTable{display:none;}
        .Timeline{display:block;}
        @media screen and (max-width:760px) {
            .btnsRow{
                flex-direction: column;
            }
            .historyTable{display:block;}
            .Timeline{display:none !important;}
            .noteform{padding:0}
            #kt_repeater_1 {padding-left:0}
            .printMiniLabelBtn{margin-bottom: 5px;}
        }

    </style>
    <link href="{{ asset('assets') }}/css/timeline.css" rel="stylesheet"/>

    @php
        $jobs = ($stage == -2 || $stage > 5) ? $case->jobs : $case->jobs->where('stage', $stage);
        $jobs = $jobs->values();
        $deliveryDateTime = $case->initial_delivery_date
            ? \Carbon\Carbon::parse(str_replace('T', ' ', $case->initial_delivery_date))
            : null;
        $printLabelData = [
            'caseId' => $case->case_id,
            'doctor' => $case->client->name,
            'patient' => $case->patient_name,
            'deliveryDate' => $deliveryDateTime ? $deliveryDateTime->format('d-M') : '',
            'deliveryTime' => $deliveryDateTime ? $deliveryDateTime->format('g:i a') : '',
            'jobs' => $jobs->map(function ($job) {
                $units = array_filter(array_map('trim', explode(',', $job->unit_num ?? '')));
                return [
                    'jobType' => $job->jobType->name ?? '',
                    'material' => $job->material->name ?? '',
                    'color' => $job->color ? trim($job->color) : '-',
                    'quantity' => count($units) ?: 0,
                ];
            })->values(),
        ];
    @endphp


    <div class="col-lg-12 col-sm-12 ">
        <div class="row btnsRow" style="padding-left: 10px;padding-top: 10px; background-color: transparent">
            <div class=" col-3 ">

                    <button class="btn btn-secondary printMiniLabelBtn" style=" background-color: #2b7b7d;   padding-left: 20px;
    padding-right: 20px;" onclick="PrintMinimizedLabel()" >Print Mini Label <i class="fa-solid fa-tag"></i></button>

            </div>
            <div class="col-2 ">

                    <button class="btn btn-secondary" style="background-color: #2b7b7d;    padding-left: 20px;
    padding-right: 20px;" onclick="PrintLabel()" >Print Label <i class="fa-solid fa-tag"></i></button>

            </div>
        </div>
        </div>

    <form style="" class="kt-form noteform" method="POST" enctype="multipart/form-data" action="#">
    @csrf
    <div>
    <!-- CASE INFO -->

        <div class="row" style="padding-left: 10px;padding-top: 10px">
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
                <div class="col-md-12 col-xs-12">
                    <input class="form-control SDTP" name="delivery_date"  type="text"   value="{{$case->initial_delivery_date}}" required disabled/>
                </div>
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
                            <option value="{{$impression->id}}" {{ $impression->id == $case->impression_type ? ' selected' : ' ' }}>
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

        <div  id="kt_repeater_1" style=" padding-right: 15px">
            <div  data-repeater-list="repeat">
                <div data-repeater-item>
                    <div class="form-group form-group ">
                        <div data-repeater-list="repeat" class="col-12">
                                <table id="tech-companies-1" class="table sunriseTable table-striped jobsTable">
                                    <thead>
                                    <tr>
                                        <th id="tech-companies-1-col-0">Unit Num</th>

                                        <th data-priority="3" id="tech-companies-1-col-2">Job Type</th>
                                        <th data-priority="1" id="tech-companies-1-col-3">Material</th>
                                        <th data-priority="3" id="tech-companies-1-col-4">Color</th>
                                        <th data-priority="3" id="tech-companies-1-col-5">Style</th>
                                        <th data-priority="3" id="tech-companies-1-col-5">Status</th>
                                        <th data-priority="6" id="tech-companies-1-col-6">Others</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($jobs as $job)

                                        @php
                                            $unit = explode(', ',$job->unit_num);
                                        @endphp
                                    <tr >

                                        <th colspan="1" data-columns="tech-companies-1-col-0">{{$job->unit_num}}</th>

                                        <td data-priority="3" colspan="1" data-columns="tech-companies-1-col-2">{{$job->jobType->name}}</td>
                                        <td data-priority="1" colspan="1" data-columns="tech-companies-1-col-3">{{$job->material->name}}</td>
                                        <td data-priority="3" colspan="1" data-columns="tech-companies-1-col-4">{{$job->color =='0' ? "No color":$job->color}}</td>
                                        <td data-priority="3" colspan="1" data-columns="tech-companies-1-col-5">{{$job->style }}</td>
                                        <td data-priority="3" colspan="1" data-columns="tech-companies-1-col-5">
                                            <b style="color:#2b7b7d">{{$job->status()}}</b>
                                        </td>
                                        <td data-priority="6" colspan="1" data-columns="tech-companies-1-col-6">
                                            @if(isset($job->abutmentDelivery))
                                                @foreach($job->abutmentDelivery as $delivery)
                                                    <span>{{$delivery->implant->name?? "None" }}{{' - ' . $delivery->abutment->name?? "None" }}{{ ' - ' . $delivery->code?? "None"}} </span>
                                                    <br>

                                                @endforeach
                                            @else
                                                <span>"Err" </span>
                                            @endif

                                            @if(isset($job->originalJob))
                                            @if(isset($job->originalJob->abutmentDelivery))
                                                @foreach($job->originalJob->abutmentDelivery as $delivery)
                                                    <span>{{$delivery->implant->name?? "None" }}{{' - ' . $delivery->abutment->name?? "None" }}{{ ' - ' . $delivery->code?? "None"}} </span>
                                                    <br>

                                                @endforeach
                                            @else
                                                <span>"Err" </span>
                                            @endif
                                            @endif

                                           @if(isset($job->abutmentR)  && $job->jobType->id ==6)
                                                <span>   Abutment Type:  {{$job->abutmentR->name}} <br></span>
                                            @endif
                                        @if($job->has_been_rejected) <span style="color:red;font-size: 10px"><b>PARTIALLY/ FULLY</b></span> <span style="color:red"><b>REJECTED</b></span> @endif
                                                @if($job->is_repeat)  <span style="color:red"><b>REPEAT</b></span> @endif
                                                @if($job->is_modification)<span style="color:red"><b>MODIFICATION</b></span>@endif
                                                @if($job->is_redo)<span style="color:red"><b>REDO</b></span>@endif
                                        @if(isset($job->redone_job_id))<span style="color:red"><b>HAS A REDO JOB BELOW</b></span>@endif
                                        </td>
                                        @if($job->is_rejection) <td class="reOverlay">REJECTION</td> @endif
                                        @if(isset($job->modified_job_id)) <td class="reOverlay">COMPLETED & UNDER MODIFICATION BELOW</td> @endif

                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>

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
                    <i class="fa-solid fa-clock-rotate-left"  ></i> Case History
                </h5>
            </div>
        </div>
        <hr>
        <!-- HISTORY -->
        <div class="historyTable" style="padding:0 30px 0 30px ">
        <table class="sunriseTable table sunriseTable table-striped " >
            <thead>
            <tr>
                <th>Stage</th>
                <th>Employee</th>
                <th>Started On</th>
                <th>Finished On</th>
            </tr>
            </thead>
            <tbody>
            @php
                $stages = array('Design','Milling','3D Printing','Sintering','Pressing','Finishing','QC'    );
            $i=1;
            @endphp
            @foreach ($stages as $key => $value)
                <tr>
                    <td class="stageName">{{$value}}</td>
                    @php $log = $case->logs->where('stage',$i)->where("is_completion",1)->first(); @endphp
                    @if($case->logs->where('stage',$i)->where('stage',$i)->first() !== null)
                    <td>{{$case->logs->where('stage',$i)->where("is_completion",1)->first() ? $case->logs->where('stage',$i)->where("is_completion",1)->first()->user->fullName() : " - "}}</td>
                    <td>{{$case->logs->where('stage',$i)->where("is_completion",0)->first() ? substr($case->logs->where('stage',$i)->where("is_completion",0)->first()->created_at,0,16) : " - "}}</td>
                    <td>{{$case->logs->where('stage',$i)->where("is_completion",1)->first() ? substr($case->logs->where('stage',$i)->where("is_completion",1)->first()->created_at,0,16) : " - "}}</td>
                    @else
                    <td>-</td><td>-</td><td>-</td>
                    @endif
                </tr>
                @php $i = $i+1; @endphp
            @endforeach
            <tr>
                <td class="stageName">Delivery</td>
                @php $log = $case->logs->where('stage',8)->first(); @endphp
                @if($case->logs->where('stage',8)->where("is_completion",1)->first() !== null)
                    <td>{{$case->logs->where('stage',$i)->where("is_completion",1)->first()->user->fullName()}}</td>
                    <td>{{$case->logs->where('stage',8)->where("is_completion",0)->first() ? substr($case->logs->where('stage',8)->where("is_completion",0)->first()->created_at,0,16) :  substr($case->logs->where('stage',8)->where("is_completion",3)->first()->created_at,0,16)}}</td>
                    <td>{{substr($case->logs->where('stage',8)->where("is_completion",1)->first()->created_at,0,16)}}</td>
                @else
                    <td>-</td><td>-</td><td>-</td>
                @endif
            </tr>


            </tbody>
        </table>
        </div>


        <div class="Timeline">

            <svg height="5" width="10">
                <line x1="0" y1="0" x2="10" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>

            <div class="event1">
                <div class="event1Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            DESIGN
                            <div class="MonthYear">{{$case->logs->where('stage',1)->where("is_completion",1)->first() ? substr($case->logs->where('stage',1)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',1)->where("is_completion",1)->first() ? $case->logs->where('stage',1)->first()->user->name_initials : "-"}}</div>
                </div>


                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>
                {{--<div class="time">9 : 27 AM</div>--}}

            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event2">

                <div class="event2Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            MILLING
                            <div class="MonthYear">{{$case->logs->where('stage',2)->where("is_completion",1)->first() ? substr($case->logs->where('stage',2)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',2)->where("is_completion",1)->first() ? $case->logs->where('stage',2)->first()->user->name_initials : "-"}}</div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>

            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event1">
                <div class="event1Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            3D Printing
                            <div class="MonthYear">{{$case->logs->where('stage',3)->where("is_completion",1)->first() ? substr($case->logs->where('stage',3)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',3)->where("is_completion",1)->first() ? $case->logs->where('stage',3)->first()->user->name_initials : "-"}}</div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>


            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event2">

                <div class="event2Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            Sintering
                            <div class="MonthYear">{{$case->logs->where('stage',4)->where("is_completion",1)->first() ? substr($case->logs->where('stage',4)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',4)->where("is_completion",1)->first() ? $case->logs->where('stage',4)->first()->user->name_initials : "-"}}</div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>

            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event1">
                <div class="event1Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            Pressing
                            <div class="MonthYear">{{$case->logs->where('stage',5)->where("is_completion",1)->first() ? substr($case->logs->where('stage',5)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',5)->where("is_completion",1)->first() ? $case->logs->where('stage',5)->first()->user->name_initials : "-"}}</div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>


            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event2">

                <div class="event2Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            Finishing
                            <div class="MonthYear">{{$case->logs->where('stage',6)->where("is_completion",1)->first() ? substr($case->logs->where('stage',6)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">
                        {{$case->logs->where('stage',6)->where("is_completion",1)->first() ? $case->logs->where('stage',6)->first()->user->name_initials : "-"}}</div>
                  </div>


                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>

            </div>

            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event1">
                <div class="event1Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            QC
                            <div class="MonthYear">{{$case->logs->where('stage',7)->where("is_completion",1)->first() ? substr($case->logs->where('stage',7)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>
                    <div class="eventTitle">{{$case->logs->where('stage',7)->where("is_completion",1)->first() ? $case->logs->where('stage',7)->first()->user->name_initials : "-"}}</div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>

            </div>
            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="event2">

                <div class="event2Bubble">
                    <div class="eventTime">

                        <div class="Day">
                            Delivery
                            <div class="MonthYear">{{$case->logs->where('stage',8)->where("is_completion",1)->first() ? substr($case->logs->where('stage',8)->where("is_completion",1)->first()->created_at,0,16) : "-"}}</div>
                        </div>
                    </div>

                    <div class="eventTitle">
                        @if ( $case->logs->where('stage',8)->where("is_completion",1)->first() !== null)
                        {{ $case->logs->where('stage',8)->where("is_completion",1)->first()->user->name_initials }}
                        @elseif ($case->logs->where('stage',8)->where("is_completion",3)->first() !== null)
                        {{ $case->logs->where('stage',8)->where("is_completion",3)->first()->user->name_initials }}
                        @else
                        -
                        @endif
                    </div>
                    <div class="eventTitle">
                        @if ( $case->logs->where('stage',8)->where("is_completion",1)->first() !== null)
                            {{ $case->logs->where('stage',8)->where("is_completion",1)->first()->user->name_initials }}
                        @elseif ($case->logs->where('stage',8)->where("is_completion",3)->first() !== null)
                            {{ $case->logs->where('stage',8)->where("is_completion",3)->first()->user->name_initials }}
                        @else
                            -
                        @endif
                    </div>
                    <div class="eventTitle">
                        @if ( $case->logs->where('stage',8)->where("is_completion",1)->first() !== null)
                            {{ $case->logs->where('stage',8)->where("is_completion",1)->first()->user->name_initials }}
                        @elseif ($case->logs->where('stage',8)->where("is_completion",3)->first() !== null)
                            {{ $case->logs->where('stage',8)->where("is_completion",3)->first()->user->name_initials }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="#004165" />
                </svg>

            </div>
            <svg height="5" width="100">
                <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>

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
                        <i class="fa fa-sticky-note" style="width:2%"></i>  Attachments:
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
        {{--<div class="form-group form-group-last">--}}
            {{--<label for="images">Add Photos:</label>--}}
            {{--<input required type="file" id="images" class="form-control" name="images[]" placeholder="address" multiple disabled>--}}
        {{--</div>--}}
        <br>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <button type="submit" class="btn btn-primary" disabled>Submit</button>
                <button type="reset" class="btn btn-danger" disabled>Reset</button>
            </div>
        </div>
        </div></div>
    </form>





@endsection
@push('js')
    <script>
        $(document).ready(function () {
            $('#lightgallery').lightGallery();
        });
        window.casePrintData = @json($printLabelData);
    </script>
    @include('cases.partials.case-printing')
@endpush
