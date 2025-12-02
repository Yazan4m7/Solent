<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset($brandingFaviconPath ?? config('branding.defaults.favicon_path')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Georgia&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400..700&display=swap" rel="stylesheet">
    @brandStyles

    <title>{{ $pageSlug ?? ($brandingName ?? config('site_vars.projectNameShort')) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link
        href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&family=Noto+Naskh+Arabic:wght@400..700&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Reset/Base CSS -->
    <style>
        :root {
            --font-family-sans-serif: "Nunito", sans-serif;
            --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;

            /* Brand palette (configurable) */
            --main-blue: var(--brand-primary, #0f7c63);
            --main-orange: var(--brand-secondary, #0ab39c);
            --main-green: var(--brand-secondary, #0ab39c);
            --air-force-blue: var(--brand-primary, #0f7c63);
            --timber-wolf: #d9e2ec;
            --rich-black: #1f2a3d;
            --indigo-dye: #1f2a3d;
            --cadet-gray: #9FA5AD;
            --dark-teal: #0f7c63;
        }

        @font-face {
            font-family: SegoeUI;
            src: local("Segoe UI Bold"),
                url(//c.s-microsoft.com/static/fonts/segoe-ui/west-european/bold/latest.woff2) format("woff2"),
                url(//c.s-microsoft.com/static/fonts/segoe-ui/west-european/bold/latest.woff) format("woff"),
                url(//c.s-microsoft.com/static/fonts/segoe-ui/west-european/bold/latest.ttf) format("truetype");
            font-weight: 600;
        }

        .noto-naskh-arabic {
            font-family: "Noto Naskh Arabic", serif;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
        }

        .noto-naskh-arabic {
            font-family: "Noto Naskh Arabic", serif;
            font-optical-sizing: auto;
            font-weight: 600;
            font-style: normal;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 5;
        }

        .overlay.active {
            display: flex;
        }

        .no-scroll {
            overflow: hidden;
        }
    </style>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,300" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway&family=Rubik:wght@500&display=swap" rel="stylesheet">
    <link href="http://fonts.cdnfonts.com/css/montserrat" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">

    <!-- Core Framework CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" media="all"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l')" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <!-- Third-party/Plugin CSS -->
    <link href="{{ asset('assets/css/jquery.datetimepicker.min.css') }}" rel="stylesheet">
    <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="//cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" />

    <!-- Theme CSS -->
    <link href="{{ asset('assets') }}/css/white-dashboard.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/theme.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('assets') }}/css/callouts.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/ysh-custom-css/dialog.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/custom-styling.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/sidebar-fix.css" rel="stylesheet" />
    <link href="{{ asset('css') }}/georgia-font.css" rel="stylesheet" />
    <link href="{{ asset('css/ysh-custom-css/machine-images.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/brand-overrides.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/brand-overrides.css" rel="stylesheet" />

    <!-- Dynamic styling -->
    @include('layouts.dynamicStyling')

    <!-- Page-specific CSS -->
    @stack('css')

</head>
{{-- <div class="overlay" id="overlay"></div> --}}

<body {{-- onload="myFunction()" --}} class="white-content{{ $class ?? '' }}">


    @auth()
        <!-- Loading Overlay -->
        <!-- Loading Spinner Overlay -->
        {{--    <div class="YSH-spinner-overlay" id="loadingOverlay" style="display: none;"> --}}
        {{--        <div class="YSH-spinner"> --}}
        {{--            <div></div> --}}
        {{--            <div></div> --}}
        {{--            <div></div> --}}
        {{--            <div></div> --}}
        {{--            <div></div> --}}
        {{--            <div></div> --}}
        {{--        </div> --}}
        {{--    </div> --}}
        <div class="wrapper" {{-- onload="myFunction()" --}}>
            @include('layouts.navbars.leftsidebar')

            <div class="main-panel">
                @include('layouts.navbars.navbar')


                {{-- <div id="loader"></div> --}}
                <div class="content" {{-- style="display:none;"  id="myDiv" --}}>
                    @if (session()->has('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session()->get('error') }}
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    @yield('content')

                </div>
            </div>

        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" {{-- style="display: none;"  id="myDiv" --}}>
            @csrf
        </form>
    @else
        @include('layouts.navbars.navbar')


        <div class="wrapper wrapper-full-page animate-bottom" {{-- style="display:none;" --}}>
            <div class="overlay" id="overlay"></div>
            <div class="full-page {{ $contentClass ?? '' }}">

                <div class="content">

                    <div class="container">

                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    @endauth

</body>
@include('layouts.footer')

</html>
