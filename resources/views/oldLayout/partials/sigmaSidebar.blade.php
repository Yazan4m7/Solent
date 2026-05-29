<head>
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">
    <!-- Theme Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/slidebars.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/menu.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
</head>

<!-- sidebar left start-->
<body >
<div class="sidebar-left" >
    <div class="sidebar-left-info">
@php
    $permissions = Cache::get('user'.Auth()->user()->id);
@endphp

        <div class="user-box">

            <div class="text-center text-white mt-2">
                <h6><b>{{Auth()->user()->first_name . ' ' . Auth()->user()->last_name}}</b></h6>
                <p class="text-center text-white mt-1">{{Auth()->user()->is_admin ? "Administrator" : "Regular User"}}</p>
            </div>
        </div>

        <!--sidebar nav start-->
        <ul class="side-navigation">

            <li>
                <h3 class="navigation-title">Navigation</h3>
            </li>

            @if(($permissions && $permissions->contains('permission_id', 100)) || Auth()->user()->is_admin)
            <li class="{{Route::currentRouteName() == 'new-case-view' ? 'active' : ''}}">
                <a href="{{route('new-case-view')}}"><i class="fa fa-plus-square"></i> <span>Create Case</span></a>
            </li>
            @endif
            <li>
                @if($permissions && $permissions->contains('permission_id', 1))
                <li class="{{Route::currentRouteName() == 'designer-cases-list' ? 'active' : ''}}">
                        <a href="{{route('designer-cases-list',1)}}"><i class="fa fa-suitcase"></i> <span>Designing Cases</span></a>
                </li>
                <li>
                @endif
            @if($permissions && $permissions->contains('permission_id', 2))
                <li class="{{Route::currentRouteName() == 'Miller-cases-list' ? 'active' : ''}}">
                    <a href="{{route('Miller-cases-list',2)}}"><i class="fa fa-suitcase"></i> <span>Milling Cases</span></a>
                </li>
                <li>
                    @endif
            @if($permissions && $permissions->contains('permission_id', 3))
                <li class="{{Route::currentRouteName() == 'Print3D-cases-list' ? 'active' : ''}}">
                    <a href="{{route('Print3D-cases-list',3)}}"><i class="fa fa-suitcase"></i> <span>3D Printing Cases</span></a>
                </li>
                <li>
                    @endif
            @if($permissions && $permissions->contains('permission_id', 4))
                <li class="{{Route::currentRouteName() == 'SinterFurnace-cases-list' ? 'active' : ''}}">
                    <a href="{{route('SinterFurnace-cases-list',4)}}"><i class="fa fa-suitcase"></i> <span>Sintering Cases</span></a>
                </li>
            @endif
                @if($permissions && $permissions->contains('permission_id', 5))
                    <li class="{{Route::currentRouteName() == 'PressFurnace-cases-list' ? 'active' : ''}}">
                        <a href="{{route('PressFurnace-cases-list',5)}}"><i class="fa fa-suitcase"></i> <span>Pressing Furnace Cases</span></a>
                    </li>
            @endif
                    @if($permissions && $permissions->contains('permission_id', 6))
                        <li class="{{Route::currentRouteName() == 'Finishing-cases-list' ? 'active' : ''}}">
                            <a href="{{route('Finishing-cases-list',6)}}"><i class="fa fa-suitcase"></i> <span>Finish & Build Up</span></a>
                        </li>
            @endif
                        @if($permissions && $permissions->contains('permission_id', 7))
                            <li class="{{Route::currentRouteName() == 'QC-cases-list' ? 'active' : ''}}">
                                <a href="{{route('QC-cases-list',7)}}"><i class="fa fa-suitcase"></i> <span>Quality Control</span></a>
                            </li>
            @endif
                            @if($permissions && $permissions->contains('permission_id', 8))
                                <li class="{{Route::currentRouteName() == 'Delivery-cases-list' ? 'active' : ''}}">
                                    <a href="{{route('Delivery-cases-list',8)}}"><i class="fa fa-suitcase"></i> <span>Delivery Cases</span></a>
                                </li>

            @endif


            @if(($permissions && $permissions->contains('permission_id', 103)) || Auth()->user()->is_admin)
                <li class="{{Route::currentRouteName() == 'cases-index' ? 'active' : ''}}">
                    <a href="{{route('cases-index')}}"><i class="fa fa-suitcase"></i>           <span>Cases</span></a>
                </li>
            @endif
            @if(($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                <li ><a href="{{route('invoices-index')}}"><i class="fa fa-money" aria-hidden="true"></i> <span>Invoices</span></a>

            @endif
            @if(($permissions && $permissions->contains('permission_id', 105)) )
                <li ><a href="{{route('receivable-payments-index')}}"><i class="fa fa-money" aria-hidden="true"></i> <span>Collect Payments</span></a>
            @endif
            @if(($permissions && $permissions->contains('permission_id', 106)) || Auth()->user()->is_admin)
                <li ><a href="{{route('admin-dashboard')}}"><i class="fa-solid fa-table-columns"></i> <span>{{config('site_vars.labWorkFlowLabel')}}</span></a>
            @endif
            @if(($permissions && ($permissions->contains('permission_id', 107))) || Auth()->user()->is_admin)
            <li ><a href="{{route('clients-index')}}"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
            @endif

            @if(($permissions && $permissions->contains('permission_id', 111))) || Auth()->user()->is_admin)
                <li ><a href="{{route('clients-index4payment')}}"><i class="fa fa-user-md"></i> <span>Take A Payment</span></a>
                    @endif

            @if(($permissions && $permissions->contains('permission_id', 109)) || Auth()->user()->is_admin)
            <li ><a href="{{route('delivery-schedule')}}"> <i class="fa-regular fa-clock"></i> <span>Delivery Schedule</span></a>
            @endif
            @if(($permissions && $permissions->contains('permission_id', 9)) || Auth()->user()->is_admin)
                <li><a href="{{route('deli-cases-accountant-index')}}"><i class="fa fa-car" aria-hidden="true"></i>Delivery Monitor</a></li>
            @endif
            @if(($permissions && $permissions->contains('permission_id', 113)) || Auth()->user()->is_admin)
                <li><a href="{{route('view-cases-monitor')}}"><i class="fa-solid fa-align-left"></i>Cases Monitor</a></li>
            @endif
            @if(Auth()->user()->is_admin)
                <li>
                <h3 class="navigation-title">Components</h3>

                <li ><a href="{{route('payments-index')}}"><i class="fa fa-credit-card"></i> <span>Payments</span></a>
                <li ><a href="{{route('material-index')}}"><i class="fa fa-cubes"></i> <span>Materials</span></a>
                <li ><a href="{{route('job-type-index')}}"><i class="fa fa-object-group" aria-hidden="true"></i> <span>Job Types</span></a>
                <li ><a href="{{route('users-index')}}"><i class="fa fa-users"></i> <span>Users</span></a>
                <li ><a href="{{route('labs-index')}}"><i class="fa fa-building"></i> <span>External Labs</span></a>
                <li ><a href="{{route('implants-index')}}"><i class="fa-solid fa-tooth"></i> <span>Implants</span></a>
                <li ><a href="{{route('abutments-index')}}"><i class="fa-brands fa-connectdevelop"></i><span>Abutments</span></a>
                <li ><a href="{{route('tags-index')}}"><i class="fa fa-tag"></i><span>Tags</span></a>
                <li ><a href="{{route('f-causes-index')}}"><i class="fa-solid fa-repeat"></i><span>Failure Causes</span></a>
                <li ><a href="{{route('devices-index')}}"><i class="fa-solid fa-tachograph-digital"></i><span>Devices</span></a>

            @endif
        </ul><!--sidebar nav end-->
    </div>
    <div class="sidebarBG" style="">Korvex</div>
</div><!-- sidebar left end-->

</body>

<script src="{{asset('assets/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('assets/js/popper.min.js')}}"></script>

<script src="{{asset('assets/js/jquery-migrate.js')}}"></script>
<script src="{{asset('assets/js/modernizr.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('assets/js/slidebars.min.js')}}"></script>

<!--plugins js-->
<script src="{{asset('assets/plugins/counter/jquery.counterup.min.js')}}"></script>
<script src="{{asset('assets/plugins/waypoints/jquery.waypoints.min.js')}}"></script>
<script src="{{asset('assets/plugins/sparkline-chart/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('assets/pages/jquery.sparkline.init.js')}}"></script>




<!--app js-->
<script src="{{asset('assets/js/jquery.app.js')}}"></script>

