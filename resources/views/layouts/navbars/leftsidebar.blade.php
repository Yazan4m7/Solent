<div class="sidebar">
    <style>
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 250px !important;
            height: 100vh !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border-right: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
        }

        .sidebar::before,
        .sidebar::after {
            display: none !important;
        }

        .sidebar .sidebar-wrapper {
            background: #ffffff !important;
            height: calc(100vh - 84px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-top: 0 !important;
        }

        .korvion-sidebar-brand {
            min-height: 84px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0;
        }

        .korvion-sidebar-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            margin: 0 auto;
            text-decoration: none !important;
        }

        .korvion-sidebar-logo-full {
            display: block;
            width: min(172px, 100%);
            height: auto;
            max-height: 50px;
            object-fit: contain;
        }

        .sidebar .nav li a i,
        .sidebar .nav li svg {
            width: 28px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
            margin-right: 12px !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 20px !important;
        }

        .sidebar .nav li svg.solent-sidebar-icon {
            fill: none;
            height: 22px !important;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }

        .sidebar .nav li > a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1f2a3d !important;
        }

        .sidebar .nav li > a span,
        .sidebar .nav li > a p {
            margin: 0;
            display: inline;
            color: #1f2a3d !important;
        }

        .sidebar .nav li.active > a span,
        .sidebar .nav li.active > a i {
            color: #1f2a3d !important;
        }

        .sidebar .nav li > a,
        .sidebar .nav li > a i,
        .sidebar .nav li > a svg {
            color: #1f2a3d !important;
        }

        .sidebar .nav li.active > a,
        .sidebar .nav li:hover > a {
            background: #f5f7fb !important;
            border-left: 3px solid #33899a !important;
            color: #1f2a3d !important;
        }

        .sidebar hr {
            border-color: #e2e8f0 !important;
        }

        @media screen and (max-width: 991px) {
            .sidebar {
                z-index: 1031 !important;
                transform: translate3d(-260px, 0, 0) !important;
                transition: transform 0.35s ease !important;
            }

            .nav-open .sidebar {
                transform: translate3d(0, 0, 0) !important;
                box-shadow: 0 10px 35px rgba(15, 23, 42, 0.18) !important;
            }

            .sidebar .sidebar-wrapper {
                height: calc(100vh - 84px) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user' . Auth()->user()->id);
        $sidebarBrandName = $brandingName ?? config('branding.defaults.name', 'Solent');
        $sidebarBrandMark = asset($brandingMarkPath ?? config('branding.defaults.mark_path'));
    @endphp
    <div class="korvion-sidebar-brand">
        <a class="korvion-sidebar-logo" href="{{ route('home') }}" aria-label="{{ $sidebarBrandName }} home">
            <img class="korvion-sidebar-logo-full" src="{{ $sidebarBrandMark }}" alt="{{ $sidebarBrandName }} logo">
        </a>
    </div>
    <div class="sidebar-wrapper">

        <ul class="nav">

            @if (($permissions && $permissions->contains('permission_id', 123)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'home' ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        @include('layouts.navbars.partials.sidebar-icon', ['name' => 'home'])
                        <span>Home</span>
                    </a>
                </li>
                <hr style="border-color:#b4b4b4;margin-top: 0.5rem;margin-bottom: 0.5rem;">
            @endif
            @if (($permissions && $permissions->contains('permission_id', 106)) || Auth()->user()->is_admin)
                <div class="" style="padding:0">

                    <li class="{{ Route::currentRouteName() == 'admin-dashboard-v2' ? 'active' : '' }}">
                        <a href="{{ route('admin-dashboard-v2') }}" style=" margin-right: 0px;">
                            @include('layouts.navbars.partials.sidebar-icon', ['name' => 'dashboard'])
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
                    <a href="{{ route('new-case-view') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'plus-square']) <span>Create
                            Case</span></a>
                </li>
            @endif



            @if (($permissions && $permissions->contains('permission_id', 103)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'cases-index' ? 'active' : '' }}">
                    <a href="{{ route('cases-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'case']) <span>Case List</span></a>
                </li>
            @endif

            {{-- COMMENTED OUT: Receive Payments Menu Item --}}
            {{-- @if ($permissions && $permissions->contains('permission_id', 105))
                    <li class="{{Route::currentRouteName() == 'receivable-payments-index' ? 'active' : ''}}" ><a href="{{route('receivable-payments-index')}}">
                            <i class="fa fa-money" aria-hidden="true"></i> <span>Collect Payments</span></a>
                @endif --}}

            @if (($permissions && $permissions->contains('permission_id', 113)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'view-cases-monitor' ? 'active' : '' }}"><a
                        href="{{ route('view-cases-monitor') }}" style="opacity:100%">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'monitor'])<span>Real-time
                            Monitor</span></a></li>
            @endif
            {{-- --}}
            @if (($permissions && $permissions->contains('permission_id', 109)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'delivery-schedule' ? 'active' : '' }}"><a
                        href="{{ route('delivery-schedule') }}"> @include('layouts.navbars.partials.sidebar-icon', ['name' => 'clock'])
                        <span>Deliveries</span></a>
            @endif
            {{-- COMMENTED OUT: Delivery Monitor Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 9)) || Auth()->user()->is_admin)
                    <li class="{{Route::currentRouteName() == 'deli-cases-accountant-index' ? 'active' : ''}}"><a href="{{route('deli-cases-accountant-index')}}"><i class="fa fa-car" aria-hidden="true"></i>Delivery Monitor</a></li>
                @endif --}}
            {{-- COMMENTED OUT: Abutments Delivery Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 125)) || Auth()->user()->is_admin)
                    <li class="{{Route::currentRouteName() == 'abutments-delivery-index' ? 'active' : ''}}"><a href="{{route('abutments-delivery-index')}}"><i class="fa-solid fa-bullseye"></i>Abutments Delivery</a></li>
                @endif --}}
            @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                <li class="{{ Route::currentRouteName() == 'clients-index' ? 'active' : '' }}"><a
                        href="{{ route('clients-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'users']) <span>Clients</span></a>
            @endif
            {{-- COMMENTED OUT: My Collections Menu Item --}}
            {{-- @if (($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                        <li class="{{Route::currentRouteName() == 'my-collections' ? 'active' : ''}}"><a href="{{route('my-collections')}}"> <i class="fa-solid fa-circle-dollar-to-slot"></i> <span>My Collections</span></a>
                    @endif --}}

{{--            @if (($permissions && $permissions->contains('permission_id', 124)) || Auth()->user()->is_admin)--}}
{{--                <li class="{{ Route::currentRouteName() == 'rejected-cases' ? 'active' : '' }}"><a--}}
{{--                        href="{{ route('rejected-cases') }}"><i class="fa fa-times "></i>--}}
{{--                        <span>Rejected & Returns</span></a>--}}
{{--            @endif--}}


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
                        @include('layouts.navbars.partials.sidebar-icon', ['name' => 'chart'])
                        <span class="nav-link-text">Insights & Reports</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse{{ $reportsExpanded == 'true' ? 'show' : '' }}" id="laravel-examples">
                        <ul class="nav pl-4">
                            <li class="{{ Route::currentRouteName() == 'num-of-units-report' ? 'active' : '' }}">
                                <a href="{{ route('num-of-units-report') }}">
                                    @include('layouts.navbars.partials.sidebar-icon', ['name' => 'layers'])
                                    <span>Units Summary</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'job-types-report' ? 'active' : '' }}">
                                <a href="{{ route('job-types-report') }}">
                                    @include('layouts.navbars.partials.sidebar-icon', ['name' => 'flow'])
                                    <span>Job Mix</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'QC-report' ? 'active' : '' }}">
                                <a href="{{ route('QC-report') }}">
                                    @include('layouts.navbars.partials.sidebar-icon', ['name' => 'check'])
                                    <span>QC Summary</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'repeats-report' ? 'active' : '' }}">
                                <a href="{{ route('repeats-report') }}">
                                    @include('layouts.navbars.partials.sidebar-icon', ['name' => 'refresh'])
                                    <span>Remakes</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'materials-report' ? 'active' : '' }}">
                                <a href="{{ route('materials-report') }}">
                                    @include('layouts.navbars.partials.sidebar-icon', ['name' => 'flask'])
                                    <span>Materials Usage</span>
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
                        @include('layouts.navbars.partials.sidebar-icon', ['name' => 'billing']) <span class="nav-link-text">Billing</span>
                        <b class="caret mt-1"></b>
                    </a>
                    <div class="collapse {{ $accountancyExpanded == 'true' ? 'show' : '' }}" id="accountancyList">
                        <ul class="nav pl-4">
                            {{-- COMMENTED OUT: Receive Payments Menu Item (Accountancy) --}}
                            {{-- @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                            <li class="{{Route::currentRouteName() == 'payments-with-collectors' ? 'active' : ''}}" >
                                <a href="{{route('payments-with-collectors')}}"><i class="fa-solid fa-money-bill-transfer"></i> <span>Receive Payments</span></a>

                        @endif --}}
{{--                            @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)--}}
{{--                                <li class="{{ Route::currentRouteName() == 'create-invoice' ? 'active' : '' }}">--}}
{{--                                    <a href="{{ route('create-invoice') }}"><i class="fa-solid fa-file-invoice"></i>--}}
{{--                                        <span>Generate Invoice</span></a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
                            @if (($permissions && $permissions->contains('permission_id', 104)) || Auth()->user()->is_admin)
                                <li class="{{ Route::currentRouteName() == 'invoices-index' ? 'active' : '' }}">
                                    <a href="{{ route('invoices-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'invoice']) <span>Invoice List</span></a>
                            @endif
                            @if (($permissions && $permissions->contains('permission_id', 121)) || Auth()->user()->is_admin)
                                <li class="{{ Route::currentRouteName() == 'payments-index' ? 'active' : '' }}"><a
                                        href="{{ route('payments-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'billing'])
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
                        @include('layouts.navbars.partials.sidebar-icon', ['name' => 'settings'])
                        <span class="nav-link-text">Settings</span>
                        <b class="caret mt-1"></b>
                    </a>

                    <div class="collapse {{ $configExpanded == 'true' ? 'show' : '' }}" id="configList">
                        <ul class="nav pl-4">

                            <li class="{{ Route::currentRouteName() == 'material-index' ? 'active' : '' }}"><a
                                    href="{{ route('material-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'boxes'])
                                    <span>Material list</span></a>
                            <li class="{{ Route::currentRouteName() == 'job-type-index' ? 'active' : '' }}"><a
                                    href="{{ route('job-type-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'flow']) <span>Job types list</span></a>
                            <li class="{{ Route::currentRouteName() == 'users-index' ? 'active' : '' }}"><a
                                    href="{{ route('users-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'users'])
                                    <span>Team Members</span></a>
                                {{-- COMMENTED OUT: External Labs Menu Item --}}
                                {{-- <li class="{{Route::currentRouteName() == 'labs-index' ? 'active' : ''}}"><a href="{{route('labs-index')}}"><i class="fa fa-building"></i> <span>External Labs</span></a> --}}
                            <li class="{{ Route::currentRouteName() == 'implants-index' ? 'active' : '' }}"><a
                                    href="{{ route('implants-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'tooth'])
                                    <span>Implant list</span></a>
                                {{-- COMMENTED OUT: Abutments Menu Item --}}
                                {{-- <li class="{{Route::currentRouteName() == 'abutments-index' ? 'active' : ''}}"><a href="{{route('abutments-index')}}"><i class="fa-brands fa-connectdevelop"></i><span>Abutments</span></a> --}}
                            <li class="{{ Route::currentRouteName() == 'tags-index' ? 'active' : '' }}"><a
                                    href="{{ route('tags-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'tag'])<span>Tags</span></a>
                            <li class="{{ Route::currentRouteName() == 'f-causes-index' ? 'active' : '' }}"><a
                                    href="{{ route('f-causes-index') }}">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'refresh'])<span>Failure Reasons</span></a>
                                {{-- <li class="{{Route::currentRouteName() == 'sys-config' ? 'active' : ''}}"><a href="{{route('sys-config')}}"><i class="fa-solid fa-screwdriver-wrench"></i><span>System Settings</span></a> --}}

                        </ul>
                    </div>
                </li>
            @endif

        </ul>
    </div>
</div>
