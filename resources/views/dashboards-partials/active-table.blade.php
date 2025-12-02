<table class=" activeTable sunriseTable" style="width:100%;">
    <thead>
        <tr>
            <th>Doctor</th>
            <th>Patient</th>
            <th class="deliveryToHeader">Delivery Date</th>
            <th class="assignedToHeader">Assigned To</th>
            <th class="">#</th>
            <th class="">Tags</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stage['activeCases'] as $case)
        <tr class="clickable" style="color:{{$color}}" data-toggle="modal"
            data-target="#confirmCompletion{{$key.$case->id}}">
            @if ($key == "finishing")
            @php
            $notReadyA=false;
            $abutmentsReceived = $case->abutmentsReceived();
            if(!$case->allUnitsAtFinishing())
            $notReadyA=true;
            @endphp
            @endif
            <td>
                <p class="">{{$case->client->name}}</p>
            </td>
            <td>
                <p class="">{{$case->patient_name}} @if ($key == "finishing")
                    @if($notReadyA) <span
                        style="float:right;margin-left: 5px; line-height: 1;color:#ffa400;font-size: 9px;">
                        Not <br>
                        Ready
                    </span> @endif

                    @if(!$abutmentsReceived) <span
                        style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">
                        Abutment <br>
                        Missing
                    </span> @endif
                    @endif

                </p>
            </td>
            <td class="">
                <p class="">x</p>
            </td>
            <td>
                <p class="">{{$case->jobs->where('stage',$stage["numericStage"])->first() ? ($case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo? $case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo->name_initials : "None") : "None"}}</p>
            </td>
            <td class="">
                <p class="">{{$case->unitsAmount($stage["numericStage"])}}</p>
            </td>
            <td class="">

                @foreach($case->tags as $tag)
                <i title="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->text : ""}}"
                    style="color:{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->color : '#000'}}"
                    class="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->icon : ''}}  fa-lg"></i>
                @endforeach
            </td>
        </tr>
        <!-- End Active tab -->

        <!-- External Milling Dialog -->
        @if ($key == "milling")
        <div class="modal fade" tabindex="-1" role="dialog"
            id="MEX{{$case->id}}">
            <form action="{{route('externally-milled')}}"
                method="POST">
                @csrf
                <input type="hidden" name="case_id"
                    value="{{$case->id}}">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Case milling
                                information</h5>
                            <button type="button" class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="form-group col-6 lab_id">
                                    <label for="lab_id">Lab
                                        name: </label>
                                    <select class="form-control"
                                        id="lab_id"
                                        name="lab_id">
                                        <option selected>Select
                                            your lab
                                        </option>
                                        @foreach($labs as $lab)
                                        <option value="{{$lab->id}}">{{$lab->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer fullBtnsWidth">
                            <button type="submit"
                                class="btn btn-primary">Save
                                changes
                            </button>
                            <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
        <!-- Active case actions Dialog -->
        <div class="modal fade" tabindex="-1" role="dialog"
            id="confirmCompletion{{$key.$case->id}}">
            <form action="{{$key == "delivery" ? route('delivery-accept', $case->id) : route('finish-case',['caseId'=> $case->id,'stage'=>$stage["numericStage"]] )}}"
                method="GET">
                @csrf
                <input type="hidden" name="case_id" value="{{$case->id}}">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Case Completion</h5>

                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group row" style="margin-bottom: 0px">
                                <div class="form-group col-6 "
                                    style="margin-bottom: 0px">
                                    <label for="doctor">Doctor: </label>
                                    <h5 id="doctor"><b>{{$case->client->name}}</b></h5>
                                </div>
                                <div class="form-group col-6 "
                                    style="margin-bottom: 0px">
                                    <label for="pat">Patient: </label>
                                    <h5 id="pat"><b>{{$case->patient_name}}</b></h5>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class=" col-12 ">
                                    <label><b>Jobs:</b></label><br>


                                    @foreach( $case->jobs->where('stage',$stage["numericStage"]) as $job)

                                    @php
                                    $unit = explode(', ',$job->unit_num);
                                    @endphp

                                    <span>{{$job->unit_num}}
                                        - {{$job->jobType->name ?? "No Job Type"}}
                                        - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}
                                        {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}
                                        <br>
                                        {{isset($job->abutmentR) && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>
                                    @endforeach
                                </div>
                            </div>
                            @if(count($case->notes)>0)
                            <hr>
                            <label><b>Notes:</b></label><br>
                            @foreach($case->notes as $note)
                            <div class="form-control"
                                style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px"
                                disabled>

                                <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>
                                <span class="noteText">{{$note->note}}</span>
                            </div>
                            @endforeach
                            @endif

                        </div>
                        <div class="modal-footer fullBtnsWidth">
                            <div class="row btnsRow"
                                style=" margin-right: 0px; margin-left: 0px;width:100%">
                                @if($key == "delivery")
                                <div class="col-12 padding5px">

                                    <a class="dropdown-item" href="{{route('delivered-in-box',$case->id)}}">
                                        <button type="button" class="btn btn-outline-info" style="width:100%">Delivered In Box</button></a>
                                </div>
                                @endif
                                <div class="col-3 padding5px">
                                    <a href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage["numericStage"]])}}">
                                        <button type="button" class="btn btn-info ">
                                            View
                                        </button>
                                    </a>
                                </div>

                                <div class="col-6 padding5px">
                                    @php
                                    $isAdmin = Auth()->user()->is_admin;
                                    $canBeFinished= true;
                                    $isUserCase = false;
                                    $canComplete = false;
                                    if($case->jobs->where('stage',$stage["numericStage"])->first() && $case->jobs->where('stage',$stage["numericStage"])->first()->assignee == Auth()->user()->id)
                                    {$canComplete = true;
                                    $isUserCase= true; }
                                    if($key == "finishing")
                                    if ($notReadyA || !$abutmentsReceived){
                                    $canComplete= false;
                                    $canBeFinished = false;
                                    }
                                    @endphp
                                    @if ($isAdmin && $canBeFinished && !$isUserCase)

                                    <a class=""
                                        href="{{route('complete-by-admin', ['id'=>$case->id,'stage'=>$stage["numericStage"]] )}}">
                                        <button type="button" class="btn btn-success">Override Complete</button>
                                    </a>


                                    @else
                                    <button type="submit" class="btn btn-success"
                                        style="width:100%" {{$canComplete ? '' : 'disabled'}}>{{$canComplete ? 'Complete' : 'Case cannot be completed'}}</button>
                                    @endif
                                </div>
                                <div class="col-3 padding5px"><a
                                        href="{{route('edit-case-view',$case->id)}}">
                                        <button type="button"
                                            class="btn btn-warning " {{$canEditCase ? '' : 'disabled'}}>
                                            Edit Case
                                        </button>
                                    </a></div>

                                @if ($key == "milling")
                                <div class="col-12 padding5px">
                                    <button type="button" class="btn btn-dark "
                                        data-toggle="modal"
                                        data-target="#MEX{{$case->id}}"
                                        data-dismiss="modal" style="width:100%">
                                        Externally Milled
                                    </button>
                                </div>
                            </div>
                            @endif
                            @if ($key == "delivery")

                            <div class="col-12 padding5px">

                                <a class="dropdown-item" href="{{route('view-voucher',$case->id)}}"> <button type="button" class="btn btn-outline-info">Print voucher</button></a>
                            </div>
                            @if($case->delivered_to_client == 1)
                            @if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 9)))
                            <div class="col-12 padding5px">
                                <a class="dropdown-item"
                                    href="{{route('receive-voucher', $case->id )}}">
                                    <button type="button" class="btn btn-outline-secondary">Receive Voucher</button>
                                </a>

                            </div>
                            @endif
                            @endif
                            @endif

                            <div class="col-12 padding5px">
                                <a class=""
                                    href="{{route('reset-to-waiting', ['id'=>$case->id,'stage'=>$stage["numericStage"]] )}}">
                                    <button type="button" class="btn btn-outline-danger">Reset To Waiting</button>
                                </a>
                            </div>
                            <div class="col-12 padding5px">
                                <button type="button" class="btn btn-secondary "
                                    data-dismiss="modal" style="width:100%">Cancel
                                </button>
                            </div>
                        </div>


                    </div>
                </div>
            </form>

            /////////// v2 DIALOG

            @endforeach
    </tbody>
</table>
