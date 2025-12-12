<div class="sidebar">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,200,1,0" />
    <style>
        .sidebar .nav li a i,
        .sidebar .nav li svg {
            width: 28px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
            margin-right: 12px !important;

            align-items: center !important;
            justify-content: center !important;
            font-size: 20px !important;
    </style>
    @php
        $permissions = Cache::get('user' . Auth()->user()->id);
    @endphp
    <div class="sidebar-wrapper">

        <ul class="nav">

            @if (($permissions && $permissions->contains('permission_id', 123)) || Auth()->user()->is_admin)
                <div class="homePageOptionInSideBar" style="padding:0;">

                    <li class="{{ Route::currentRouteName() == 'home' ? 'active' : '' }}">
                        <a style="
                                    margin-right: 0px;
                                    padding-right: 0px !important;
                                     padding-left: 0px !important;
                                "
                            href="{{ route('home') }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Home Screen</span>
                        </a>
                        <hr style="border-color:#b4b4b4;margin-top: 0.5rem;margin-bottom: 0.5rem;">
                </div>
            @endif
            @if (($permissions && $permissions->contains('permission_id', 106)) || Auth()->user()->is_admin)
                <div class="" style="padding:0">

                    <li class="{{ Route::currentRouteName() == 'admin-dashboard-v2' ? 'active' : '' }}">
                        <a href="{{ route('admin-dashboard-v2') }}" style=" margin-right: 0px;">
                            <span class="material-symbols-outlined googleIconInSideBar">
                                dashboard
                            </span>
                            <span>OPERATIONS DASHBOARD</span>
                        </a>


                        {{-- <li  class="{{Route::currentRouteName() == 'admin-dashboard' ? 'active' : ''}}"> --}}
                        {{-- <a href="{{route('admin-dashboard')}}" > <i class="tim-icons icon-chart-pie-36"></i><span>{{ _('Dashboard') }}</span></a> --}}
                        {{-- </li> --}}
                </div>

                <hr style="border-color:#b4b4b4;margin-top: 0.5rem;margin-bottom: 0.5rem;">
            @endif
            @if (($permissions && $permissions->contains('permission_id', 100)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'new-case-view' ? 'active' : '' }}">
                    <a href="{{ route('new-case-view') }}"><i class="fa fa-plus-square"></i> <span>Create
                            Case</span></a>
                </li>
            @endif



            @if (($permissions && $permissions->contains('permission_id', 103)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'cases-index' ? 'active' : '' }}">
                    <a href="{{ route('cases-index') }}"><i class="fa fa-suitcase"></i> <span>Cases</span></a>
                </li>
            @endif

            {{-- COMMENTED OUT: Receive Payments Menu Item --}}
            {{-- @if ($permissions && $permissions->contains('permission_id', 105))
                    <li class="{{Route::currentRouteName() == 'receivable-payments-index' ? 'active' : ''}}" ><a href="{{route('receivable-payments-index')}}">
                            <i class="fa fa-money" aria-hidden="true"></i> <span>Collect Payments</span></a>
                @endif --}}

            @if (($permissions && $permissions->contains('permission_id', 109)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'delivery-schedule' ? 'active' : '' }}"><a
                        href="{{ route('delivery-schedule') }}"> <i class="fa-regular fa-clock"></i> <span>Delivery
                            Schedule</span></a>
            @endif
            {{-- COMMENTED OUT: Delivery Monitor Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 9)) || Auth()->user()->is_admin)
                    <li class="{{Route::currentRouteName() == 'deli-cases-accountant-index' ? 'active' : ''}}"><a href="{{route('deli-cases-accountant-index')}}"><i class="fa fa-car" aria-hidden="true"></i>Delivery Monitor</a></li>
                @endif --}}
            {{-- COMMENTED OUT: Abutments Delivery Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 125)) || Auth()->user()->is_admin)
                    <li class="{{Route::currentRouteName() == 'abutments-delivery-index' ? 'active' : ''}}"><a href="{{route('abutments-delivery-index')}}"><i class="fa-solid fa-bullseye"></i>Abutments Delivery</a></li>
                @endif --}}
            @if (($permissions && $permissions->contains('permission_id', 113)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'view-cases-monitor' ? 'active' : '' }}"><a
                        href="{{ route('view-cases-monitor') }}" style="opacity:100%"><i
                            class="fa-solid fa-table-cells-large" style="opacity:100%"></i>Cases Monitor</a></li>
            @endif
            {{-- --}}
            @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'clients-index' ? 'active' : '' }}"><a
                        href="{{ route('clients-index') }}"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
            @endif
            {{-- COMMENTED OUT: My Collections Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                        <li class="{{Route::currentRouteName() == 'my-collections' ? 'active' : ''}}"><a href="{{route('my-collections')}}"> <i class="fa-solid fa-circle-dollar-to-slot"></i> <span>My Collections</span></a>
                    @endif --}}

            @if (($permissions && $permissions->contains('permission_id', 124)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'rejected-cases' ? 'active' : '' }}"><a
                        href="{{ route('rejected-cases') }}"><i class="fa fa-times "></i> <span>Rejected
                            Cases</span></a>
            @endif


            @if (($permissions && $permissions->contains('permission_id', 120)) || Auth()->user()->is_admin)
                @php
                    $reportsExpanded = in_array(Route::currentRouteName(), [
                        'num-of-units-report',
                        'job-types-report',
                        'QC-report',
                        'repeats-report',
                        'implants-report',
                    ])
                        ? 'true'
                        : 'false';
                @endphp

                <li>
                    <a data-toggle="collapse" href="#laravel-examples" aria-expanded="{{ $reportsExpanded }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-link-text">{{ __('Reports') }}</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse{{ $reportsExpanded == 'true' ? 'show' : '' }}" id="laravel-examples">
                        <ul class="nav pl-4">
                            <li class="{{ Route::currentRouteName() == 'num-of-units-report' ? 'active' : '' }}">
                                <a href="{{ route('num-of-units-report') }}">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <p>{{ 'Number Of Units' }}</p>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'job-types-report' ? 'active' : '' }}">
                                <a href="{{ route('job-types-report') }}">
                                    <i class="fa-solid fa-sitemap"></i>
                                    <p>{{ 'Job Types' }}</p>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'QC-report' ? 'active' : '' }}">
                                <a href="{{ route('QC-report') }}">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <p>{{ __('QC') }}</p>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'repeats-report' ? 'active' : '' }}">
                                <a href="{{ route('repeats-report') }}">
                                    <i class="fa-solid fa-rotate-right"></i>
                                    <p>{{ 'Repeats' }}</p>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'materials-report' ? 'active' : '' }}">
                                <a href="{{ route('materials-report') }}">
                                    <i class="fa-solid fa-flask"></i>
                                    <p>Materials Report</p>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
            @endif
            @if (
                ($permissions && $permissions->contains('permission_id', 104)) ||
                    ($permissions && $permissions->contains('permission_id', 121)) ||
                    ($permissions && $permissions->contains('permission_id', 111)) ||
                    Auth()->user()->is_admin)
                @php
                    $accountancyExpanded = in_array(Route::currentRouteName(), [
                        'invoices-index',
                        'payments-index',
                        'clients-index4payment',
                        'payments-with-collectors',
                        'create-invoice',
                    ]);
                @endphp
                <li>
                    <a data-toggle="collapse" href="#accountancyList" aria-expanded="{{ $accountancyExpanded }}">
                        <i class="fa-solid fa-dollar-sign"></i> <span class="nav-link-text">Accountancy</span>
                        <b class="caret mt-1"></b>
                    </a>
                    <div class="collapse {{ $accountancyExpanded == 'true' ? 'show' : '' }}" id="accountancyList">
                        <ul class="nav pl-4">
                            {{-- COMMENTED OUT: Receive Payments Menu Item (Accountancy) --}}
                            {{-- @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                            <li class="{{Route::currentRouteName() == 'payments-with-collectors' ? 'active' : ''}}" >
                                <a href="{{route('payments-with-collectors')}}"><i class="fa-solid fa-money-bill-transfer"></i> <span>Receive Payments</span></a>

                        @endif --}}
                            @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                                <li class="{{ Route::currentRouteName() == 'create-invoice' ? 'active' : '' }}">
                                    <a href="{{ route('create-invoice') }}"><i class="fa-solid fa-file-invoice"></i> <span>Create Invoice</span></a>
                                </li>
                            @endif
                            @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                                <li class="{{ Route::currentRouteName() == 'invoices-index' ? 'active' : '' }}">
                                    <a href="{{ route('invoices-index') }}"><i
                                            class="fa-solid fa-file-invoice-dollar"></i> <span>Invoices</span></a>
                            @endif
                            @if (($permissions && $permissions->contains('permission_id', 121)) || Auth()->user()->is_admin)
                                <li class="{{ Route::currentRouteName() == 'payments-index' ? 'active' : '' }}"><a
                                        href="{{ route('payments-index') }}"><i class="fa fa-credit-card"></i>
                                        <span>Payments</span></a>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            @if (Auth()->user()->is_admin)
                @php
                    $configExpanded = in_array(Route::currentRouteName(), [
                        'material-index',
                        'job-type-index',
                        'users-index',
                        // 'labs-index', // COMMENTED OUT
                        'implants-index',
                        // 'abutments-index', // COMMENTED OUT
                        'tags-index',
                        'f-causes-index',
                        // 'devices-index', // REMOVED
                        'sys-config',
                        'media-index',
                    ])
                        ? 'true'
                        : 'false';
                @endphp

                <li>
                    <a data-toggle="collapse" href="#configList" aria-expanded="{{ $configExpanded }}">
                        <i class="fa-solid fa-gear"></i>
                        <span class="nav-link-text">Configuration</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse {{ $configExpanded == 'true' ? 'show' : '' }}" id="configList">
                        <ul class="nav pl-4">

                            <li class="{{ Route::currentRouteName() == 'material-index' ? 'active' : '' }}"><a
                                    href="{{ route('material-index') }}"><i class="fa fa-cubes"></i>
                                    <span>Materials</span></a>
                            <li class="{{ Route::currentRouteName() == 'job-type-index' ? 'active' : '' }}"><a
                                    href="{{ route('job-type-index') }}"><i class="fa fa-object-group"
                                        aria-hidden="true"></i> <span>Job Types</span></a>
                            <li class="{{ Route::currentRouteName() == 'users-index' ? 'active' : '' }}"><a
                                    href="{{ route('users-index') }}"><i class="fa fa-users"></i>
                                    <span>Users</span></a>
                                {{-- COMMENTED OUT: External Labs Menu Item --}}
                                {{-- <li class="{{Route::currentRouteName() == 'labs-index' ? 'active' : ''}}"><a href="{{route('labs-index')}}"><i class="fa fa-building"></i> <span>External Labs</span></a> --}}
                            <li class="{{ Route::currentRouteName() == 'implants-index' ? 'active' : '' }}"><a
                                    href="{{ route('implants-index') }}"><i class="fa-solid fa-tooth"></i>
                                    <span>Implants</span></a>
                                {{-- COMMENTED OUT: Abutments Menu Item --}}
                                {{-- <li class="{{Route::currentRouteName() == 'abutments-index' ? 'active' : ''}}"><a href="{{route('abutments-index')}}"><i class="fa-brands fa-connectdevelop"></i><span>Abutments</span></a> --}}
                            <li class="{{ Route::currentRouteName() == 'tags-index' ? 'active' : '' }}"><a
                                    href="{{ route('tags-index') }}"><i class="fa fa-tag"></i><span>Tags</span></a>
                            <li class="{{ Route::currentRouteName() == 'f-causes-index' ? 'active' : '' }}"><a
                                    href="{{ route('f-causes-index') }}"><i
                                        class="fa-solid fa-repeat"></i><span>Failure Causes</span></a>
                                {{-- <li class="{{Route::currentRouteName() == 'sys-config' ? 'active' : ''}}"><a href="{{route('sys-config')}}"><i class="fa-solid fa-screwdriver-wrench"></i><span>System Settings</span></a> --}}

                        </ul>
                    </div>
                </li>
            @endif

        </ul>
    </div>
</div>
