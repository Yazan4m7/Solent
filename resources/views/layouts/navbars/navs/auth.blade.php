<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css"
    integrity="sha512-siarrzI1u3pCqFG2LEzi87McrBmq6Tp7juVsdmGY1Dr8Saw+ZBAzDzrGwX3vgxX1NkioYNCFOVC0GpDPss10zQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @import url(https://fonts.googleapis.com/css?family=Lato:100,300,400,700);

    .navbar-wrapper {
        display: none;
    }

    .main-panel {
        margin-left: 250px !important;
        width: calc(100% - 250px) !important;
        min-height: 100vh;
        background: #f5f6fa !important;
        border-top: 0 !important;
    }

    body.white-content,
    body.white-content .main-panel,
    body.white-content .main-panel > .content {
        background: #f5f6fa !important;
    }

    .navbar.stickMe,
    .navbar.navbar-transparent.stickMe,
    .stickMe {
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;
        background: #ffffff !important;
        padding: 0 !important;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: none;
        z-index: 1000;
    }

    .stickMe > .container-fluid {
        display: flex;
        padding: 0 !important;
    }

    .headerRow {
        display: flex;
        width: 100%;
        min-height: 84px;
        margin: 0 !important;
        padding: 0;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        background-color: transparent;
    }

    .headerTitleCol {
        flex: 1 1 auto !important;
        max-width: none !important;
        min-width: 0;
    }

    .left-toggler-container {
        display: flex;
        align-items: center !important;
        flex-wrap: nowrap !important;
        min-height: 84px;
        margin: 0 !important;
        padding: 0 0 0 24px;
        background-color: transparent;
    }

    .logo-col {
        display: none;
    }

    .logo-navbar {
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .logo-navbar .logo,
    .logo-navbar .korvion-horizontal-logo {
        width: 26px !important;
        height: 26px !important;
        max-height: 26px !important;
        object-fit: contain !important;
        filter: none !important;
        position: static !important;
    }

    .pageTitleContainer {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex: 1 1 auto !important;
        max-width: none !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        border-image: none !important;
        border-radius: 0 !important;
    }

    .pageTitleAccent {
        display: inline-block;
        width: 4px;
        height: 30px;
        flex: 0 0 4px;
        border-radius: 6px;
        background: linear-gradient(180deg, #2c6068 0%, #265057 100%);
    }

    .pageTitle {
        display: inline-flex !important;
        align-items: center !important;
        min-width: 0;
        margin: 0 !important;
        padding: 0;
        width: auto !important;
        color: #1f2a3d !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase;
        line-height: 1.1 !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .navbar.stickMe .pageTitle,
    .navbar.stickMe .navbar-brand.pageTitle {
        color: #1f2a3d !important;
    }

    .headerSearchCol {
        display: flex;
        align-items: center;
        flex: 0 1 346px;
        width: 346px;
        justify-content: flex-end;
        max-width: 346px;
        padding-right: 0 !important;
    }

    .headerSearchCol form {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        margin: 0;
    }

    #wrapp {
        margin: 0;
        display: flex;
        align-items: center;
        width: min(336px, 100%);
        height: 44px;
        padding: 0;
        position: relative;
    }

    #wrapp .SBF2 {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        height: 44px;
        padding: 0 16px;
        border: 1px solid rgba(17, 21, 30, 0.12);
        border-radius: 999px;
        background: #ffffff;
        box-sizing: border-box;
    }

    #wrapp input[type="text"] {
        flex: 1 1 auto;
        min-width: 0;
        height: 100%;
        font-size: 14px;
        line-height: 1;
        display: block;
        font-family: "Lato";
        border: none;
        outline: none;
        color: #334155;
        padding: 0;
        width: auto;
        position: static;
        background: none;
        cursor: text;
    }

    #wrapp input[type="text"]::placeholder {
        color: #6b7280;
    }

    #wrapp #search_submit {
        order: -1;
        flex: 0 0 16px;
        height: 16px;
        width: 16px;
        display: inline-flex;
        background: url("data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.75 18.5C6.47 18.5 3 15.03 3 10.75S6.47 3 10.75 3s7.75 3.47 7.75 7.75a7.72 7.72 0 0 1-1.68 4.82l3.31 3.31-1.25 1.25-3.31-3.31a7.72 7.72 0 0 1-4.82 1.68Zm0-1.8a5.95 5.95 0 1 0 0-11.9 5.95 5.95 0 0 0 0 11.9Z' fill='%23111718'/%3E%3C/svg%3E") center center / 16px auto no-repeat;
        text-indent: -10000px;
        border: none;
        position: static;
        pointer-events: none;
        opacity: 0.6;
        transition: opacity .4s ease;
    }

    .headerActionsCol {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
        gap: 24px;
        flex: 0 0 auto;
        margin-left: auto;
        padding-right: 24px;
    }

    .dotsDiv {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto !important;
        max-width: none !important;
        min-width: 180px;
        height: 84px;
        padding-right: 24px !important;
    }

    .solent-layout-profile {
        display: inline-flex !important;
        align-items: center;
        gap: 12px;
        min-width: 220px;
        margin: 0;
        padding: 4px 8px 4px 4px !important;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: #111827 !important;
        text-decoration: none;
        box-shadow: none;
    }

    .solent-layout-profile:hover,
    .solent-layout-profile:focus {
        color: #111827 !important;
        text-decoration: none;
        background: rgba(17, 24, 39, 0.04);
    }

    .solent-layout-profile::after {
        display: none;
    }

    .solent-layout-profile-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        flex: 0 0 50px;
        border-radius: 999px;
        background: linear-gradient(135deg, #6868e8 0%, #18aab2 100%);
        color: #ffffff;
        font-size: 16px;
        font-weight: 800;
        line-height: 1;
        text-transform: uppercase;
    }

    .solent-layout-profile-meta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        height: 50px;
        text-align: left;
        line-height: 1.1;
    }

    .solent-layout-profile-name,
    .solent-layout-profile-role {
        display: block;
        max-width: 142px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .solent-layout-profile-name {
        color: #111827;
        font-size: 14px;
        font-weight: 800;
    }

    .solent-layout-profile-role {
        margin-top: 3px;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
    }

    .solent-layout-profile-caret {
        margin-left: 4px;
        color: #667085;
        font-size: 12px;
    }

    .solent-layout-profile-shell {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .solent-layout-profile-shell .navbar-nav {
        display: flex;
        align-items: center;
        margin: 0 !important;
    }

    .main-panel > .content {
        min-height: calc(100vh - 84px);
        padding-top: 28px !important;
        background: #f5f6fa !important;
    }

    @media screen and (max-width: 991.98px) {
        .main-panel {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .headerRow {
            min-height: auto;
            flex-wrap: wrap !important;
            gap: 8px 10px;
        }

        .headerTitleCol {
            max-width: 100% !important;
            flex: 1 1 auto !important;
        }

        .headerActionsCol {
            flex: 0 0 auto;
            gap: 12px;
            padding-right: 12px;
        }

        .headerSearchCol {
            flex: 0 1 260px;
            width: 260px;
            max-width: 260px !important;
            justify-content: flex-end;
            padding: 0 !important;
        }

        .left-toggler-container {
            min-height: 64px;
            padding-left: 12px;
            gap: 10px;
        }

        .logo-col {
            display: flex;
            flex: 0 0 42px;
            max-width: 42px;
            align-items: center;
            justify-content: center;
        }

        .logo-navbar {
            display: inline-flex !important;
            width: 32px !important;
            height: 32px !important;
        }

        .pageTitleContainer {
            max-width: calc(100% - 52px) !important;
        }

        .headerSearchCol form,
        #wrapp {
            width: 100% !important;
        }

        .dotsDiv {
            display: flex !important;
            height: 64px;
            min-width: 0;
            padding-right: 0 !important;
        }

        .solent-layout-profile {
            min-width: 180px;
        }
    }

    @media screen and (max-width: 450px) {
        .pageTitleContainer {
            display: none !important;
        }

        .headerRow {
            align-items: flex-start !important;
        }

        .headerActionsCol {
            flex: 1 1 100%;
            width: 100%;
            justify-content: space-between;
            padding: 0 12px 12px;
        }

        .headerSearchCol {
            flex: 1 1 auto;
            width: auto;
            max-width: none !important;
        }

        .solent-layout-profile {
            min-width: 52px;
            padding-right: 4px !important;
        }

        .solent-layout-profile-meta,
        .solent-layout-profile-caret {
            display: none;
        }
    }
</style>

@php
    $permissions = Cache::get('user' . Auth()->user()->id);
    $headerBrandIcon = asset($brandingFaviconPath ?? config('branding.defaults.favicon_path'));
    $authUser = Auth()->user();
    $profileName = trim(implode(' ', array_filter([
        $authUser->first_name ?? null,
        $authUser->last_name ?? null,
    ])));
    $profileName = $profileName !== '' ? $profileName : ($authUser->name ?? $authUser->email ?? __('User'));
    $profileInitials = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $profileName), 0, 2));
    $profileInitials = $profileInitials !== '' ? $profileInitials : 'U';
    $profileRole = ($authUser->is_admin ?? false) ? __('Administrator') : __('Regular User');
