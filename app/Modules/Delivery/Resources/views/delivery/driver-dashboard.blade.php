@extends('layouts.app' ,[ 'pageSlug' => 'Delivery Dashboard' ])

@section('content')
    <style>
        .waitingBadge {background-color: indianred !important;}
        .successBadge {background-color: #28a745 !important; }
        .tab-pane,.col-lg-12,.col-sm-12 {padding:0 !important;}
        td { padding-top: 0 !important; padding-top:0 !important; }
        .btn-secondary { margin-bottom: 0 !important;}
        h5{
          font-size:18px !important;
        }
        @media (max-width: 768px) {
            .idColumn, .dataTables_length {
                display: none;
            }
            .row{padding:0px;}
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
    @php
    if(!isset($_COOKIE['driverEmpDBTab']))
    $_COOKIE['driverEmpDBTab']='#tab1';
    @endphp
    <div class="row ">
        <div class="col-lg-12 col-sm-12">
            <div class="tab-2 m-b-30">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab3' ? ' active show ' : ''  }}" onclick="tabChanged(this)" href="#tab3" data-toggle="tab" aria-expanded="false">Delivered Cases <span class="badge bg-info m-1 successBadge">{{$deliveredCases->count()}}</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab1' ? ' active show ' : ''  }}" onclick="tabChanged(this)" href="#tab1" data-toggle="tab" aria-expanded="false">Active Cases <span class="badge bg-info m-1 activeBadge">{{$activeCases->count()}}</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab2' ? ' active show ' : ''  }}" onclick="tabChanged(this)" href="#tab2" data-toggle="tab" aria-expanded="false">Waiting Cases <span class="badge bg-info m-1 waitingBadge">{{$waitingCases->count()}}</span></a>
                    </li>

                </ul>
                <div class="tab-content bg-white">
                    <div class="tab-pane {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab3' ? ' active show ' : ''  }} p-4" id="tab3">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="card-body color-table">
                                    <div class="card-title border-b mb-4">
                                        <h5 style="color:#28a745"><b>Delivered Cases</b></h5>
                                    </div>
                                    <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th class="idColumn">ID</th>
                                            <th>Doctor</th>
                                            <th>Patient</th>
                                            <th>Delivery Date</th>
                                            <th>Units #</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($deliveredCases as $case)
                                            <tr class="doDropdown">
                                                <td class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                <td><p class="">{{$case->client ? $case->client->name : 'No Client'}}</p></td>
                                                <td><p class="">{{$case->patient_name}}</p></td>
                                                <td><p class="">{{str_replace('T', "     ",$case->initial_delivery_date)}}</p></td>
                                                <td><p class="">{{$case->unitsAmount($stage)}}</p></td>

                                            </tr>

                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab1' ? ' active show ' : ''  }} p-4" id="tab1">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                    <div class="card-body color-table">
                                        <div class="card-title border-b mb-4">
                                            <h5 style="color:steelblue"><b>Active Cases</b></h5>
                                        </div>
                                        <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th class="idColumn">ID</th>
                                                <th>Doctor</th>
                                                <th>Patient</th>
                                                <th>Delivery Date</th>
                                                <th>Units #</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($activeCases as $case)
                                                @php
                                                    $color = $case->delivered_to_client ==1 ? "#49d900" : "#212529";
                                                @endphp
                                                <tr  style="color:{{$color}}">
                                                    <td class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                    <td><p class="">{{$case->client ? $case->client->name : 'No Client'}}</p></td>
                                                    <td><p class="">{{$case->patient_name}}</p></td>
                                                    <td><p class="">{{str_replace('T', "     ",$case->initial_delivery_date)}}</p></td>
                                                    <td><p class="">{{$case->unitsAmount($stage)}}</p></td>
                                                    <td style="overflow:visible">
                                                        <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @if($case->jobs[0]->delivery_accepted == Auth()->user()->id && $case->delivered_to_client !=1)
                                                                <a class="dropdown-item" href="{{route('delivered-in-box',['caseId'=> $case->id] )}}">Complete</a>
                                                            @endif
                                                            <a class="dropdown-item" href="{{route('view-case', ['id' => $case->id,8])}}">View case</a>
                                                        </div>
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
                    <div class="tab-pane {{isset($_COOKIE['driverEmpDBTab']) &&$_COOKIE['driverEmpDBTab'] == '#tab2' ? ' active show ' : ''  }} p-4" id="tab2">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">


                                    <div class="card-body color-table">
                                        <div class="card-title border-b mb-4">
                                            <h5 style="color:indianred"><b>Waiting Cases</b></h5>
                                        </div>
                                        <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border"  style="width:100%">
                                            <thead>
                                            <tr>
                                                <th class="idColumn">ID</th>
                                                <th>Doctor</th>
                                                <th>Patient</th>
                                                <th>Delivery Date</th>
                                                <th>Units #</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($waitingCases as $case)
                                                <tr class="">
                                                    <td class="idColumn"><p class="text-primary">{{$case->id}}</p></td>
                                                    <td><p class="">{{$case->client ? $case->client->name : 'No Client'}}</p></td>
                                                    <td><p class="">{{$case->patient_name}}</p></td>
                                                    <td><p class="">{{str_replace('T', "     ",$case->initial_delivery_date)}}</p></td>
                                                    <td><p class="">{{$case->unitsAmount($stage)}}</p></td>
                                                    <td style="overflow:visible">
                                                        <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        @php
                                                            $permissions = Cache::get('user'.Auth()->user()->id);
                                                        @endphp
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                            <a class="dropdown-item" href="{{route('delivery-accept',$case->id)}}">Take </a>
                                                            <a class="dropdown-item" href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage])}}">View case</a>
                                                        </div>
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

