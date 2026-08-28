<div class="sidebar">
    <style>
        body.white-content .wrapper > .sidebar,
        .wrapper > .sidebar,
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 250px !important;
            height: 100vh !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            background: var(--color-sidebar) !important;
            box-shadow: none !important;
            border-right: 1px solid var(--sidebar-border) !important;
            border-radius: 0 !important;
        }

        body.white-content .wrapper > .sidebar::before,
        body.white-content .wrapper > .sidebar::after,
        .wrapper > .sidebar::before,
        .wrapper > .sidebar::after,
        .sidebar::before,
        .sidebar::after {
            display: none !important;
        }

        body.white-content .wrapper > .sidebar .sidebar-wrapper,
        .wrapper > .sidebar .sidebar-wrapper,
        .sidebar .sidebar-wrapper {
            background: var(--color-sidebar) !important;
            height: calc(100vh - 84px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-top: 0 !important;
        }

        body.white-content .wrapper > .sidebar .korvion-sidebar-brand,
        .wrapper > .sidebar .korvion-sidebar-brand,
        .korvion-sidebar-brand {
            min-height: 84px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-sidebar) !important;
            border-bottom: 1px solid var(--sidebar-brand-border);
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
            filter: none !important;
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

        body.white-content .wrapper > .sidebar .nav li > a,
        .wrapper > .sidebar .nav li > a,
        .sidebar .nav li > a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--sidebar-link) !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }

        body.white-content .wrapper > .sidebar .nav li > a span,
        body.white-content .wrapper > .sidebar .nav li > a p,
        .wrapper > .sidebar .nav li > a span,
        .wrapper > .sidebar .nav li > a p,
        .sidebar .nav li > a span,
        .sidebar .nav li > a p {
            margin: 0;
            display: inline;
            color: var(--sidebar-link) !important;
            font-weight: 400;
            transition: color 0.15s ease;
        }

        body.white-content .wrapper > .sidebar .nav li > a,
        body.white-content .wrapper > .sidebar .nav li > a i,
        body.white-content .wrapper > .sidebar .nav li > a svg,
        .wrapper > .sidebar .nav li > a,
        .wrapper > .sidebar .nav li > a i,
        .wrapper > .sidebar .nav li > a svg,
        .sidebar .nav li > a,
        .sidebar .nav li > a i,
        .sidebar .nav li > a svg {
            color: var(--sidebar-link) !important;
            transition: color 0.15s ease;
        }

        .sidebar .nav li.active > a,
        .sidebar .nav li.active > a:hover,
        .sidebar .nav li.active > a:focus {
            background: var(--sidebar-active-bg) !important;
            border-left: 3px solid var(--accent) !important;
            color: var(--surface) !important;
            font-weight: 500 !important;
        }

        .sidebar .nav li.active > a span,
        .sidebar .nav li.active > a p {
            color: var(--surface) !important;
            font-weight: 500 !important;
        }

        .sidebar .nav li.active > a i,
        .sidebar .nav li.active > a svg {
            color: var(--accent-lt) !important;
            font-weight: 500 !important;
        }

        .sidebar .nav li:not(.active):hover > a {
            background: var(--sidebar-hover) !important;
            color: var(--surface) !important;
        }

        .sidebar hr {
            border-color: var(--sidebar-border) !important;
        }

        @media screen and (max-width: 991px) {
            .sidebar {
                z-index: 1031 !important;
                transform: translate3d(-260px, 0, 0) !important;
                transition: transform 0.35s ease !important;
            }

            .nav-open .sidebar {
                transform: translate3d(0, 0, 0) !important;
                box-shadow: 0 10px 35px var(--shadow-1) !important;
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
        $sidebarBrandMark = asset('images/brands/solent/solent_h_white.svg');
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
                <hr style="border-color:var(--sidebar-border);margin-top: 0.5rem;margin-bottom: 0.5rem;">
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

                <hr style="border-color:var(--sidebar-border);margin-top: 0.5rem;margin-bottom: 0.5rem;">
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
