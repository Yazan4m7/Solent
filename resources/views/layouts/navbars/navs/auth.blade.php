<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css"
    integrity="sha512-siarrzI1u3pCqFG2LEzi87McrBmq6Tp7juVsdmGY1Dr8Saw+ZBAzDzrGwX3vgxX1NkioYNCFOVC0GpDPss10zQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @import url(https://fonts.googleapis.com/css?family=Lato:100,300,400,700);

    @media screen and (min-width: 999px) {
        .logo-col {
            flex: 0 0 223px;
            max-width: 223px;
            padding-left: 0;
        }

        .logo {
            width: 180px;
        }

    }

    /*@media (max-width: 992px) {*/
    /*#wrapp  #search{*/
    /*top:3px;*/
    /*}*/
    /*}*/
    /*@media screen and (max-width: 770px) {*/
    /*.searchBox2{*/
    /*display:none;*/
    /*}*/
    /*}*/

    .navbar-wrapper {
        display: none;
    }

    .headerRow {
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        background-color: transparent;
        padding: 0;

    }

    /** SEARCH BOXES  **/
    :root {
        --tab-color-white: #f9f9f9;
        --tab-color-black: #004345;
        --tab-color-cadet: #2b7b7d;
        --tab-color-fighter: #315f5f;
        --tab-color-space: #383961;
        --tab-color-gray: #d7d9d7;
        --tab-color-english: #2b7b7d;
        /*
         --------
         * CSS Vars
         --------
         */
        --tab-bg-color: var(--tab-color-cadet);
        --tab-text-color: var(--tab-color-gray);
        --tab-border-color: var(--tab-color-space);
        --tab-active-bg-color: var(--tab-color-white);
        --tab-active-text-color: var(--tab-color-black);
        --tab-active-border-color: var(--tab-color-english);
        --tab-focus-bg-color: var(--tab-color-fighter);
        --tab-focus-text-color: var(--tab-color-white);
        --tab-focus-text-secondary-color: var(--tab-color-english);
        --tab-focus-border-color: var(--tab-color-fighter);
        --color-light: white;
        --color-dark: #212121;
        --color-signal: #37b44a;
        --color-background: #f5f6fa;
        --color-text: var(--color-dark);
        --color-accent: var(--color-signal);
        --size-bezel: 0.2rem;
        --size-radius: 5px;
        --global-heading-font-family: "Lato", sans-serif;

    }

    .outerSBLabel {
        position: relative;
        margin-bottom: 0 !important;
    }

    .outerSBLabel:focus-visible,
    searchBox:focus-visible,
    .searchBox,
    .outerSBLabel {
        outline: none;
    }

    input:-internal-autofill-selected {
        background-color: transparent !important;
    }

    /**SEACRH BOX 2 **/




    body {
        /*background: #DDD;*/
        /*font-size: 15px;*/
    }

    #wrapp {
        margin-bottom: 60px;
        display: inline-block;
        position: relative;
        /*height: 80px;*/
        float: right;
        padding: 0;
        position: relative;
    }

    #wrapp input[type="text"] {
        height: 35px;
        font-size: 15px;
        display: inline-block;
        font-family: "Lato";
        border: none;
        outline: none;
        color: #555;
        padding: 3px;
        padding-right: 60px;
        width: 0px;
        position: absolute;
        top: 3px;
        right: -74px;
        background: none;
        z-index: 1;
        transition: width .4s cubic-bezier(0.000, 0.795, 0.000, 1.000);
        cursor: pointer;
    }

    #wrapp input[type="text"]:focus:hover {
        border-bottom: 1px solid #BBB;
    }

    #wrapp input[type="text"]:focus {
        width: 200px;
        z-index: 1;
        border-bottom: 1px solid #BBB;
        cursor: text;
    }

    #wrapp #search_submit {
        height: 47px;
        width: 63px;
        display: inline-block;
        color: red;
        float: right;
        background: url({{ asset('assets') }}/5613.png) center center no-repeat;
        text-indent: -10000px;
        border: none;
        position: absolute;
        top: 0;
        right: -90px;
        z-index: 2;
        pointer-events: none;
        cursor: pointer;
        opacity: 0.6;
        cursor: pointer;
        transition: opacity .4s ease;
        background-size: 50%;
    }

    #wrapp #search_submit:hover {
        opacity: 0.8;
    }



    /** END SEARCH BOX 2 **/

    .searchBox_label {
        position: absolute;
        left: 5px;
        top: 0;
        padding: calc(var(--size-bezel) * 0.75) calc(var(--size-bezel) * 0.5);
        margin: calc(var(--size-bezel) * 0.75 + 3px) calc(var(--size-bezel) * 0.5);
        background: pink;
        white-space: nowrap;
        transform: translate(0, 0);
        transform-origin: 0 0;
        background: var(--color-background);
        transition: transform 120ms ease-in;
        color: #1d253b80;
        line-height: 1.2;
    }

    .searchBox {
        box-sizing: border-box;
        display: block;
        width: 100%;
        border: 1px solid #1d253b80;
        padding: calc(var(--size-bezel) * 1.5) var(--size-bezel);
        background: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' fill='%231d253b80' class='bi bi-search' viewBox='0 0 16 16'> <path d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'></path> </svg>") no-repeat;
        background-position: 96% 45%;
        padding-right: 25px;
        background-size: 16px;
        border-radius: var(--size-radius);
    }

    .searchBox:not(:-moz-placeholder-shown)+.searchBox_label {
        transform: translate(0.25rem, -65%) scale(0.8);
        color: var(--color-accent);
    }

    .searchBox:not(:-ms-input-placeholder)+.searchBox_label {
        transform: translate(0.25rem, -65%) scale(0.8);
        color: var(--color-accent);
    }

    .searchBox:focus+.searchBox_label,
    .searchBox:not(:placeholder-shown)+.searchBox_label {
        transform: translate(0.25rem, -65%) scale(0.8);
        color: var(--color-accent);
    }

    @media screen and (max-width: 999px) {
        /*#wrapp #search_submit {*/
        /*right:-7px !important;*/

        /*}*/
        /*#wrapp #search {*/
        /*top: 0px;*/

        /*}*/

    }

    @media screen and (max-width: 776px) {
        #wrapp #search_submit {
            right: -13px !important;
            top: -12px;
        }

        #wrapp #search {
            top: -8px;
            right: 4px;
        }
    }

    @media screen and (max-width: 450px) {
        .pageTitleContainer {
            display: none;
        }
    }

    @media screen and (max-width: 991px) and (min-width:776px) {
        #wrapp #search_submit {
            right: -67px !important;
            top: -5px;

        }

        #wrapp #search {
            top: -2px;
            right: -45px;
        }
    }
