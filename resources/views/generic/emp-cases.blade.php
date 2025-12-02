@extends('layouts.app' ,[ 'pageSlug' => "Cases ( Legacy ) " ])
@php
    $title="Cases List";
        switch ($stage) {
          case "1":
            $title= "Design Cases";
            break;
          case "2":
            $title= "Milling Cases";
            break;
          case "3":
            $title= "3D Printing Cases";
            break;
            case "4":
            $title= "Sintering Furnace Cases";
            break;
            case "5":
            $title= "Pressing Furnace Cases";
            break;
            case "6":
            $title= "Finish & Build up Cases";
            break;
            case "7":
            $title= "Quality Control Cases";
            break;
            case "8":
            $title= "Delivery Cases ( Deprecated)";
            break;
        }
@endphp
@section('head')

<style>
    @media (max-width: 768px) {
        .idColumn, .dataTables_length {
            display: none;
        }
        .card{
            padding:0px !important;
        }
        .stuffContainer{
            padding-right: 5px !important;
            padding-left: 5px !important;
        }
        .card-body{
            padding-left: 0.25rem !important;
            padding-right: 0.25rem !important;
        }
    }

</style>

@endsection
@section('title')
{{$title}}
@endsection
@section('content')
@php
$color= "#212529";
$permissions = Cache::get('user'.Auth()->user()->id);
@endphp
<div class="row ">
    <div class="col-lg-12 col-sm-12 stuffContainer">
        <div class="tab-2 m-b-30">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{isset($_COOKIE['activeEmpDBTab']) &&$_COOKIE['activeEmpDBTab'] == '#tab1' ? ' active show ' : ''  }}" onclick="tabChanged(this)" href="#tab1" data-toggle="tab" aria-expanded="false">Active Cases <span class="badge bg-info m-1 activeBadge">{{$activeCases->count()}}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{isset($_COOKIE['activeEmpDBTab']) &&$_COOKIE['activeEmpDBTab'] == '#tab2' ? ' active show ' : ''  }}" onclick="tabChanged(this)" href="#tab2" data-toggle="tab" aria-expanded="false">Waiting Cases <span class="badge bg-info m-1 waitingBadge">{{$waitingCases->count()}}</span></a>
                </li>

            </ul>
            <div class="tab-content bg-white">
                <div class="tab-pane {{isset($_COOKIE['activeEmpDBTab']) &&$_COOKIE['activeEmpDBTab'] == '#tab1' ? ' active show ' : ''  }} p-4" id="tab1">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 stuffContainer">

                            <div class="card bg-white m-b-30">
                                <div class="card-body color-table">
                                    <div class="card-title border-b mb-4">
                                        <h5 style="color:steelblue"><b>Active Cases</b></h5>
                                    </div>
                                    <table class="table display responsive"  style="width:100%">
                                        <thead >
                                        <tr >
                                            <th  class="idColumn">ID</th>
                                            <th>Doctor</th>
                                            <th>Patient</th>
                                            <th>Delivery Date</th>
                                            <th>Units #</th>
                                            {{--<th>Tags</th>--}}
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($activeCases as $case)
                                            @php
                                            if ($stage==6)
                                            if(!$case->allJobsInSameStage(6))
                                            $color="red";
                                            @endphp
                                            <tr class="" style="color:{{$color}}">
                                                <td  class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                <td><p class="">{{$case->client->name}}</p></td>
                                                <td><p class="">{{$case->patient_name}}</p></td>
                                                <td><p class="">{{str_replace('T', "     ",$case->initial_delivery_date)}}</p></td>
                                                <td><p class="">{{$case->unitsAmount($stage)}}</p></td>
                                                {{--<td>--}}
                                                 {{--@foreach($case->tags as $tag)--}}
                                                {{--<span style="padding:1px;color:{{$tag->originalTagRecord->color}};border:2px solid {{$tag->originalTagRecord->color}};border-radius: 50%">{{$tag->originalTagRecord->initials}}</span>--}}
                                                {{--@endforeach--}}
                                                {{--</td>--}}
                                                <td style="overflow:visible">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        @if($stage =='2')
                                                            <a class="dropdown-item" data-toggle="modal" data-target="#MEX{{$case->id}}"> <i class="kt-nav__link-icon flaticon2-contract"></i> <span class="kt-nav__link-text">Externally Milled</span> </a>

                                                        @endif
                                                            <a class="dropdown-item" data-toggle="modal" data-target="#confirmCompletion{{$case->id}}"> <i class="kt-nav__link-icon flaticon2-contract"></i> <span class="kt-nav__link-text">Complete</span> </a>

                                                            @if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 102)||$permissions->contains('permission_id', 1))))
                                                                <a class="dropdown-item" href="{{route('edit-case-view',$case->id)}}">Edit case</a>
                                                            @endif
                                                        <a class="dropdown-item" href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage])}}">View case</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal" tabindex="-1" role="dialog" id="myModal{{$case->id}}">
                                                <form action="{{route('assign-to-delivery-person')}}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="case_id" value="{{$case->id}}">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Sending case to delivery</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div >
                                                                    <label>Delivery Driver:</label>

                                                                    <div class="kt-form__control">
                                                                        <select class="form-control" id="driver" name="driver_user">
                                                                            @foreach($drivers as $driver)
                                                                                <option value="{{$driver->id}}">{{$driver->first_name . ' ' . $driver->last_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <br/>

                                                                </div>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Send</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            @if($stage =='2')
                                                <div class="modal" tabindex="-1" role="dialog" id="MEX{{$case->id}}">
                                                    <form action="{{route('externally-milled')}}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="case_id" value="{{$case->id}}">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Case milling information</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group row">
                                                                        <div class="form-group col-6 lab_id">
                                                                            <label for="lab_id">Lab name: </label>
                                                                            <select class="form-control" id="lab_id" name="lab_id">
                                                                                <option selected >Select your lab</option>
                                                                                @foreach($labs as $lab)
                                                                                    <option value="{{$lab->id}}" >{{$lab->name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            <div class="modal" tabindex="-1" role="dialog" id="confirmCompletion{{$case->id}}">
                                                <form action="{{route('finish-case',['caseId'=> $case->id,'stage'=>$stage] )}}" method="GET">
                                                    @csrf
                                                    <input type="hidden" name="case_id" value="{{$case->id}}">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Case Completion</h5>

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

                                                                            <span >{{$job->unit_num}} - {{$job->jobType->name}} - {{$job->material->name}} {{$job->color =='0' ? "":" - " .$job->color}}
                                                                           {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}<br>
                                                                                {{isset($job->abutmentR)  && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>
                                                                    @endforeach
                                                                </div></div>
                                                                @if(count($case->notes)>0)
                                                                <hr>
                                                                <label ><b>Notes:</b></label><br>

                                                                @foreach($case->notes as $note)

                                                                    <div class="form-control" style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px" disabled>

                                                                        <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->first_name . '] : ' }}</span><br> <span class="noteText">{{$note->note}}</span>
                                                                    </div>
                                                                @endforeach
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer" >
                                                                <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">
                                                                    <div class="col-3 " style="width:100%"> <a  href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage])}}">
                                                                            <button type="button" class="btn btn-secondary" style="width:100%">View </button></a></div>
                                                                    <div class="col-6 " style="width:100%"> <button type="submit" class="btn btn-primary" style="width:100%">Complete</button></div>
                                                                    <div class="col-3 " style="width:100%"> <button type="button" class="btn btn-danger" data-dismiss="modal" style="width:100%">Cancel</button></div>
                                                                </div>



                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>


                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="tab-pane {{isset($_COOKIE['activeEmpDBTab']) &&$_COOKIE['activeEmpDBTab'] == '#tab2' ? ' active show ' : ''  }} p-4" id="tab2">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 stuffContainer">

                            <div class="card bg-white m-b-30">
                                <div class="card-body color-table">
                                    <div class="card-title border-b mb-4">
                                        <h5 style="color:indianred"><b>Waiting Cases</b></h5>
                                    </div>
                                    <table class="table display responsive"  style="width:100%">
                                        <thead>
                                        <tr>
                                            <th class="idColumn">ID</th>
                                            <th>Doctor</th>
                                            <th>Patient</th>
                                            <th>Delivery Date</th>
                                            <th>Units #</th>
                                            <th>Tags</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($waitingCases as $case)
                                            @php
                                               $tag="";
                                                   if ($stage==6){
                                                    if (!$case->shouldShowForFinishing()) continue;
                                                   if($case->modelNotReady())
                                                   $tag="Not Ready";
                                                   }
                                            @endphp
                                            <tr class="" style="">
                                                <td class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                <td><p class="">{{$case->client->name}}</p></td>
                                                <td><p class="">{{$case->patient_name}}  <span style="color:red">{{$tag}}</span></p></td>
                                                <td><p class="">{{str_replace('T', "     ",$case->initial_delivery_date)}}</p></td>
                                                <td style="text-align: center"><p class="">{{$case->unitsAmount($stage)}}</p></td>
                                                <td>

                                                    @foreach($case->tags as $tag)
                                                        <span style="padding:1px;color:{{$tag->originalTagRecord->color}};border:2px solid {{$tag->originalTagRecord->color}};border-radius: 50%">{{$tag->originalTagRecord->initials}}</span>
                                                    @endforeach
                                                </td>
                                                <td style="overflow:visible">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                        <a class="dropdown-item" href="{{route('assign-to-me',['caseId'=> $case->id,'stage'=>$stage] )}}">Assign To Me</a>
                                                        <a class="dropdown-item" href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage])}}">View case</a>
                                                        @if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 102)||$permissions->contains('permission_id', 1))))
                                                            <a class="dropdown-item" href="{{route('edit-case-view',$case->id)}}">Edit case</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>


                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>


@endsection

@push('js')
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>


    <script>
        function tabChanged(element) {
            var id = $(element).attr('href');
            setCookie('activeEmpDBTab', id, 356);
        }

        $(document).ready(function() {
            var isMobile = false; //initiate as false
            // device detection
            if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
                isMobile = true;
            }
            if(isMobile)
            $('table.display').DataTable({

                "scrollX": true,
                responsive: false,

            });
            else
                $('table.display').DataTable({
                    "scrollX": false,
                    responsive: true,
                    "pageLength": 25,
                    "searching": false,
                    "lengthChange": false,
                    "columnDefs": [
                        { targets: [0], visible: false},
                    ],
                });
        } );
    </script>
@endpush
