@extends('layouts.app' ,[ 'pageSlug' => "Cases List"])
@section('content')

    <style>
        .content{

        }
        /* Tooltip styling */
        .tooltiptext {
            display: none;
        }

        /* Case preview action hover states */
        .solent-case-actions-modal__actions .btn-primary:hover,
        .solent-case-actions-modal__actions .btn-primary:focus-visible,
        .solent-case-actions-modal__actions .btn-outline-primary:hover,
        .solent-case-actions-modal__actions .btn-outline-primary:focus-visible,
        .solent-case-actions-modal__actions .btn-outline-secondary:hover,
        .solent-case-actions-modal__actions .btn-outline-secondary:focus-visible {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }

        .solent-case-actions-modal__actions .btn-outline-danger:hover,
        .solent-case-actions-modal__actions .btn-outline-danger:focus-visible {
            background: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }

        .solent-case-actions-modal__actions .btn-light:hover,
        .solent-case-actions-modal__actions .btn-light:focus-visible {
            background: #334155 !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }

        /* Filter section improvements */
        .kt-subheader__search label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .kt-subheader__search .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.075);
        }

        .kt-subheader__search .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        /* Filter container with subtle shadow and border */
        .container.full-width {
            background-color: #f8f9fa;
            border-radius: 5px;
            /*padding: 4px;*/
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        /* Better spacing */
        .filter-section {
            margin-bottom: 1.5rem;
        }

        .cases-filter-tools {
            display: flex;
            align-items: stretch;
            justify-content: flex-end;
            gap: 8px;
        }

        .cases-filter-tools .btn-group {
            min-width: 0;
        }

        .cases-filter-tools .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            margin: 0;
        }

        .cases-filter-tools .btn > i {
            position: static;
            margin: 0;
        }

        .cases-filter-tools .cases-columns-menu {
            width: 220px;
            max-width: calc(100vw - 32px);
            max-height: min(440px, calc(100dvh - 32px));
            overflow-y: auto;
            padding: 8px;
        }

        .cases-columns-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            margin: 0;
            padding: 8px 12px;
            white-space: normal;
            text-align: start;
        }

        .cases-columns-menu .form-check-input {
            position: static;
            flex: 0 0 16px;
            width: 16px;
            height: 16px;
            margin: 0;
        }

        .cases-results #casesTable {
            min-width: 700px;
            table-layout: auto;
        }

        /* Table actions styling */
        .table-actions {
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .content {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .row {
                padding: 3px;
            }

            .pagination {
                flex-wrap: wrap;
            }

            /* Better button display on mobile */
            .cases-filter-actions .btn-primary {
                width: 100%;
                min-height: 44px;
                margin: 0;
            }

            /* Fix filter layout on mobile */
            .justify-content-end {
                justify-content: space-between !important;
            }

            /* Make action buttons more visible on mobile */
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 1rem;
            }

            .bootstrap-select ul.dropdown-menu li:first-child {
                display: none;
            }

            body.white-content .cases-results .dataTables_length {
                display: none;
            }

            body.white-content .cases-results .solent-datatable-toolbar {
                justify-content: flex-end;
            }

            body.white-content .cases-results .dataTables_filter {
                width: auto;
                max-width: 100%;
                margin: 0;
                float: none;
                text-align: end;
            }

            body.white-content .cases-results .dataTables_filter label {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                width: auto;
            }

            body.white-content .cases-results .dataTables_filter input {
                width: 160px;
                height: 40px;
                min-height: 40px;
                padding: 6px 10px;
                font-size: 16px;
            }

            /* Responsive button group on mobile */
            .cases-filter-tools > .btn-group,
            .cases-filter-tools > a {
                flex: 1 1 0;
            }

            .cases-filter-tools .btn-group,
            .cases-filter-tools .dropdown-toggle {
                width: 100%;
            }

            .cases-filter-tools .cases-columns-menu {
                left: 0 !important;
                right: auto !important;
                transform: translate3d(0, 44px, 0) !important;
            }

            .cases-results > .row,
            .cases-results > .row > .col-12 {
                margin: 0;
                padding: 0;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);

    @endphp
    @if(!isset($isSearchResults))
        @if(!isset($trashedCases))
            @if(isset($clients))
                <form class="kt-form" method="GET" action="{{route('cases-index')}}">
                    @else
                        <form class="kt-form" method="GET" action="{{route('dentist-cases',['id' =>$id])}}">
                            <input type="hidden" class="form-control" name="id" value="{{$id}}">
                            @endif
                            <div class="container full-width list-filter-shell">
                                <div class="row " style="padding-bottom:0;justify-content: flex-end;">
                                    <!-- Date filtering section -->
                                    <div class="col-6 col-sm-6 col-md-3 mb-3">
                                        <div class="kt-subheader__search">
                                            <label class="solent-filter-label"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>From (Start of):</span></label>
                                            <input type="date" class="form-control" name="from" value="{{$from}}">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-3 mb-3">
                                        <div class="kt-subheader__search">
                                            <label class="solent-filter-label"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>To (End of):</span></label>
                                            <input type="date" class="form-control" name="to" value="{{$to}}">
                                        </div>
                                    </div>

                                    <!-- New Date Column Selection -->
                                    <div class="col-6 col-sm-6 col-md-3 mb-3">
                                        <div class="kt-subheader__search">
                                            <label class="solent-filter-label"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i><span>Filter Date By:</span></label>
                                            <select class="form-control" name="date_column">
                                                <option value="initial_delivery_date" {{ isset($date_column) && $date_column == 'initial_delivery_date' ? 'selected' : '' }}>Initial Delivery Date</option>
                                                <option value="actual_delivery_date" {{ isset($date_column) && $date_column == 'actual_delivery_date' ? 'selected' : '' }}>Actual Delivery Date</option>
                                                <option value="created_at" {{ isset($date_column) && $date_column == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Doctor selection -->
                                    <div class="col-6 col-sm-6 col-md-3 mb-3">
                                        @if(isset($clients))
                                            <div class="dropdown" style="text-align: left;">
                                                <label class="solent-filter-label"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i><span>Doctor:</span></label>
                                                <br>
                                                <select style="width:100%" class="selectpicker clearOnAll greyBG"
                                                        multiple
                                                        name="doctor[]" id="doctor"
                                                        data-live-search="true">
                                                        <option
                                                            value="all" {{(isset($selectedClients) && in_array("All" ,$selectedClients)) ? 'selected' : ''}}>
                                                            All
                                                        </option>
                                                        @foreach($clients as $d)
                                                            <option
                                                                value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Second row for additional filters and actions -->
                                <div class="row cases-filter-actions" style="padding-bottom:0;">
                                    <!-- Submit button - Left aligned -->
                                    <div class="col-md-6 col-12 mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i> Apply Filters
                                        </button>
                                    </div>

                                    <!-- Actions aligned to the right -->
                                    <div class="col-md-6 col-12 mb-3 text-right cases-filter-tools">
                                        <div class="btn-group" role="group" aria-label="Table actions">
                                            <!-- Show/Hide columns button -->
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                        data-boundary="viewport"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                    <i class="fas fa-columns"></i> <span>Columns</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right cases-columns-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="toggle-vis dropdown-item" data-column="0" href="#"
                                                       onclick="toggleCheckBox(this); toggleColumnVisibilty(1);"><input
                                                            type="checkbox"
                                                            class="form-check-input"/>
                                                        ID</a>
                                                    <a class="toggle-vis dropdown-item" data-column="1" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(2);"><input
                                                            type="checkbox"
                                                            class="form-check-input"/>Case ID</a>
                                                    <a class="toggle-vis dropdown-item" data-column="2" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(3);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Doctor</a>
                                                    <a class="toggle-vis dropdown-item" data-column="3" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(4);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Patient name</a>
                                                    <a class="toggle-vis dropdown-item" data-column="4" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(5);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Initial Deli.
                                                        Date</a>
                                                    <a class="toggle-vis dropdown-item" data-column="5" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(6);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Delivered</a>
                                                    <a class="toggle-vis dropdown-item" data-column="6" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(7);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Status</a>
                                                    <a class="toggle-vis dropdown-item" data-column="7" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(8);"><input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked/>Tags</a>
                                                    <a class="toggle-vis dropdown-item" data-column="8" href="#"
                                                       onclick="toggleCheckBox(this);toggleColumnVisibilty(9);"><input
                                                            type="checkbox"
                                                            class="form-check-input"/> Date
                                                        Created </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Trash icon button - now separate from the columns button -->
                                        <a href="{{route('deleted-cases')}}" class="btn btn-outline-danger">
                                            <i class="fa-regular fa-trash-can"></i> <span>Deleted</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- Added better spacing between filters and table -->
                            <div class="filter-section"></div>
                        </form>
                    @endif
                    @endif
                    <div class="container full-width list-results-shell cases-results solent-table-frame">
                        <div class="row" style="justify-content: flex-end;">
                            <div class="col-12">
                                <br>
                                <table id="casesTable"
                                       class="table-striped compact sunriseTable"
                                       role="grid"
                                       style="width:100%">
                                    <thead>
                                    <tr role="row">
                                        <th>ID
                                        </th>
                                        <th>Case ID
                                        </th>
                                        <th>Doctor
                                        </th>
                                        <th>Patient</th>
                                        <th class="initDeliDateHeader">Initial Deli. Date</th>
                                        <th>Date Delivered</th>
                                        <th>Status</th>
                                        <th class="tagsHeader">Tags</th>
                                        <th>Date Created
                                        </th>

                                    </tr>
                                    </thead>

                                    <tbody>

                                    @foreach($cases  as $case)

                                        <tr role="row" class="odd clickable" data-toggle="modal"
                                            data-target="#caseActionsModal{{ $case->id }}">
                                            <td class="sorting_1 ">{{$case->id }}</td>
                                            <td>{{$case->case_id ?? "x"}}</td>
                                            <td>{{$case->client->name ?? "x"}}</td>
                                            <td>{{$case->patient_name ?? "x"}}</td>
                                            <td class="initDeliDateTD">{{$case->initDeliveryDate() ?? "x" }}
                                                &nbsp;&nbsp; {{$case->initDeliveryTime() ?? "x"}}</td>
                                            <td>{{$case->actualDeliveryDate()=="" ? "Not yet" : $case->actualDeliveryDate()}}
                                                &nbsp;&nbsp; {{$case->actualDeliveryTime() ?? ""}}</td>
                                            <td>
                                                @if(str_contains($case->status(), "Completed") )
                                                    <span class="badge badge-success solent-case-status-badge">
                                                                           {{$case->status()}} </span>
                                                @elseif(str_contains($case->status(), "In-Progress") || str_contains($case->status(), "Active"))
                                                    <span style="width:auto; margin: auto; text-align: center"
                                                          class="badge badge-primary solent-case-status-badge">
                                                                           <span class="tooltipX"> {{str_replace("Active in","",$case->status())}}
                                                                               <span
                                                                                   class="tooltiptext">{!!  $case->getStatusToolTipHTML() !!}</span>
                                                                </span></span>
                                                @elseif(str_contains($case->status(), "Waiting"))
                                                    <span style="width:auto; margin: auto; text-align: center"
                                                          class="badge badge-danger solent-case-status-badge">
                                                                @php
                                                                    $status =  preg_replace('/' . "in" . '/', "", str_replace("Waiting","",$case->status()), 1);
                                                                @endphp

                                                        {{$status}} </span>
                                                @else
                                                    <span style="width:auto; margin: auto; text-align: center"
                                                          class="badge badge-warning solent-case-status-badge">
                                                                           <span class="tooltipX"> {{$case->status()}}
                                                                               <span
                                                                                   class="tooltiptext">{!!  $case->getStatusToolTipHTML() !!}</span>
                                                                </span></span>

                                                @endif

                                            </td>
                                            <td class="tagsTD">

                                                @foreach($case->tags as $tag)
                                                    @if(isset($tag->originalTagRecord))
                                                        <i title="{{$tag->originalTagRecord->text}}"
                                                           style="color:{{$tag->originalTagRecord->color}}"
                                                           class="{{$tag->originalTagRecord->icon}}  fa-lg"></i>
                                                    @endif
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


                                                                @foreach( $case->jobs as $job)

                                                                    @php
                                                                        $unit = explode(', ',$job->unit_num);
                                                                    @endphp

                                                                    <span>{{$job->unit_num}} - {{$job->jobType->name ?? "No Job Type"}} - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}
                                                                        {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}<br>
                                                                                    {{isset($job->abutmentR)  && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>
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

                                                                    <span
                                                                        class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>
                                                                    <span class="noteText">{{$note->note}}</span>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer fullBtnsWidth">
                                                        <div class="row"
                                                             style=" margin-right: 0px; margin-left: 0px;width:100%">

                                                            @if(!isset($trashedCases))
                                                                <div class="row">
                                                                    <!-------------------------
                                                                           ------ View Voucher ------
                                                                           -------------------------->
                                                                    <div class="col-6 padding5px">
                                                                        <a href="{{route('view-voucher',$case->id)}}">
                                                                            <button type="button" class="btn btn-info ">
                                                                                <i
                                                                                    class="fas fa-print"></i> Print
                                                                                Voucher
                                                                            </button>
                                                                        </a></div>

                                                                    <!-------------------------
                                                                    -------- View Case --------
                                                                    -------------------------->
                                                                    <div class="col-6 padding5px">
                                                                        <a href="{{route('view-case',['id' =>$case->id ,'stage' =>-2 ])}}">
                                                                            <button type="button" class="btn btn-info ">
                                                                                <i
                                                                                    class="far fa-file-alt"></i> View
                                                                                Case
                                                                            </button>
                                                                        </a></div>
                                                                </div>
                                                                <div class="row">
                                                                    <!-------------------------
                                                                    ------- LOCK CASE -------
                                                                    -------------------------->
                                                                    @if(Auth()->user()->is_admin || $permissions->contains('permission_id', 130))
                                                                        @if(!$case->locked)
                                                                            <div class="col-4 padding5px ">
                                                                                <a href="{{route('lock-case',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class=" btn btn-dark "><i
                                                                                            class="fas fa-lock"></i>
                                                                                        Lock Case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                        @if($case->locked)
                                                                            <div class="col-4 padding5px ">
                                                                                <a href="{{route('unlock-case',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class=" btn btn-dark "><i
                                                                                            class="fas fa-lock-open"></i>
                                                                                        Unlock Case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                    @endif
                                                                    @if(Auth()->user()->is_admin )

                                                                        <!-------------------------
                                                                                 ------ DELETE CASE ------
                                                                                 -------------------------->
                                                                        @if(!$case->locked)
                                                                            <div class="col-4 padding5px ">
                                                                                <a data-clientName="{{ $case->client->name }}"
                                                                                   data-patientName="{{ $case->patient_name }}"
                                                                                   style="color:red"
                                                                                   onclick="caseDelConfirmation(event )"
                                                                                   href="{{route('delete-case',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class="  btn btn-danger "><i
                                                                                            class="fas fa-trash"></i>
                                                                                        Delete Case
                                                                                    </button>
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

                                                                            <div class="col-4 padding5px">
                                                                                <a href="{{route('edit-case-view',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class="btn btn-warning "><i
                                                                                            class="fa-solid fa-pen-to-square"></i>
                                                                                        Edit Case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                    @endif

                                                                </div>
                                                                <div class="row">
                                                                    @if (isset($case->actual_delivery_date))
                                                                        @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 116)) && !$case->locked)
                                                                            <!-------------------------
                                                                                  ------- Reject CASE -------
                                                                                  -------------------------->
                                                                            <div class="col-4 padding5px">
                                                                                <a href="{{route('reject-case-view',$case->id )}}">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-danger">
                                                                                        <i
                                                                                            class="fas fa-times x2"></i>
                                                                                        Reject case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                        <!-------------------------
                                                                                  ------- Repeat CASE -------
                                                                                  -------------------------->
                                                                        @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 117))&&!$case->locked)

                                                                            <div class="col-4 padding5px">
                                                                                <a href="{{route('repeat-case-view',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-warning ">
                                                                                        <i
                                                                                            class="fas fa-undo"></i>
                                                                                        Repeat case
                                                                                    </button>
                                                                                </a></div>

                                                                        @endif
                                                                        <!-------------------------
                                                                                  ------- Modify CASE -------
                                                                                  -------------------------->
                                                                        @if ((Auth()->user()->is_admin  || $permissions->contains('permission_id', 118)) && !$case->locked)
                                                                            <div class="col-4 padding5px">
                                                                                <a href="{{route('modify-case-view',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-warning  ">
                                                                                        <i
                                                                                            class="fa fa-broom "></i>
                                                                                        Modify case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <div class="row">
                                                                    <!-------------------------
                                                                    -------- REDO CASE --------
                                                                    -------------------------->
                                                                    @if($case->delivered_to_client == 1)
                                                                        @if (Auth()->user()->is_admin  || $permissions->contains('permission_id', 119))

                                                                            <div class="col-4 padding5px">
                                                                                <a href="{{route('redo-case-view',$case->id)}}">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-warning ">
                                                                                        <i
                                                                                            class="fa fa-broom "></i>
                                                                                        Redo case
                                                                                    </button>
                                                                                </a></div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <!-------------------------
                                                                                ------ RESTORE CASE ------
                                                                                -------------------------->
                                                                <div class="col-12 padding5px">
                                                                    <a href="{{route('restore-case',$case->id)}}">
                                                                        <button type="button" class="btn btn-danger ">
                                                                            Restore case
                                                                        </button>
                                                                    </a></div>
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

                                        </div>

                                    @endforeach
                                    </tbody>

                                </table>

                            </div>
                            <div style="text-align:right">

                            </div>
                        </div>
                    </div>

                    </div>
                </form>

                @foreach($cases as $case)
                    <x-partiels.caseActionsModal
                        :case="$case"
                        :modalId="'caseActionsModal' . $case->id"
                        :showCaseManagement="true"
                        :trashed="isset($trashedCases)" />
                @endforeach

                @push('js')
                    {{--<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>--}}
                    <!-- Responsive and datable js -->
                    <script type="text/javascript">
                        $(document).ready(function () {

                            $("#casesTable").DataTable({
                                "pageLength": 25,
                                "searching": true,
                                "lengthChange": true,
                                "autoWidth": false,
                                "responsive": false,
                                "dom": "<'solent-datatable-toolbar'lf>r<'solent-table-scroll't><'solent-datatable-foot'ip>",
                                "columnDefs": [
                                    { targets: [1, 8], visible: false },
                                    { targets: [4, 7], visible: !window.matchMedia('(max-width: 768px)').matches },
                                    { targets: [2, 3], className: 'solent-table-name' }
                                ],
                                "order": []
                                //{targets: [0, 1, 8], visible: false}
                            });


                        });

                        function toggleColumnVisibilty(colNumber) {

                            var column = $('#casesTable').DataTable().column(colNumber - 1);
                            column.visible(!column.visible());
                        }

                        function toggleCheckBox(ele) {
                            var $tc = $(ele).find('input:checkbox:first'),
                                tv = $tc.attr('checked');

                            $tc.attr('checked', !tv);
                        }

                        function caseDelConfirmation(ev) {
                            ev.preventDefault();
                            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
                            var clientName = ev.currentTarget.getAttribute('data-clientName');
                            var patientName = ev.currentTarget.getAttribute('data-patientName');

                            //console.log(urlToRedirect); // verify if this is the right URL
                            swal.fire({
                                title: "You sure You want to delete.. </br>" + clientName + " - " + patientName,
                                text: "This will also delete related info. (invoice, photos .. etc)?",
                                icon: "warning",
                                showDenyButton: true,
                                confirmButtonText: 'Delete Case',
                                denyButtonText: 'Cancel'
                            }).then((willDelete) => {
                                if (willDelete) {
                                    window.location = urlToRedirect;
                                } else {
                                    swal.fire("Case NOT deleted.");
                                }
                            });

                        }
                    </script>

                @endpush


                @endsection

