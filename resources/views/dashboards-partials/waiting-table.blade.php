<table class=" waitingTable sunriseTable" style="width:100%">
    <thead>
        <tr>
            <td class="no-sort">
                @if ($key == "milling" || $key == "sintering" ||$key == "3dprinting" || $key == "pressing" || $key == "delivery")
                <input type="checkbox" class="selectAllCases" value="0" name="selectAllCases" onchange="selectAll(this)" />
                @endif
            </td>
            <th>Doctor</th>
            <th>Patient</th>
            <th class="deliveryDateHeader"><span class="innerSpan4Mobile">D.Date</span><span
                    class="innerSpan4DeskTop">Delivery Date</span></th>
            <th>#</th>
            @if ($key == "Delivery")
            <th> Assigned To</th>
            @endif
            <th>Tags</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stage['waitingCases'] as $case)

        <tr style="color:{{$color}}">
            @if ($key == "Finishing")
            @php
            $notReadyA=false;
            $abutmentsReceived = $case->abutmentsReceived();
            if(!$case->allUnitsAtFinishing())
            $notReadyA=true;
            @endphp
            @endif
            <td>
                @if ($key == "milling" || $key == "3dprinting" || $key == "sintering" || $key == "pressing" || $key == "delivery" )
                <input type="checkbox" class="custom-control-input multipleCB  " value="{{$case->id}}" name="casesCheckBoxes[]"   data-group-id="{{$type}}" onchange="multiCBChanged()" />
                @endif
            </td>
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">
                <p class="">{{$case->client->name}}</p>
            </td>
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">
                <p class="">{{$case->patient_name}} @if ($key == "Finishing")
                    @if($notReadyA) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                        Not <br>
                        Ready
                    </span> @endif
                    @if(!$abutmentsReceived) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                        Abutment <br>
                        Missing
                    </span> @endif
                    @endif
                </p>
            </td>
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">
                <p class="">X</p>
            </td>
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">
                <p class="">{{$case->unitsAmount($stage['numericStage'])}}</p>
            </td>
            <!-- Assigned to for delivery stage -->
            @if ($key == "delivery")
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">
                <p class="">{{$case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo ?
                             $case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo->name_initials : "None"}}</p>
            </td>
            @endif
            <td class="clickable" data-toggle="modal"
                data-target="#waitingDialog{{$key. $case->id}}">

                @foreach($case->tags as $tag)
                <i title="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->text : ""}}"
                    style="color:{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->color : '#000'}}"
                    class="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->icon : ''}}  fa-lg"></i>
                @endforeach
            </td>
        </tr>
        @if($key == "delivery")
        <div class="modal fade" tabindex="-1" role="dialog"
            id="myModal{{$case->id}}">
            <form action="{{route('assign-to-delivery-person')}}"
                method="POST">
                @csrf
                <input type="hidden" name="case_id"
                    value="{{$case->id}}">
                <div class="modal-dialog modal-dialog-centered"
                    role="document" style="width: 30%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Assign case to driver</h5>
                            <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div>


                                <div class="kt-form__control" style="    display: flex;
                                                                        flex-direction: column;
                                                                        align-items: center;">
                                    <label style="margin-bottom:10px !important">Delivery
                                        Driver:</label>
                                    <nav class="driversContainer">
                                        @foreach($drivers as $driver)
                                        <br />
                                        {{--<a class="driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}"><button class="btn btn-info driverNameBtn">--}}
                                            {{--{{$driver->name_initials}}--}}
                                            {{--</button></a>--}}
                                            <a class="btn btn-info driverNameBtn driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}">
                                                {{$driver->name_initials}}
                                            </a>
                                            @endforeach
                                    </nav>
                                </div>


                            </div>

                        </div>
                        <div class="modal-footer fullBtnsWidth" style="padding: 0px 10px 3px 10px !important">
                            {{--<button type="submit"--}}
                            {{--class="btn btn-primary">--}}
                            {{--Assign--}}
                            {{--</button>--}}
                            <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif


        {{--BEGIN WAITING DIALOG --}}
        <div class="modal fade" tabindex="-1" role="dialog" id="waitingDialog{{$key.$case->id}}">
            <form action="{{$key=="Delivery" ? route('delivery-accept', $case->id) : route('assign-to-me',['caseId'=> $case->id,'stage'=>$stage["numericStage"]] )}}"
                method="GET">
                @csrf
                <input type="hidden" name="case_id" value="{{$case->id}}">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Case Completion</h5>
                            @if(Auth()->user()->is_admin )
                            <div class="tooltipY">
                                <a href="{{route('finish-case-completely',['caseId' => $case->id])}}">
                                    <i class="fa-solid fa-forward-fast close "></i>
                                </a>
                                <span class="tooltiptextY">Send To Delivery</span>
                            </div>
                            @endif
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>

                        </div>
                        <div class="modal-body">

                            <div class="form-group row" style="margin-bottom: 0px">
                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                    <label for="doctor">Doctor: </label>
                                    <h5 id="doctor"><b>{{$case->client->name}}</b></h5>
                                </div>
                                <div class="form-group col-6 " style="margin-bottom: 0px">
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
                                <div class="col-md-3 col-sm-12 padding5px">
                                    <a href="{{route('view-case', ['id' => $case->id, 'stage' => $stage["numericStage"]  ])}}">
                                        <button type="button" class="btn btn-info ">View
                                        </button>
                                    </a>
                                </div>
                                @if ($key == "milling")
                                    <div class="col-md-6 col-sm-12 padding5px">
                                        <a >
                                            <button type="button" data-toggle="modal"  class="btn btn-success" data-dismiss="modal"  onclick="openModal('MillingDialog')"><i class="fa-solid fa-hexagon-nodes"></i> Nest </button>
                                        </a>
                                    </div>

                                @else
                                <div class="col-md-6 col-sm-12 padding5px">
                                    <button type="submit" class="btn btn-success"
                                        style="width:100%">{{$key == "delivery" ? 'Take' : 'Assign To Me'}}</button>
                                </div>
                                @endif
                                <div class="col-md-3 col-sm-12 padding5px"><a
                                        href="{{route('edit-case-view',$case->id)}}">
                                        <button type="button"
                                            class="btn btn-warning " {{$canEditCase ? '' : 'disabled'}}>
                                            Edit Case
                                        </button>
                                    </a></div>
                                @if ($key == "qc")
                                    <div class="col-12 padding5px">
                                        <a href="{{route('assign-and-finish',['caseId'=> $case->id,'stage'=>$stage["numericStage"]])}}">
                                            <button type="button" class="btn btn-info "><i class="fa-solid fa-arrow-trend-up"></i>Nest </button>
                                        </a>
                                    </div>
                                @endif


                                @if ($key == "delivery")
                                @if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 129))))
                                @if($case->jobs[0]->assignee == null)
                                <div class="col-12 padding5px">
                                    <a data-toggle="modal"
                                        data-target="#myModal{{$case->id}} ">
                                        <button type="button" data-dismiss="modal" class="btn btn-warning"> Assign to.. </button>
                                    </a>
                                </div>
                                @else
                                <div class="col-12 padding5px">
                                    <a data-toggle="modal"
                                        data-target="#myModal{{$case->id}}">
                                        <button type="button" data-dismiss="modal" class="btn btn-warning">Re-Assign.. </button>
                                    </a>
                                </div>
                                @endif
                                @endif
                                @endif
                                @if ($key == "delivery")
                                <div class="col-12 padding5px">
                                    <a href="{{route('view-voucher',$case->id)}}">
                                        <button type="button" class="btn btn-info "><i
                                                class="fas fa-print"></i> Print Voucher </button>
                                    </a>
                                </div>
                                @endif
                                <div class="col-12 padding5px">
                                    <button type="button" class="btn btn-secondary "
                                        data-dismiss="modal" style="width:100%">Cancel
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>

        @endforeach
        <!-- Begin Active tab -->


    </tbody>
</table>