</style>

@php
    $permissions = Cache::get('user' . Auth()->user()->id);
@endphp
<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
    <div class="container-fluid noPadOnMobile">
        <div class="row headerRow" style="display:flex;">

            <!-- Logo and title -->
            <div class="col-lg-7 col-md-7 noPadOnMobile">
                <div class="container-fluid noPadOnMobile" style="padding-left: 0;">
                    <div class="row left-toggler-container"
                        style="background-color:transparent;padding: 0;flex-wrap: nowrap;align-items: center;">

                        <!-- Logo and mobile bars -->
                        <div class=" logo-col noPadOnMobile" style="">
                            <div class="">
                                <div class="navbar-toggle d-inline">
                                    <button type="button" class="navbar-toggler ">
                                        <span class="navbar-toggler-bar bar1"></span>
                                        <span class="navbar-toggler-bar bar2"></span>
                                        <span class="navbar-toggler-bar bar3"></span>
                                    </button>
                                </div>
                                <a class="navbar-brand logo-navbar" href="/home">
                                    <img class ="logo" src="{{ asset($brandingLogoPath) }}"
                                        style="POSITION: absolute;TOP: -24PX;LEFT: 47PX;max-height: 87px;width: auto;filter: brightness(51%);" alt="{{ $brandingName ?? 'Brand' }}" />
                                </a>
                            </div>
                        </div>

                        <!--Page Title -->
                        <div class=" col-sm-9 col-lg-6 noPadOnMobile pageTitleContainer">
                            <span class="navbar-brand pageTitle"
                                style="font-weight: 800;">{{ $pageSlug ?? ($brandingName ?? 'Korvion') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 noPadOnMobile">
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
            <div class="col-1 col-sm-1  mb-1 noPadOnMobile dotsDiv">
                <button stlyle= "flex-grow: 3" class="navbar-toggler navbar-toggler-right" type="button"
                    data-toggle="collapse" data-target="#navigation" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                </button>
                <div class="collapse navbar-collapse" style = "flex-grow: 3" id="navigation">
                    <ul class="navbar-nav ml-auto">

                        <li class="dropdown nav-item">
                            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"
                                style=" background-color:transparent;border:none;padding: 8px 12px">
                                <div class="photo">
                                    <img src="{{ asset('white') }}/img/anime3.png" alt="{{ __('Profile Photo') }}">
                                </div>

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
