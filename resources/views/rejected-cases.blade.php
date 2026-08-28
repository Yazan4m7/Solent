@extends('layouts.app' ,[ 'pageSlug' => "Cases List"])
@section('content')

    <style>
        .tooltiptext {
            display: none;
        }
    </style>
    @if(!isset($isSearchResults))
    @if(!isset($trashedCases))
        @if(isset($clients))
            <form class="kt-form" method="GET" action="{{route('rejected-cases')}}">
                @else
                    <form class="kt-form" method="GET" action="{{route('dentist-cases',['id' =>$id])}}">
                        <input type="hidden" class="form-control" name="id" value="{{$id}}">
                        @endif
                        <div class="container full-width">
                            <div class="row " style="padding-bottom:0">
                                <div class="col-12 col-sm-6 col-md-3 mb-3">
                                    <div class="kt-subheader__search" style="">
                                        <label>From (Start of):</label>
                                        <input type="date" class="form-control" name="from" value="{{$from}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 mb-3">
                                    <div class="kt-subheader__search" style="">
                                        <label>To (End of):</label>
                                        <input type="date" class="form-control" name="to" value="{{$to}}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 mb-3">

                                    @if(isset($clients))
                                        <div class="dropdown" style="text-align: left;">
                                            <label>Doctor:</label>
                                            <br>

                                            <select style="width:100%" class="selectpicker clearOnAll" multiple
                                                    name="doctor[]" id="doctor"
                                                    data-live-search="true">

                                                    <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>
                                                        All
                                                    </option>
                                                    @foreach($clients as $d)
                                                        <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                                                    @endforeach

                                            </select>

                                        </div>
                                    @endif

                                </div>
                                <div class="col-12 col-sm-6 col-md-2 mb-3">

                                    @if(isset($clients))
                                        <div class="kt-subheader__search">
                                            <label>Patient Name:</label>
                                            <br>
                                            <input type="text" name="patient_name" value="{{$patientName ?? ''}}"
                                                   class="form-control">
                                        </div>
                                    @endif

                                </div>

                            </div>
                        </div>
                            <div class="container full-width">
                                <div class="row justify-content-between">
                                    <div class="col-6 col-sm-6 col-md-3  mb-3">
                                        <button type="submit" class="btn btn-primary ">Submit</button>
                                    </div>

                                    <div  class="col-6 col-sm-6 col-md-3  mb-3">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                Show / Hide
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding-left:10px">
                                                <a class="toggle-vis dropdown-item" data-column="0" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"/>
                                                    ID</a>
                                                <a class="toggle-vis dropdown-item" data-column="1" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"/> Case
                                                    ID</a>
                                                <a class="toggle-vis dropdown-item" data-column="2" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Doctor</a>
                                                <a class="toggle-vis dropdown-item" data-column="3" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Patient name</a>
                                                <a class="toggle-vis dropdown-item" data-column="4" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Initial Deli.
                                                    Date</a>
                                                <a class="toggle-vis dropdown-item" data-column="5" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Date Delivered</a>
                                                <a class="toggle-vis dropdown-item" data-column="6" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Status</a>
                                                <a class="toggle-vis dropdown-item" data-column="7" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Tags</a>
                                                <a class="toggle-vis dropdown-item" data-column="8" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"/> Date
                                                    Created </a>
                                                <a class="toggle-vis dropdown-item" data-column="8" href="#"
                                                   onclick="toggleCheckBox(this)"><input type="checkbox"
                                                                                         class="form-check-input"
                                                                                         checked/> Actions</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                    </form>
                @endif
                    @endif
                            <div class="container full-width">
                                <div class="row">
                                    <div class="col-12">
                                        <br>
                                        <table id="datatable"
                                               class="table-striped table-bordered compact sunriseTable"
                                               role="grid" aria-describedby="datatable_info"
                                               style="width:100%">
                                            <thead>
                                            <tr role="row">
                                                <th class="sorting_asc hiddenByDefault" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-sort="ascending"
                                                    aria-label="Name: activate to sort column descending"
                                                    style="">ID
                                                </th>
                                                <th class="sorting hiddenByDefault" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Position: activate to sort column ascending"
                                                    style="">Case ID
                                                </th>
                                                <th class="sorting hiddenByDefault" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Office: activate to sort column ascending"
                                                    style="">Doctor
                                                </th>
                                                <th class="sorting" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Age: activate to sort column ascending"
                                                    style="">Patient name
                                                </th>
                                                <th class="sorting" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Start date: activate to sort column ascending"
                                                    style="">Initial Deli. Date
                                                </th>
                                                <th class="sorting" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Salary: activate to sort column ascending"
                                                    style="">Date Delivered
                                                </th>
                                                <th class="sorting statusCol" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Start date: activate to sort column ascending"
                                                    style="">Status
                                                </th>
                                                <th class="sorting" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Start date: activate to sort column ascending"
                                                    style="">Tags
                                                </th>
                                                <th class="sorting" tabindex="0"
                                                    aria-controls="datatable" rowspan="1" colspan="1"
                                                    aria-label="Salary: activate to sort column ascending"
                                                    style="">Date Created
                                                </th>

                                            </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($cases  as $case)
                                                <tr role="row" class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$case->id}}">
                                                    <td class="sorting_1 ">{{$case->id}}</td>
                                                    <td>{{$case->case_id}}</td>
                                                    <td>{{$case->client->name}}</td>
                                                    <td>{{$case->patient_name}}</td>
                                                    <td>{{$case->initDeliveryDate() }}
                                                        &nbsp;&nbsp; {{$case->initDeliveryTime()}}</td>
                                                    <td>{{$case->actualDeliveryDate()=="" ? "Not yet" : $case->actualDeliveryDate()}}
                                                        &nbsp;&nbsp; {{$case->actualDeliveryTime() ?? ""}}</td>
                                                    <td>
                                                        @if(str_contains($case->status(), "Completed") )
                                                            <span class="badge badge-success solent-case-status-badge">
                                                                           {{$case->status()}} </span>
                                                        @elseif(str_contains($case->status(), "In-Progress") || str_contains($case->status(), "Active"))
                                                            <span style="width:auto; margin: auto; text-align: center"
                                                                  class="badge badge-primary solent-case-status-badge">
                                                                           <span class="tooltipX"> {{$case->status()}}
                                                                               <span class="tooltiptext">{!!  $case->getStatusToolTipHTML() !!}</span>
                                                                </span></span>
                                                        @elseif(str_contains($case->status(), "Waiting"))
                                                            <span style="width:auto; margin: auto; text-align: center"
                                                                  class="badge badge-danger solent-case-status-badge">
                                                                     {{$case->status()}} </span>
                                                        @else
                                                            <span style="width:auto; margin: auto; text-align: center"
                                                                  class="badge badge-warning solent-case-status-badge">
                                                                           <span class="tooltipX"> {{$case->status()}}
                                                                               <span class="tooltiptext">{!!  $case->getStatusToolTipHTML() !!}</span>
                                                                </span></span>

                                                        @endif

                                                    </td>
                                                    <td>

                                                        @foreach($case->tags as $tag)
                                                            <i title="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->text : ""}}"
                                                               style="color:{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->color : '#000'}}"
                                                               class="{{isset($tag->originalTagRecord) ? $tag->originalTagRecord->icon : ''}}  fa-lg"></i>
                                                        @endforeach
                                                    </td>
                                                    <td>{{$case->createdAtDate()}}
                                                        &nbsp;&nbsp; {{$case->createdAtTime() }}</td>


                                                </tr>
                                                <div class="modal" tabindex="-1" role="dialog" id="actionsDialog{{$case->id}}">

                                                    <input type="hidden" name="case_id" value="{{$case->id}}">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Case Actions</h5>

                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                                                        <label ><b>Jobs:</b></label><br>



                                                                        @foreach( $case->jobs as $job)

                                                                            @php
                                                                                $unit = explode(', ',$job->unit_num);
                                                                            @endphp

                                                                            <span >{{$job->unit_num}} - {{$job->jobType->name ?? "No Job Type"}} - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}
                                                                                {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}<br>
                                                                                {{isset($job->abutmentR)  && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>
                                                                        @endforeach
                                                                    </div></div>
                                                                @if(count($case->notes)>0)
                                                                    <hr>
                                                                    <label ><b>Notes:</b></label><br>
                                                                    @foreach($case->notes as $note)
                                                                        <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px" disabled>

                                                                            <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer fullBtnsWidth" >
                                                                <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">

                                                                    @if(!isset($trashedCases))
                                                                        <div class="row">
                                                                            <!-------------------------
                                                                                   ------ View Voucher ------
                                                                                   -------------------------->
                                                                            <div class="col-6 padding5px" >
                                                                                <a  href="{{route('view-voucher',$case->id)}}">
                                                                                    <button type="button" class="btn btn-info "><i
                                                                                                class="fas fa-print"></i> View Voucher </button>
                                                                                </a></div>

                                                                            <!-------------------------
                                                                            -------- View Case --------
                                                                            -------------------------->
                                                                            <div class="col-6 padding5px" >
                                                                                <a  href="{{route('view-case',['id' =>$case->id ,'stage' =>-2 ])}}">
                                                                                    <button type="button" class="btn btn-info "><i
                                                                                                class="far fa-file-alt"></i> View Case </button>
                                                                                </a></div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <!-------------------------
                                                                            ------- LOCK CASE -------
                                                                            -------------------------->
                                                                            @if(Auth()->user()->is_admin )
                                                                                @if(!$case->locked)
                                                                                    <div class="col-4 padding5px " >
                                                                                        <a  href="{{route('lock-case',$case->id)}}">
                                                                                            <button type="button" class=" btn btn-dark "><i
                                                                                                        class="fas fa-lock"></i>
                                                                                                Lock Case</button>
                                                                                        </a></div>
                                                                                @endif
                                                                                @if($case->locked)
                                                                                    <div class="col-4 padding5px " >
                                                                                        <a  href="{{route('unlock-case',$case->id)}}">
                                                                                            <button type="button" class=" btn btn-dark "><i
                                                                                                        class="fas fa-lock-open"></i>
                                                                                                Unlock Case
                                                                                            </button>
                                                                                        </a></div>
                                                                                @endif
                                                                            <!-------------------------
                                                                                 ------ DELETE CASE ------
                                                                                 -------------------------->
                                                                                @if(!$case->locked)
                                                                                    <div class="col-4 padding5px " >
                                                                                        <a data-clientName="{{ $case->client->name }}"
                                                                                           data-patientName="{{ $case->patient_name }}"
                                                                                           style="color:red" onclick="caseDelConfirmation(event )" href="{{route('delete-case',$case->id)}}">
                                                                                            <button type="button" class="  btn btn-danger "> <i
                                                                                                        class="fas fa-trash"></i> Delete Case </button>
                                                                                        </a></div>
                                                                                @endif
                                                                            @endif
                                                                        <!-------------------------
                                                                                  -------- Edit CASE --------
                                                                                  -------------------------->
                                                                            @if(Auth()->user()->is_admin ||
                                                                            ($permissions && ($permissions->contains('permission_id', 102))) ||
                                                                            ($permissions &&
                                                                            ((!isset($case->actual_delivery_date)&& $permissions->contains('permission_id', 115)))
                                                                            || ($case->jobs[0]->stage == 1 && $permissions->contains('permission_id', 1)))
                                                                            )
                                                                                @if(!$case->locked)

                                                                                    <div class="col-4 padding5px" >
                                                                                        <a  href="{{route('edit-case-view',$case->id)}}">
                                                                                            <button type="button" class="btn btn-warning "><i class="fa-solid fa-pen-to-square"></i> Edit Case</button>
                                                                                        </a></div>
                                                                                @endif
                                                                            @endif

                                                                        </div>
                                                                        <div class="row">
                                                                        @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 116)) && !$case->locked)
                                                                            <!-------------------------
                                                                                  ------- Reject CASE -------
                                                                                  -------------------------->
                                                                                <div class="col-4 padding5px" >
                                                                                    <a  href="{{route('reject-case-view',$case->id )}}">
                                                                                        <button type="button" class="btn btn-outline-danger"><i
                                                                                                    class="fas fa-times x2"></i>
                                                                                            Reject case </button>
                                                                                    </a></div>
                                                                        @endif
                                                                        <!-------------------------
                                                                                  ------- Repeat CASE -------
                                                                                  -------------------------->
                                                                            @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 117))&&!$case->locked)
                                                                                <a class="dropdown-item"
                                                                                   href="{{route('repeat-case-view',$case->id)}}"></a>
                                                                                <div class="col-4 padding5px" >
                                                                                    <a  href="{{route('repeat-case-view',$case->id)}}">
                                                                                        <button type="button" class="btn btn-outline-warning "><i
                                                                                                    class="fas fa-undo"></i>
                                                                                            Repeat case </button>
                                                                                    </a></div>
                                                                            @endif
                                                                        <!-------------------------
                                                                                  ------- Modify CASE -------
                                                                                  -------------------------->
                                                                            @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 118)) && !$case->locked)
                                                                                <div class="col-4 padding5px" >
                                                                                    <a  href="{{route('modify-case-view',$case->id)}}">
                                                                                        <button type="button" class="btn btn-outline-warning  "><i
                                                                                                    class="fa fa-broom "></i>
                                                                                            Modify case</button>
                                                                                    </a></div>
                                                                            @endif


                                                                        <!-------------------------
                                                                                  -------- REDO CASE --------
                                                                                  -------------------------->
                                                                            @if(!$case->delivered_to_client && !$case->locked)
                                                                                @if (Auth()->user()->is_admin  || $permissions->contains('permission_id', 119))

                                                                                    <div class="col-4 padding5px" >
                                                                                        <a  href="{{route('redo-case-view',$case->id)}}">
                                                                                            <button type="button" class="btn btn-outline-warning "><i
                                                                                                        class="fa fa-broom "></i>
                                                                                                Redo case </button>
                                                                                        </a></div>
                                                                                @endif
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                    <!-------------------------
                                                                                ------ RESTORE CASE ------
                                                                                -------------------------->
                                                                        <div class="col-12 padding5px" >
                                                                            <a  href="{{route('restore-case',$case->id)}}">
                                                                                <button type="button" class="btn btn-danger ">Restore case </button>
                                                                            </a></div>
                                                                    @endif
                                                                    <div class="col-12 padding5px" >
                                                                        <button type="button" class="btn btn-secondary " data-dismiss="modal" style="width:100%">Cancel</button>
                                                                    </div>
                                                                </div>


                                                            </div>



                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                    @push('js')
                    <!-- Responsive and datable js -->
                    <script type="text/javascript">
                        $(document).ready(function () {
                            var table =
                                $('#datatable').DataTable({
                                    "pageLength": 25,
                                    "searching": false,
                                    "lengthChange": false,
                                    "columnDefs": [
                                        {targets: [0, 1, 8], visible: false},
                                    ],
                                    "order": [[5, "desc"], [4, "asc"]],
                                    // "scrollX":       true,
                                    //stateSave: true,
                                });

                            $('a.toggle-vis').on('click', function (e) {
                                e.preventDefault();

                                // Get the column API object
                                var column = table.column($(this).attr('data-column'));

                                // Toggle the visibility
                                column.visible(!column.visible());
                            });
                        });

                        function toggleCheckBox(ele) {
                            var $tc = $(ele).find('input:checkbox:first'),
                                tv = $tc.attr('checked');

                            $tc.attr('checked', !tv);
                        }
                        function caseDelConfirmation(ev) {
                            ev.preventDefault();
                            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
                            console.log(urlToRedirect); // verify if this is the right URL
                            swal.fire({
                                title: "Are you sure?",
                                text: "This will also delete related info. (invoice, photos .. etc)",
                                icon: "warning",
                                showDenyButton: true,
                                confirmButtonText: 'Delete Case',
                                denyButtonText: 'Cancel',
                            })
                                .then((willDelete) => {
                                // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                                if (willDelete.isConfirmed)
                            {
                                window.location = urlToRedirect;
                                //swal.fire("Poof! Your imaginary file has been deleted!");
                            }
                        else
                            {
                                swal.fire("Deletion Canceled.");
                            }
                        })
                            ;
                        }
                    </script>
            @endpush


@endsection