@endsection

@push('js')


    <script>
        function tabChanged(element) {
            var id = $(element).attr('href');
            setCookie('driverEmpDBTab', id, 356);
        }
//        var relX, relY;
//        $('html').mousemove(function(event){
//             relX = event.pageX - $(this).offset().left;
//             relY = event.pageY - $(this).offset().top;
//            var relBoxCoords = "(" + relX + "," + relY + ")";
//            $(".mouse-cords").text(relBoxCoords);
//        });
//        $(".clickable-row").click(function() {
//            //window.location = $(this).data("href");
//            alert('row click');
//        });
//        function setposition(e) {
//            var bodyOffsets = document.body.getBoundingClientRect();
//            tempX = e.pageX - bodyOffsets.left -300;
//            tempY = e.pageY-  bodyOffsets.top -200;
//            console.log(tempX + " " + tempY);
//
//            $(".dropdown-menu").css({ 'top': relY, 'left': relX });
//        }
//
//        $('.doDropdown').click(function( event ) {
//            setposition(event);
//            $('.dropdown-menu').toggle();
//            event.preventDefault();
//            event.stopPropagation();
//            //$(this).dropdown();
//        });

        $(document).ready(function() {
//            var isMobile = false; //initiate as false
            // device detection
//            if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
//                || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
//                isMobile = true;
//            }

        } );
    </script>
<script>
//    $(document).ready(function () {
//        var tables = $('.globalTable');
//        $('#DataTables_Table_0').on( 'draw.dt', function() {
//            $('.dataTables_scrollBody thead tr').addClass('hidden')
//        });
//        if (tables) {
//            tables.DataTable({
//                "pageLength":25,
//                "searching":false,
//                "lengthChange":false,
//                "responsive":false,
//                "scrollX":true,
//
//                "columnDefs": [
//                    {targets: [0], visible: false}
//                ],
//                "initComplete" : function () {
//                    $('.dataTables_scrollBody thead tr').addClass('hidden');
//                }
//            });
//            }
//            tables.addClass("nowrap compact  stripe");
//            }
//    );
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.dataTable').DataTable({
            "pageLength": 25,
            "searching": false,
            "lengthChange": false,
            "order": [[ 4, "desc" ]],
        });
    });
</script>
@endpush
