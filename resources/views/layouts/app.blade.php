<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset($brandingFaviconPath ?? config('branding.defaults.favicon_path')) }}?v=20260603-favwhite1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Georgia&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400..700&display=swap" rel="stylesheet">
    @brandStyles

    @php($isPlatformAdminPage = !empty($platformAdminPage))
    @php($pageTitle = $isPlatformAdminPage ? 'Platform Administration' : ($pageSlug ?? ($brandingName ?? config('site_vars.projectNameShort'))))
    <title>{{ trans('ui.dom')[$pageTitle] ?? $pageTitle }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link
        href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&family=Cairo:wght@400;500;600;700;800&family=Noto+Naskh+Arabic:wght@400..700&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Reset/Base CSS -->
    <style>
        :root {
            --font-family-sans-serif: "Cairo", sans-serif;
            --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;

            --accent:#6366f1;
            --accent-lt:#818cf8;
            --accent-bg:rgba(99,102,241,.12);
            --success:#10b981;
            --warning:#f59e0b;
            --danger:#ef4444;
            --text-1:#0f172a;
            --text-2:#64748b;
            --text-3:#94a3b8;
            --surface:#ffffff;
            --surface-raised:#f8fafc;
            --border:rgba(0,0,0,.08);
            --border-06:rgba(0,0,0,.06);
            --border-07:rgba(0,0,0,.07);
            --shadow-1:rgba(0,0,0,.1);
            --shadow-2:rgba(0,0,0,.04);
            --chart-grid:rgba(0,0,0,.05);
            --sidebar-link:rgba(255,255,255,.78);
            --sidebar-hover:rgba(255,255,255,.06);
            --sidebar-border:rgba(216,220,227,.14);
            --sidebar-brand-border:rgba(99,102,241,.28);
            --sidebar-active-bg:rgba(99,102,241,.15);
            --overlay-bg:rgba(0,0,0,.5);

            /* Solent premium palette */
            --color-main-bg: var(--surface-raised);
            --color-sidebar: var(--text-1);
            --color-topbar: var(--text-1);
            --color-card: var(--surface);
            --color-card-border: var(--border);
            --color-text: var(--text-1);
            --color-muted: var(--text-2);
            --color-primary-teal: var(--accent);
            --color-secondary-purple: var(--accent);
            --color-brand-gold: var(--accent);
            --color-warning-orange: var(--warning);
            --color-danger-pink: var(--danger);

            --color-shell-bg: var(--color-sidebar);
            --color-accent-gold: var(--color-brand-gold);
            --color-surface: var(--color-main-bg);
            --color-surface-alt: var(--surface-raised);
            --color-surface-soft: var(--accent-bg);

            --brand-primary: var(--color-primary-teal);
            --brand-secondary: var(--color-secondary-purple);
            --brand-accent: var(--color-brand-gold);

            /* Legacy aliases */
            --main-blue: var(--color-primary-teal);
            --main-orange: var(--color-warning-orange);
            --main-green: var(--color-primary-teal);
            --air-force-blue: var(--color-shell-bg);
            --timber-wolf: var(--color-surface);
            --rich-black: var(--color-shell-bg);
            --indigo-dye: var(--color-shell-bg);
            --cadet-gray: var(--color-surface-alt);
            --dark-teal: var(--color-shell-bg);
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
            background: var(--overlay-bg);
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
    <link href="https://fonts.cdnfonts.com/css/montserrat" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
    <link href="{{ asset('assets') }}/css/white-dashboard.css" rel="stylesheet" />
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
    <link href="{{ asset('assets') }}/css/solent-demo.css" rel="stylesheet" />

    @stack('css')
    <link href="{{ asset('assets/css/rtl.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/site-typography.css') }}?v=20260823-2" rel="stylesheet" />
    <link href="{{ asset('assets/css/solent-quick-nav.css') }}" rel="stylesheet" />

    <script>
        window.SolentI18n = {!! json_encode([
            'locale' => app()->getLocale(),
            'direction' => trans('ui.direction'),
            'messages' => trans('ui.dom'),
            'dataTables' => trans('ui.datatables'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

</head>
{{-- <div class="overlay" id="overlay"></div> --}}

<body {{-- onload="myFunction()" --}} class="white-content{{ $class ?? '' }}{{ ($pageSlug ?? null) === 'Dashboard' ? ' solent-dashboard-page' : '' }}{{ $isPlatformAdminPage ? ' platform-admin-page' : '' }}">


    @auth()
        @include('components.processing-overlay')

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
            <div class="overlay" id="overlay"></div>
            @include('layouts.navbars.leftsidebar')

            <div class="main-panel">
                @php($ui = trans('ui.dom'))
                <div class="solent-floating-topbar" aria-label="{{ $ui['Workspace actions'] ?? 'Workspace actions' }}">
                    <button type="button" class="navbar-toggle solent-mobile-sidebar-toggle" aria-label="{{ $ui['Open menu'] ?? 'Open menu' }}">
                        <span class="navbar-toggler-bar bar1"></span>
                        <span class="navbar-toggler-bar bar2"></span>
                        <span class="navbar-toggler-bar bar3"></span>
                    </button>
                    @if ($isPlatformAdminPage)
                        <strong style="color:#0f172a;font-size:14px;">Platform Administration</strong>
                    @else
                        @include('layouts.navbars.quicknav', ['quickNavVariant' => 'desktop'])
                        <form class="solent-floating-search" action="{{ route('global-search') }}" method="GET">
                            <div id="wrapp" class="searchBox2">
                                <div class="SBF2">
                                    <input id="search" name="searchText" type="text" placeholder="{{ $ui['Search cases, doctors...'] ?? 'Search cases, doctors...' }}">
                                    <span id="search_submit" aria-hidden="true"></span>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

                {{-- <div id="loader"></div> --}}
                <div class="content{{ ($pageSlug ?? null) === 'Dashboard' ? ' solent-dashboard-content' : '' }}" {{-- style="display:none;"  id="myDiv" --}}>
                    @unless ($isPlatformAdminPage)
                        @include('layouts.navbars.quicknav', ['quickNavVariant' => 'mobile'])
                    @endunless
                    @if (session()->has('error'))
                        <div class="alert alert-danger" role="alert">
                            @php($flashError = trim((string) session()->get('error')))
                            {{ $ui[$flashError] ?? session()->get('error') }}
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success" role="alert">
                            @php($flashSuccess = trim((string) session()->get('success')))
                            {{ $ui[$flashSuccess] ?? session()->get('success') }}
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