@endphp
<nav class="navbar navbar-expand-lg  navbar-transparent stickMe">
    <div class="container-fluid noPadOnMobile .stickMe" style="display:flex;">
        <div class="row headerRow" style="display:flex;">

            <!-- Logo and title -->
            <div class="col-lg-7 col-md-7 noPadOnMobile headerTitleCol">
                <div class="container-fluid noPadOnMobile" style="padding-left: 0;">
                    <div class="row left-toggler-container">

                        <!-- Logo and mobile bars -->
                        <div class="logo-col noPadOnMobile">
                            <div class="d-flex align-items-center">
                                <div class="navbar-toggle d-inline">
                                    <button type="button" class="navbar-toggler ">
                                        <span class="navbar-toggler-bar bar1"></span>
                                        <span class="navbar-toggler-bar bar2"></span>
                                        <span class="navbar-toggler-bar bar3"></span>
                                    </button>
                                </div>
                                <a class="navbar-brand logo-navbar" href="{{ route('home') }}">
                                    <img class="logo korvion-horizontal-logo"
                                        src="{{ $headerBrandIcon }}"
                                        alt="{{ $brandingName ?? 'Brand' }}" />
                                </a>
                            </div>
                        </div>

                        <!--Page Title -->
                        {{-- page title --}}
                        @if (!isset($pageSlug) || strtolower($pageSlug) !== 'home')
                            <div class="col-sm-9 col-lg-6 noPadOnMobile pageTitleContainer">
                                <span class="pageTitleAccent"></span>
                                <span class="navbar-brand pageTitle"
                                    style="width: 100%;">{{ $pageSlug ?? ($brandingName ?? 'Dashboard') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="headerActionsCol noPadOnMobile">
                <div class="headerSearchCol noPadOnMobile">
                    <form action="{{ route('global-search') }}" method="GET">
                        <div id="wrapp" class="searchBox2">
                            <div class="SBF2">
                                <input id="search" name="searchText" type="text" placeholder="Patient Name?">
                                <span id="search_submit"></span>
                            </div>
                        </div>
                    </form>
                </div>
                {{--            <x-weather-widget></x-weather-widget> --}}
                <div class="mb-1 noPadOnMobile dotsDiv">
                    <div class="solent-layout-profile-shell" id="navigation">
                        <ul class="navbar-nav ml-auto">

                            <li class="dropdown nav-item">
                                <a href="#" class="dropdown-toggle nav-link solent-layout-profile" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="solent-layout-profile-avatar" aria-hidden="true">{{ $profileInitials }}</span>
                                    <span class="solent-layout-profile-meta">
                                        <span class="solent-layout-profile-name">{{ $profileName }}</span>
                                        <span class="solent-layout-profile-role">{{ $profileRole }}</span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down solent-layout-profile-caret" aria-hidden="true"></i>
                                    <p class="d-lg-none"></p>
                                </a>
                                <ul class="dropdown-menu dropdown-navbar">
                                    {{-- <li class="nav-link"> --}}
                                    {{-- <a href="{{ route('profile.edit') }}" class="nav-item dropdown-item">{{ __('Profile') }}</a> --}}
                                    {{-- </li> --}}
                                    {{-- <li class="nav-link"> --}}
                                    {{-- <a href="#" class="nav-item dropdown-item">{{ __('Settings') }}</a> --}}
                                    {{-- </li> --}}
                                    {{-- <li class="dropdown-divider"></li> --}}
                                    <li class="nav-link">
                                        <a href="{{ route('logout') }}" class="nav-item dropdown-item"
                                            onclick="event.preventDefault();  document.getElementById('logout-form').submit();">{{ __('Log out') }}</a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</nav>
<script>
    document.onkeydown = function(evt) {
        var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
        if (keyCode == 13) {
            //your function call here
            document.searchFrom.submit();
        }
    }
</script>
