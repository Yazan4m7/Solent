@extends('layouts.app', ['pageSlug' => config('site_vars.labWorkFlowLabel')])
@php
    // Load global configuration
    $deviceConfig = config('app_config.device_images', [
        'width' => '100%',
        'max_width' => '180px',
        'height' => 'auto',
        'padding' => '10px',
        'border_radius' => '8px',
        'hover_effect' => true,
        'background' => 'transparent',
    ]);
@endphp


@push('css')
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">


    <link href="{{ asset('assets') }}/css/ysh-custom-css/dialog.css" rel="stylesheet"/>
    <link href="{{ asset('assets') }}/css/ysh-custom-css/OperationsDashboardStyling.css" rel="stylesheet"/>
    <link href="{{ asset('assets') }}/css/active-cases.css" rel="stylesheet"/>
    <link href="{{ asset('assets') }}/css/waiting-dialog.css" rel="stylesheet"/>
    <link href="{{ asset('assets') }}/css/v3styles.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/css/ysh-custom-css/OperationsDashboardStyling.css" rel="stylesheet">
    <style>
        :root {
            --ops-primary: var(--color-primary-teal, #22B8A5);
            --ops-secondary: var(--color-secondary-purple, #6366F1);
            --ops-surface: var(--color-main-bg, #F3F4F7);
            --ops-surface-alt: #E9EEF4;
            --ops-panel: var(--color-card, #FFFFFF);
            --ops-border: var(--color-card-border, #D8DCE3);
            --ops-text: var(--color-text, #111827);
            --ops-muted: var(--color-muted, #64748B);
        }

        body.white-content {
            background: var(--ops-surface);
            color: var(--ops-text);
            font-family: 'Cairo', sans-serif;
        }

        body.white-content button,
        body.white-content input,
        body.white-content select,
        body.white-content textarea,
        body.white-content table,
        body.white-content .card,
        body.white-content .modal {
            font-family: inherit;
        }

        .sidebar,
        .sidebar .sidebar-wrapper {
            background: var(--color-shell-bg, #11151E) !important;
        }

        .sidebar .nav li > a,
        .sidebar .nav li > a p,
        .sidebar .nav li > a i {
            color: #f5f7fb !important;
        }

        .sidebar .nav li.active > a,
        .sidebar .nav li:hover > a {
            background: rgba(255, 255, 255, 0.06) !important;
            border-left: 3px solid var(--ops-primary);
            color: var(--ops-primary) !important;
        }

        .card,
        .card .card-body {
            background: var(--ops-panel);
            color: var(--ops-text);
        }

        .card .card-header {
            background: var(--ops-surface-alt);
            color: var(--ops-text);
            border-bottom: 1px solid var(--ops-border);
        }

        .nav-tabs .nav-link {
            color: var(--ops-text);
            background: var(--ops-surface-alt);
            border: 1px solid var(--ops-border);
        }

        .nav-tabs .nav-link.active {
            background: var(--ops-primary);
            color: #11151E;
            border-color: var(--ops-primary);
        }

        table.dataTable thead th,
        .dataTables_wrapper .dataTables_scrollHead th {
            background: var(--ops-surface);
            color: var(--ops-text);
            border-color: var(--ops-border);
        }

        table.dataTable tbody tr {
            background: var(--ops-panel);
            color: var(--ops-text);
        }

        table.dataTable tbody tr:nth-child(even) {
            background: var(--ops-secondary);
        }

        table.dataTable tbody tr:hover {
            background: var(--ops-surface-alt);
        }

        .badge-primary,
        .btn-primary {
            background: var(--ops-primary);
            border-color: var(--ops-primary);
            color: #11151E;
        }

        .badge-info,
        .btn-info {
            background: var(--ops-secondary);
            border-color: var(--ops-secondary);
            color: #11151E;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--ops-text) !important;
            background: var(--ops-panel) !important;
            border: 1px solid var(--ops-border) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--ops-primary) !important;
            color: #0b0e14 !important;
            border-color: var(--ops-primary) !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            background: var(--ops-surface-alt);
            color: var(--ops-text);
            border: 1px solid var(--ops-border);
        }

        .header .navbar,
        .navbar.navbar-transparent {
            background: var(--color-shell-bg, #11151E) !important;
            color: #f5f7fb !important;
            border-bottom: 1px solid rgba(179, 135, 45, 0.35);
        }

        input[type="checkbox"],
        input[type="radio"]{
            transform: scale(1.4);
        }

        /* Device container styling */
        .device-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: {{ $deviceConfig["container_gap"] ?? '15px' }};
            width: 100%;
        }

        .device-item {
            width: 100%;
            max-width: {{ $deviceConfig["max_width"] ?? '150px' }};
            text-align: center;
            margin-bottom: {{ $deviceConfig["margin_bottom"] ?? '15px' }};
            transition: all 0.3s ease;
        }

        .device-item img {
            width: {{ $deviceConfig["width"] ?? '100%' }};
            height: {{ $deviceConfig["height"] ?? 'auto' }};
            max-width: {{ $deviceConfig["max_width"] ?? '150px' }};
            padding: {{ $deviceConfig["padding"] ?? '10px' }};
            border-radius: {{ $deviceConfig["border_radius"] ?? '8px' }};
            background: {{ $deviceConfig["background"] ?? 'transparent' }};
            object-fit: contain;
        }

        .device-item:hover {
            transform: {{ $deviceConfig["hover_effect"] ? 'scale(1.05)' : 'none' }};
            box-shadow: none !important;
        }

        .device-item .device-name {
            margin-top: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .device-item .device-status {
            font-size: 12px;
            color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .device-item {
                max-width: {{ $deviceConfig["responsive_sizes"]["tablet"] ?? '120px' }};
            }
            .device-item img {
                max-width: {{ $deviceConfig["responsive_sizes"]["tablet"] ?? '120px' }};
            }
        }

        @media (max-width: 576px) {
            .device-item {
                max-width: {{ $deviceConfig["responsive_sizes"]["mobile"] ?? '100px' }};
            }
            .device-item img {
                max-width: {{ $deviceConfig["responsive_sizes"]["mobile"] ?? '100px' }};
            }
        }
    </style>

<style>
    /* ----------------------------------------------
    * Generated by Animista on 2025-6-15 21:15:57
    * Licensed under FreeBSD License.
    * See http://animista.net/license for more info.
    * w: http://animista.net, t: @cssanimista
    * ---------------------------------------------- */
    .slide-in-blurred-top{animation:slide-in-blurred-top .6s cubic-bezier(.23,1.000,.32,1.000) both}
    @keyframes slide-in-blurred-top{0%{transform:translateY(-1000px) scaleY(2.5) scaleX(.2);transform-origin:50% 0;filter:blur(40px);opacity:0}100%{transform:translateY(0) scaleY(1) scaleX(1);transform-origin:50% 50%;filter:blur(0);opacity:1}}
</style>

<style>
    /* Korvex-inspired reskin for Operations dashboard */
    :root {
        --Korvex-navy: var(--color-main-bg, #F3F4F7);
        --Korvex-panel: var(--color-card, #FFFFFF);
        --Korvex-panel-2: var(--color-card, #FFFFFF);
        --Korvex-gold: var(--color-brand-gold, #DBC373);
        --Korvex-gold-soft: #eadb9a;
        --Korvex-ink: var(--color-text, #111827);
        --Korvex-muted: var(--color-muted, #64748B);
        --Korvex-border: var(--color-card-border, #D8DCE3);
        --Korvex-accent: var(--color-primary-teal, #22B8A5);
        --Korvex-glow: 0 14px 34px rgba(17, 24, 39, 0.08);
    }

    body.white-content {
        background: radial-gradient(circle at 15% 20%, rgba(196,154,60,0.12), transparent 26%),
        radial-gradient(circle at 82% 12%, rgba(95,125,183,0.18), transparent 30%),
        var(--Korvex-navy);
        color: var(--Korvex-ink);
    }

    .site-wrapper {
        background: transparent;
    }

    .header .navbar,
    .navbar.navbar-transparent {
        background: rgba(13, 18, 32, 0.82) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--Korvex-border);
    }

    .card,
    .card .card-body {
        background: var(--Korvex-panel-2);
        border: 1px solid var(--Korvex-border);
        color: var(--Korvex-ink);
        border-radius: 18px;
        box-shadow: var(--Korvex-glow);
    }

    .card .card-header {
        background: linear-gradient(135deg, rgba(196,154,60,0.2), rgba(17,27,45,0.9));
        border-bottom: 1px solid var(--Korvex-border);
        color: var(--Korvex-ink);
    }

    .macaw-tabs.macaw-aurora-tabs {
        background: transparent;
    }

    .macaw-tabs .stageSidebar {
        display: flex;
        gap: 12px;
        align-items: stretch;
        overflow-x: auto;
        padding: 12px;
        margin-bottom: 18px;
        background: rgba(255, 255, 255, 0.58);
        border: 1px solid rgba(134, 177, 196, 0.42);
        border-radius: 22px;
        box-shadow: 0 18px 40px rgba(45, 76, 108, 0.12);
        backdrop-filter: blur(14px);
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .macaw-tabs .stageSidebar::-webkit-scrollbar {
        display: none;
    }

    .stageSidebar button {
        display: flex;
        flex: 0 0 96px;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 128px;
        padding: 16px 10px !important;
        margin: 0;
        border-radius: 18px !important;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(237, 246, 250, 0.96));
        border: 1px solid #cae0ea;
        color: #32425a;
        box-shadow: 0 10px 22px rgba(75, 110, 145, 0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }

    .stageSidebar button:hover,
    .stageSidebar button[aria-selected="true"] {
        transform: translateY(-2px);
        border-color: #9ac9de;
        background: linear-gradient(180deg, rgba(255, 255, 255, 1), rgba(226, 242, 248, 1));
        box-shadow: 0 16px 28px rgba(75, 110, 145, 0.18);
    }

    .stageSidebar__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        color: #3a4658;
    }

    .stageSidebar__icon i,
    .stageSidebar__icon svg {
        font-size: 22px;
    }

    .stageSidebar__name {
        font-size: 17px;
        font-weight: 700;
        line-height: 1.15;
        text-align: center;
        color: #34445b;
    }

    .stageSidebar__counts {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .stageSidebar .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid transparent;
        box-shadow: none;
    }

    .stageSidebar .activeBadge {
        background: #eaf5ff;
        color: #0f766e;
        border-color: rgba(34, 184, 165, 0.34);
    }

    .stageSidebar .waitingBadge {
        background: #fff0f0;
        color: #b45309;
        border-color: rgba(255, 138, 31, 0.34);
    }

    .modal {
        padding: 16px !important;
    }

    .modal-dialog {
        width: 100%;
        max-width: min(680px, calc(100vw - 32px));
        margin: auto;
    }

    .modal-content {
        border-radius: 24px;
        overflow: hidden;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 24px;
        padding-right: 24px;
    }

    .modal-header {
        padding-top: 22px;
        padding-bottom: 18px;
    }

    .modal-body {
        padding-top: 18px;
        padding-bottom: 22px;
    }

    .modal-footer {
        padding-top: 12px;
        padding-bottom: 18px;
    }

    @media (max-width: 575.98px) {
        .stageSidebar button {
            flex-basis: 88px;
            min-height: 120px;
            padding: 14px 8px !important;
        }

        .stageSidebar__name {
            font-size: 15px;
        }

        .modal {
            padding: 12px !important;
        }

        .modal-dialog {
            max-width: calc(100vw - 24px);
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding-left: 18px;
            padding-right: 18px;
        }
    }

    .macaw-tabs .macaw-silk-tabs > [role="tablist"] {
        background: rgba(17,27,45,0.6);
        border: 1px solid var(--Korvex-border);
        border-radius: 14px;
        padding: 8px;
        box-shadow: var(--Korvex-glow);
    }

    .nav-tabs .nav-link {
        background: rgba(255,255,255,0.03);
        border-radius: 10px;
        color: var(--Korvex-ink);
        border: 1px solid var(--Korvex-border);
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--Korvex-gold), var(--Korvex-gold-soft));
        color: #111827;
        border-color: var(--Korvex-gold);
    }

    table.dataTable thead th,
    .dataTables_wrapper .dataTables_scrollHead th {
        background: rgba(17, 27, 45, 0.9);
        color: var(--Korvex-ink);
        border-color: var(--Korvex-border);
    }

    table.dataTable tbody tr {
        background: rgba(22, 36, 58, 0.8);
        color: var(--Korvex-ink);
    }

    table.dataTable tbody tr:nth-child(even) {
        background: rgba(17, 27, 45, 0.85);
    }

    .btn-primary,
    .badge-primary {
        background: linear-gradient(135deg, var(--Korvex-gold), var(--Korvex-gold-soft));
        border-color: var(--Korvex-gold);
        color: #111827;
        box-shadow: var(--Korvex-glow);
    }

    .btn-info,
    .badge-info {
        background: rgba(95, 125, 183, 0.25);
        border-color: var(--color-secondary-purple, #6366F1);
        color: var(--Korvex-ink);
    }

    /* Hero and stat chips */
    .ops-hero {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 16px;
        margin: 0 0 18px 0;
        padding: 18px;
        background: linear-gradient(135deg, rgba(196,154,60,0.12), rgba(17,27,45,0.75));
        border: 1px solid var(--Korvex-border);
        border-radius: 18px;
        box-shadow: var(--Korvex-glow);
    }

    .ops-hero h2 {
        margin: 0 0 6px 0;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .ops-hero p {
        margin: 0;
        color: var(--Korvex-muted);
    }

    .ops-hero .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }

    .ops-chip {
        padding: 14px;
        background: rgba(17, 27, 45, 0.85);
        border: 1px solid var(--Korvex-border);
        border-radius: 14px;
        box-shadow: var(--Korvex-glow);
    }

    .ops-chip .label {
        color: var(--Korvex-muted);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }

    .ops-chip .value {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--Korvex-ink);
    }

    .ops-chip .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        color: var(--Korvex-ink);
        background: rgba(196,154,60,0.14);
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(196,154,60,0.45);
        margin-top: 6px;
    }

    @media (max-width: 1100px) {
        .ops-hero {
            grid-template-columns: 1fr;
        }
    }

    body.white-content {
        background: var(--color-main-bg, #F3F4F7) !important;
        color: var(--color-text, #111827) !important;
    }

    .header .navbar,
    .navbar.navbar-transparent {
        background: var(--color-topbar, #151C27) !important;
        border-bottom: 1px solid rgba(216, 220, 227, 0.14) !important;
    }

    .card,
    .card .card-body,
    .modal-content,
    .ops-chip,
    .ops-hero {
        background: var(--color-card, #FFFFFF) !important;
        border-color: var(--color-card-border, #D8DCE3) !important;
        color: var(--color-text, #111827) !important;
        box-shadow: 0 14px 34px rgba(17, 24, 39, 0.08) !important;
    }

    .card .card-header,
    .modal-header,
    .modal-footer {
        background: #F8FAFC !important;
        border-color: var(--color-card-border, #D8DCE3) !important;
        color: var(--color-text, #111827) !important;
    }

    .macaw-tabs .stageSidebar {
        background: var(--color-card, #FFFFFF) !important;
        border-color: var(--color-card-border, #D8DCE3) !important;
    }

    .stageSidebar button,
    .nav-tabs .nav-link,
    .macaw-tabs .macaw-silk-tabs > [role="tablist"] {
        background: var(--color-card, #FFFFFF) !important;
        border-color: var(--color-card-border, #D8DCE3) !important;
        color: var(--color-text, #111827) !important;
    }

    .stageSidebar button:hover,
    .stageSidebar button[aria-selected="true"],
    .nav-tabs .nav-link.active {
        background: var(--color-primary-teal, #22B8A5) !important;
        border-color: var(--color-primary-teal, #22B8A5) !important;
        color: #061313 !important;
    }

    .stageSidebar .activeBadge {
        background: rgba(34, 184, 165, 0.14) !important;
        border-color: rgba(34, 184, 165, 0.34) !important;
        color: #0f766e !important;
    }

    .stageSidebar .waitingBadge {
        background: rgba(255, 138, 31, 0.16) !important;
        border-color: rgba(255, 138, 31, 0.34) !important;
        color: #b45309 !important;
    }

    table.dataTable thead th,
    .dataTables_wrapper .dataTables_scrollHead th {
        background: #F8FAFC !important;
        color: var(--color-text, #111827) !important;
        border-color: var(--color-card-border, #D8DCE3) !important;
    }

    table.dataTable tbody tr,
    table.dataTable tbody tr:nth-child(even) {
        background: var(--color-card, #FFFFFF) !important;
        color: var(--color-text, #111827) !important;
    }

    .btn-primary,
    .badge-primary {
        background: var(--color-primary-teal, #22B8A5) !important;
        border-color: var(--color-primary-teal, #22B8A5) !important;
        color: #061313 !important;
    }

    .btn-info,
    .badge-info {
        background: var(--color-secondary-purple, #6366F1) !important;
        border-color: var(--color-secondary-purple, #6366F1) !important;
        color: #ffffff !important;
    }

    .ops-hero p,
    .ops-chip .label {
        color: var(--color-muted, #64748B) !important;
    }

    .ops-chip .pill {
        background: rgba(219, 195, 115, 0.18) !important;
        border-color: rgba(219, 195, 115, 0.42) !important;
        color: var(--color-text, #111827) !important;
    }

    .stageSidebar .badge,
    .macaw-tabs.macaw-silk-tabs .innerBtn .badge {
        align-items: center !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        height: 20px !important;
        justify-content: center !important;
        line-height: 20px !important;
        margin: 0 4px 0 0 !important;
        min-height: 20px !important;
        min-width: 20px !important;
        padding: 0 6px !important;
        width: auto !important;
    }

    .macaw-tabs.macaw-aurora-tabs > .stageSidebar > button {
        justify-content: flex-start !important;
        min-height: 104px !important;
        padding: 14px 8px 32px !important;
        position: relative !important;
    }

    .macaw-tabs.macaw-aurora-tabs > .stageSidebar > button .stageSidebar__counts {
        bottom: 8px !important;
        display: flex !important;
        gap: 6px !important;
        justify-content: center !important;
        left: 50% !important;
        margin: 0 !important;
        position: absolute !important;
        transform: translateX(-50%) !important;
        width: auto !important;
    }

    .macaw-tabs.macaw-aurora-tabs > .stageSidebar > button .stageSidebar__counts .badge {
        font-size: 11px !important;
        height: 22px !important;
        line-height: 22px !important;
        margin: 0 !important;
        min-height: 22px !important;
        min-width: 26px !important;
        padding: 0 7px !important;
    }

    .macaw-tabs.macaw-aurora-tabs > .stageSidebar > button .stageSidebar__icon {
        flex: 0 0 auto !important;
        min-height: 24px !important;
    }

    .macaw-tabs.macaw-aurora-tabs > .stageSidebar > button .stageSidebar__name {
        flex: 0 0 auto !important;
    }

    .macaw-tabs.macaw-silk-tabs > [role="tablist"] {
        box-sizing: border-box !important;
        display: flex !important;
        gap: 8px !important;
        margin-left: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        padding: 8px !important;
        width: 100% !important;
    }

    .macaw-tabs.macaw-silk-tabs > [role="tablist"] > .innerBtn {
        align-items: center !important;
        box-sizing: border-box !important;
        display: inline-flex !important;
        flex: 1 1 0 !important;
        justify-content: center !important;
        margin: 0 !important;
        max-width: none !important;
        min-width: 0 !important;
        padding: 6px 8px !important;
        transform: none !important;
        white-space: nowrap !important;
    }

    .macaw-tabs.macaw-silk-tabs > [role="tablist"] > .innerBtn[aria-selected="true"] {
        transform: none !important;
    }

    body.white-content,
    body.white-content *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]):not(.material-icons):not(.material-symbols-outlined) {
        font-family: 'Cairo', sans-serif !important;
    }
</style>

    @endpush



@section('content')
    {{--    @php--}}
    {{--        try {--}}
    {{--    @endphp--}}


    @php
        $color = '#01292b';
            //dd($devices);

            $permissions = Cache::get('user' . Auth()->user()->id);
            $canEditCase = false;
            if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 102))) {
                $canEditCase = true;
            }
    @endphp
    @php
        $stages = [
            'design' => [
                'activeCases' => $aDesign,
                'waitingCases' => $wDesign,
                'numericStage' => 1,
                'icon' => "<i class='fa-solid fa-desktop'></i>",
            ],
            '3dprinting' => [
                'activeCases' => $aPrinting,
                'waitingCases' => $wPrinting,
                'numericStage' => 3,
                'icon' => "
            <svg version='1.1' class='printingIcon' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
             viewBox='0 0 367.579 213.624' style='enable-background:new 0 0 367.579 213.624;' xml:space='preserve'>
            <g id='XMLID_80_'>
            <path id='XMLID_81_' d='M54.962,85.176h21.863c12.45,0,20.9-2.581,25.355-7.743c4.453-5.162,6.681-10.678,6.681-16.549
                c0-6.579-2.456-12.424-7.364-17.537c-4.911-5.11-11.767-7.667-20.573-7.667c-16.803,0-27.382,8.858-31.732,26.57L6.225,55.417
                C9.767,39.426,18.345,26.19,31.96,15.714C45.573,5.238,62.35,0,82.292,0c20.851,0,38.387,4.965,52.609,14.891
                c14.22,9.926,21.332,23.5,21.332,40.719c0,22.589-11.843,38.038-35.528,46.344c27.834,7.385,41.753,23.771,41.753,49.159
                c0,18.208-7.112,33.177-21.332,44.911c-14.222,11.734-33.834,17.601-58.834,17.601c-23.989,0-42.994-6.033-57.012-18.099
                C11.259,183.46,2.833,168.488,0,150.615l44.031-6.377c4.251,21.358,16.599,32.036,37.046,32.036c9.513,0,17.232-2.522,23.154-7.57
                c5.921-5.046,8.882-11.809,8.882-20.288c0-8.984-2.709-15.698-8.123-20.139c-5.416-4.441-15.767-6.662-31.049-6.662H54.962V85.176z
                '/>
            <path id='XMLID_83_' d='M197.682,3.188h63.256c25.788,0,45.002,3.568,57.643,10.704c12.641,7.136,23.967,18.423,33.979,33.858
                c10.012,15.437,15.02,34.947,15.02,58.53c0,29.659-8.75,54.431-26.242,74.32c-17.496,19.89-41.615,29.834-72.359,29.834h-71.295
                V3.188z M245.356,41.297v130.118h19.999c17.677,0,30.808-6.604,39.392-19.814c8.586-13.209,12.881-28.719,12.881-46.536
                c0-12.55-2.451-24.165-7.35-34.845c-4.9-10.678-10.984-18.167-18.258-22.471c-7.273-4.301-16.009-6.453-26.21-6.453H245.356z'/>
            </g>
            </svg>
            ",
            ],
            'milling' => [
                'activeCases' => $aMilling,
                'waitingCases' => $wMilling,
                'numericStage' => 2,
                'icon' => "<svg class='millingIcon' version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
             viewBox='0 0 219.296 416.891' style='enable-background:new 0 0 219.296 416.891;' xml:space='preserve'>
            <path id='XMLID_96_'  d='M83.523,285.071
            c-8.936,0-17.387-0.009-25.838,0.002c-18.806,0.023-29.419-10.595-29.401-29.402c0.014-14.833-0.005-29.665,0.01-44.498
            c0.016-15.216,6.395-23.871,20.709-28.584c1.27-0.418,2.911-2.281,2.937-3.503c0.228-10.983,0.133-21.974,0.133-33.931
            c-6.659,0-13.105,0.414-19.467-0.14c-4.52-0.393-9.315-1.333-13.297-3.374c-6.953-3.564-10.608-9.93-11.158-17.792
            c-0.267-3.816-0.089-51.173-0.126-55.005c-0.039-4.011,1.898-6.506,5.923-6.479c4.169,0.028,5.652,2.911,5.665,6.735
            c0.008,2.333-0.048,48.178-0.011,50.511c0.152,9.463,4.396,13.745,13.798,13.749c50.664,0.021,101.328,0.018,151.992,0.001
            c9.887-0.003,14.027-4.111,14.03-13.927c0.015-47.664,0.002-54.688,0.006-102.352c0-1.496-0.504-3.464,0.245-4.389
            c1.598-1.976,3.891-4.731,5.849-4.692c1.897,0.038,3.856,3.071,5.52,5.018c0.499,0.584,0.113,1.935,0.113,2.934
            c-0.001,48.164,0.019,55.688-0.021,103.852c-0.013,16.046-9.434,25.329-25.55,25.365c-5.95,0.013-11.901,0.002-18.142,0.002
            c0,12.556,0,24.485,0,36.328c3.596,1.459,7.364,2.457,10.568,4.39c8.888,5.365,12.844,13.639,12.863,23.894
            c0.03,15.999,0.112,31.998,0.031,47.997c-0.081,16.03-11.432,27.205-27.559,27.28c-8.982,0.042-17.965,0.008-27.537,0.008
            c-0.12,2.405-0.316,4.492-0.315,6.579c0.007,28.831,0.009,57.661,0.106,86.491c0.014,4.045-1.001,7.294-4.025,10.249
            c-5.715,5.585-11.001,11.606-16.559,17.356c-4.076,4.216-6.825,4.203-10.946-0.073c-6.011-6.239-12.218-12.334-17.687-19.025
            c-1.956-2.393-2.785-6.337-2.807-9.581c-0.186-28.664-0.075-57.329-0.052-85.994C83.524,289.277,83.523,287.485,83.523,285.071z
             M96.692,294.324c-0.469,0.283-0.937,0.567-1.406,0.85c0,3.786,0.243,7.591-0.068,11.351c-0.316,3.831,1.056,6.487,3.816,9.093
            c7.003,6.614,13.707,13.545,20.548,20.331c1.076,1.067,2.241,2.045,4.245,3.863c0-5.612,0.096-9.891-0.05-14.162
            c-0.054-1.586-0.161-3.641-1.12-4.651C114.113,312.002,105.374,303.19,96.692,294.324z M96.59,329.452
            c-0.428,0.236-0.857,0.472-1.285,0.708c0,1.957,0.271,3.96-0.048,5.864c-1.276,7.614,1.425,13.266,7.321,18.203
            c5.594,4.684,10.458,10.239,15.648,15.406c1.543,1.536,3.099,3.06,5.565,5.493c0-6.143,0.101-10.776-0.062-15.399
            c-0.049-1.384-0.441-3.121-1.342-4.054C113.871,346.854,105.21,338.173,96.59,329.452z M116.706,386.799
            c-7.345-7.452-14.241-14.449-20.966-21.272c-2.12,11.971,1.972,20.231,14.419,28.222
            C112.486,391.28,114.851,388.769,116.706,386.799z M106.012,285.297c5.899,5.949,11.817,11.916,17.377,17.523
            c0-5.319,0-11.342,0-17.523C117.143,285.297,111.367,285.297,106.012,285.297z'/>
            <path id='XMLID_93_' d='M163.2,66.867c0.003-13.2-0.054-6.892,0.028-20.092c0.04-6.383,2.412-9.25,7.259-9.047
            c5.681,0.237,6.988,4.26,6.997,8.851c0.057,26.603-0.012,33.698-0.026,60.301c-0.003,4.996-2.164,8.607-7.374,8.444
            c-5.177-0.162-6.987-3.838-6.935-8.856C163.289,93.269,163.197,80.068,163.2,66.867z'/>
            <path id='XMLID_83_' style='fill:#FFFFFF;' d='M96.692,294.324c8.682,8.866,17.422,17.678,25.965,26.676
            c0.959,1.01,1.066,3.065,1.12,4.651c0.146,4.271,0.05,14.833,0.05,20.445c-2.004-1.818-3.169-2.796-4.245-3.863
            c-6.841-6.787-13.545-13.717-20.548-20.331c-2.76-2.607-4.132-5.262-3.816-9.093c0.311-3.76,0.068-13.849,0.068-17.634
            C95.754,294.89,96.223,294.607,96.692,294.324z'/>
            <path id='XMLID_82_' style='fill:#FFFFFF;' d='M116.706,375.126c-1.855,1.97-4.22,16.153-6.547,18.624
            c-12.447-7.991-16.539-27.924-14.419-39.895C102.465,360.678,109.361,367.675,116.706,375.126z'/>
            <path id='XMLID_81_' style='fill:#FFFFFF;' d='M123.895,244.73c10.552,0,21.104-0.043,31.655,0.017
            c6.287,0.036,9.415,2.403,9.209,7.09c-0.26,5.919-4.436,7.163-9.32,7.172c-21.302,0.037-42.604,0.06-63.906,0.008
            c-4.776-0.012-8.647-1.797-8.584-7.259c0.062-5.416,3.757-7.081,8.685-7.049C102.387,244.777,113.141,244.729,123.895,244.73z'/>
            <path id='XMLID_80_' style='fill:#FFFFFF;' d='M96.462,165.605c-5.129,0-10.259,0.075-15.387-0.024
            c-4.105-0.079-6.799-2.359-6.76-6.389c0.038-3.964,2.628-6.336,6.784-6.353c10.259-0.043,20.519-0.042,30.778,0.017
            c4.142,0.024,6.756,2.289,6.733,6.364c-0.022,4.051-2.594,6.308-6.766,6.342c-5.127,0.041-10.255,0.01-15.383,0.01
            C96.462,165.583,96.462,165.594,96.462,165.605z'/>
            </svg>
            ",
            ],
            'sintering' => [
                'activeCases' => $aSintering,
                'waitingCases' => $wSintering,
                'numericStage' => 4,
                'icon' => "<i class='fa-solid fa-fire-flame-curved'></i>",
            ],
            'pressing' => [
                'activeCases' => $aPressing,
                'waitingCases' => $wPressing,
                'numericStage' => 5,
                'icon' => "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'>
            <defs><style>.fa-secondary{opacity:.4}</style>
            </defs><path class='fa-primary' d='M350 206.6c3.781 8.803 1.984 19.03-4.594 26l-136 144.1c-9.062 9.601-25.84 9.601-34.91 0l-136-144.1C31.97 225.7 30.17 215.4 33.95 206.6C37.75 197.8 46.42 192.1 56 192.1L128 192.1V64.03c0-17.69 14.33-32.02 32-32.02h64c17.67 0 32 14.34 32 32.02v128.1l72 .0314C337.6 192.1 346.3 197.8 350 206.6z'/>
            <path class='fa-secondary' d='M352 416H31.1C14.33 416 0 430.3 0 447.1S14.33 480 31.1 480H352C369.7 480 384 465.7 384 448S369.7 416 352 416z'/></svg>",
            ],
            'finishing' => [
                'activeCases' => $aFinishing,
                'waitingCases' => $wFinishing,
                'numericStage' => 6,
                'icon' => "<i class='fa-solid fa-broom'></i>",
            ],
            'QC' => [
                'activeCases' => $aQC,
                'waitingCases' => $wQC,
                'numericStage' => 7,
                'icon' => "<i class='fa-solid fa-magnifying-glass'></i>",
            ],
            'delivery' => [
                'activeCases' => $aDelivery,
                'waitingCases' => $wDelivery,
                'numericStage' => 8,
                'icon' => "<i class='fa-solid fa-truck'></i>",
            ],
        ];
        if (!Auth()->user()->is_admin) {
            if (!$permissions->contains('permission_id', 1)) {
                unset($stages['design']);
            }
            if (!$permissions->contains('permission_id', 2)) {
                unset($stages['milling']);
            }
            if (!$permissions->contains('permission_id', 3)) {
                unset($stages['3dprinting']);
            }
            if (!$permissions->contains('permission_id', 4)) {
                unset($stages['sintering']);
            }
            if (!$permissions->contains('permission_id', 5)) {
                unset($stages['pressing']);
            }
            if (!$permissions->contains('permission_id', 6)) {
                unset($stages['finishing']);
            }
            if (!$permissions->contains('permission_id', 7)) {
                unset($stages['qc']);
            }
            if (!$permissions->contains('permission_id', 8)) {
                unset($stages['delivery']);
            }
        }

    @endphp
    @php
        $activeTotal = collect($stages)->sum(fn($s) => count($s['activeCases']));
        $waitingTotal = collect($stages)->sum(fn($s) => count($s['waitingCases']));
        $totalCases = $activeTotal + $waitingTotal;
        $stagesCount = count($stages);
        $devicesCount = isset($devices) ? count($devices) : 0;
    @endphp
        <!-- Begin .site-wrapper -->
    <div class="site-wrapper">
        <div class="ops-hero">
            <div>
                <h2>Operations Control</h2>
                <p>Korvex-styled workflow cockpit: monitor load, move cases, and keep machines humming.</p>
            </div>
            <div class="stat-grid">
                <div class="ops-chip">
                    <div class="label">Active Units</div>
                    <div class="value">{{ $activeTotal }}</div>
                    <div class="pill">In progress</div>
                </div>
                <div class="ops-chip">
                    <div class="label">Waiting Queue</div>
                    <div class="value">{{ $waitingTotal }}</div>
                    <div class="pill">Ready to start</div>
                </div>
                <div class="ops-chip">
                    <div class="label">Stages Live</div>
                    <div class="value">{{ $stagesCount }}</div>
                    <div class="pill">Pipelines</div>
                </div>
                <div class="ops-chip">
                    <div class="label">Devices</div>
                    <div class="value">{{ $devicesCount }}</div>
                    <div class="pill">Tracked hardware</div>
                </div>
            </div>
        </div>
        <!-- Begin waiting milling dialog -->
        <!-- Begin Main -->
        <main style="background: transparent">
            <!-- Begin .macaw-tabs -->
            <div class="macaw-tabs macaw-aurora-tabs notransition">
                <div role="tablist" class="stageSidebar" aria-orientation="vertical">
                    @foreach ($stages as $key => $stage)
                        @php
                            // For display and ID purposes, lowercase for all stages. 3dprinting
                            $keyId = strtolower($key);
                            $displayKey = $key;

                        $displayKey=  ( $key == "3dprinting")? "Printing" : $displayKey;
                        $displayKey=  ( $key == "Qc")? "QC" : $displayKey;
                        @endphp
                        <button role="tab" aria-selected="false" aria-controls="{{ $keyId . 'label' }}"
                                id="{{ $keyId }}" onclick="setOuterTab(this)">
                            <span class="stageSidebar__icon">{!! $stage['icon'] !!}</span>
                            <span class="stageSidebar__name">{{ $displayKey }}</span>
                            <span class="stageSidebar__counts">
                                <span class="badge activeBadge">{{ count($stage['activeCases']) }}</span>
                                <span class="badge waitingBadge">{{ count($stage['waitingCases']) }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>
                @foreach ($stages as $key => $stage)
                    @php
                        // Standardize key format
                        $key = strtolower($key);

                    @endphp
                    {{--                <h1>{{$key}}</h1> --}}
                    <div class="notransition" tabindex="0" role="tabpanel" aria-labelledby="{{ $key }}"
                         id="{{ $key . 'label' }}" hidden>
                        <!-- Begin .macaw-tabs -->
                        <div class="macaw-tabs macaw-silk-tabs notransition">
                            @include('cases.dashboards-partials.tabs', ['key' => $key, 'stage' => $stage])
                            {{-- ----------------waiting TABLE--------------- --}}
                            {{-- ----------------waiting TABLE--------------- --}}
                            {{-- ----------------waiting TABLE--------------- --}}
                            {{-- ----------------waiting TABLE--------------- --}}
                            <div tabindex="0" role="tabpanel" hidden aria-labelledby="{{ 'waiting-' . $key . 'label' }}"
                                 id="{{ 'waiting-' . $key }}">
                                @switch(strtolower($key))
                                    @case('milling')
                                        <x-waiting-dialog title="Choose Machine" btnText="NEST" type="milling"
                                                          :devices="$devices"
                                                          stageId="2"/>
                                        <button type="submit" class="btn btn-primary receiveSelectBtn milling"
                                                data-toggle="modal"
                                                data-backdrop="false" style="display:none; margin:5px;"
                                                onclick="openModal('milling',true)">SET
                                        </button>
                                        @break

                                    @case('3dprinting')
                                        @php $key = "3dprinting"; @endphp
                                        <x-waiting-dialog title="Choose Printer" btnText="SET" type="3dprinting"
                                                                     :devices="$devices"
                                                                     stageId="3" showBuildName="true"/>
                                        <button type="submit" class="btn btn-primary receiveSelectBtn 3dprinting"
                                                data-toggle="modal" data-backdrop="false"
                                                style="display:none; margin:5px;"
                                                onclick="openModal('3dprinting',true)">SET
                                        </button>
                                        @break

                                    @case('sintering')
                                        <x-waiting-dialog title="Choose Furnace" btnText="SET" type="sintering"
                                                          :devices="$devices"
                                                          stageId="4"/>
                                        <button type="submit" class="btn btn-primary receiveSelectBtn sintering"
                                                data-toggle="modal" data-backdrop="false"
                                                style="display:none; margin:5px;"
                                                onclick="openModal('sintering',true)">SET
                                        </button>
                                        @break

                                    @case('pressing')
                                        <x-waiting-dialog title="Choose Furnace" btnText="SET" type="pressing"
                                                          :devices="$devices"
                                                          stageId="5"/>
                                        <button type="submit" class="btn btn-primary receiveSelectBtn pressing"
                                                data-toggle="modal"
                                                data-backdrop="false" style="display:none; margin:5px;"
                                                onclick="openModal('pressing',true)">SET
                                        </button>
                                        @break

                                    @case('delivery')
                                        <x-waiting-delivery-dialog title="Assign to"  btnText="ASSIGN"  :drivers="$drivers" stageId="5" />
                                        <button type="submit" class="btn btn-primary receiveSelectBtn delivery"
                                                data-toggle="modal"
                                                data-backdrop="false" style="display:none; margin:5px;"
                                                onclick="openModal('DeliveryDialog',false)">ASSIGN
                                        </button>
                                        @break
                                @endswitch
                                <table class="{{ $key }} waitingTable sunriseTable" style="width:100%">
                                    <thead>
                                    <tr>

                                        @if ($key == 'milling' || $key == 'sintering' || $key == '3dprinting' || $key == 'pressing' || $key == 'delivery')
                                            {{--                                              check if not empty --}}
                                            @if (count($stage['waitingCases']) != 0)
                                                <td class="no-sort">
                                                    <input type="checkbox" class="selectAllCases {{$key}}" value="0"
                                                           name="selectAllCases"
                                                           onchange="selectAll(this, '{{ $key }}')" style="    position: absolute;
    left: 2%;
    top: 5%;
    bottom: 5%;"/>
                                                </td>
                                            @endif
                                        @endif

                                        <th>Doctor</th>
                                        <th>Patient</th>
                                        <th class="deliveryDateHeader"><span
                                                class="innerSpan4Mobile">D.Date</span><span
                                                class="innerSpan4DeskTop">Delivery Date</span></th>
                                        <th>#</th>
                                        @if ($key == 'delivery')
                                            <th> Assigned To</th>
                                        @endif
                                        <th>Tags</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach ($stage['waitingCases'] as $case)
                                        <tr style="color:{{ $color }}">
                                            @php
                                                // Normalize key case
                                                $key = strtolower($key);

                                            @endphp



                                            @if ($key == 'finishing')
                                                @php
                                                    $notReadyA = false;
                                                    $abutmentsReceived = $case->abutmentsReceived();
                                                    if (!$case->allUnitsAtFinishing()) {
                                                        $notReadyA = true;
                                                    }

                                                @endphp
                                            @endif
                                            @if ($key == 'milling' || $key == '3dprinting' || $key == 'sintering' || $key == 'pressing' || $key == 'delivery')
                                                <td class="no-sort">
                                                    <input type="checkbox"
                                                           data-type="{{$key}}"
                                                           data-group-id="{{$key}}"
                                                           class="custom-control-input multipleCB {{ $key }}   checkboxes-group-{{$key}}"
                                                           value="{{ $case->id }}"
                                                           name="CheckBoxes{{ $key }}[]"
                                                           onchange="multiCBChanged('{{ $key === '3dprinting' ? '3dprinting' : $key }}', this, '{{ $case->id }}')">

                                                </td>
                                            @endif
                                            <td class="clickable" data-toggle="modal"
                                                data-target="#waitingDialog{{ $key . $case->id }}">
                                                <p class="">{{ $case->client?->name ?? 'Err404-1' }}</p>
                                            </td>
                                            <td class="clickable" data-toggle="modal"
                                                data-target="#waitingDialog{{ $key . $case->id }}">
                                                <p class="">{{ $case->patient_name }} @if ($key == 'finishing')
                                                        @if ($notReadyA)
                                                            <span
                                                                style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                                                                    Not <br>
                                                                    Ready
                                                                </span>
                                                        @endif
                                                        @if (!$abutmentsReceived)
                                                            <span
                                                                style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                                                                    Abutment <br>
                                                                    Missing
                                                                </span>
                                                        @endif
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="clickable" data-toggle="modal"
                                                data-target="#waitingDialog{{ $key . $case->id }}">
                                                <p class="">
                                                    {{ date_format(date_create($case->initDeliveryDate()), 'd-M') }}</p>
                                            </td>
                                            <td class="clickable" data-toggle="modal"
                                                data-target="#waitingDialog{{ $key . $case->id }}">
                                                <p class="">{{ $case->unitsAmount($stage['numericStage']) }}</p>
                                            </td>
                                            <!-- Assigned to for delivery stage -->
                                            @if ($key == 'delivery')
                                                <td class="clickable" data-toggle="modal"
                                                    data-target="#waitingDialog{{ $key . $case->id }}">
                                                    <p class="">
                                                        {{ $case->jobs->where('stage', $stage['numericStage'])->first()->assignedTo
                                                            ? $case->jobs->where('stage', $stage['numericStage'])->first()->assignedTo->name_initials
                                                            : 'None' }}
                                                    </p>
                                                </td>
                                            @endif
                                            <td class="clickable" data-toggle="modal"
                                                data-target="#waitingDialog{{ $key . $case->id }}">

                                                @foreach ($case->tags as $tag)
                                                    <i title="{{$tag->originalTagRecord?->text ?? ''}}"
                                                       style="color:{{$tag->originalTagRecord?->color ?? '#000'}}"
                                                       class="{{$tag->originalTagRecord?->icon ?? ''}}  fa-lg"></i>
                                                @endforeach
                                            </td>
                                        </tr>
{{--                                        old delivery dialog--}}
                                        @if ($key == 'delivery')
                                            <div class="modal fade" style="display: none" tabindex="-1" role="dialog"
                                                 id="myModal{{ $case->id }}">
                                                <form action="{{ route('assign-to-delivery-person') }}"
                                                      method="POST">
                                                    @csrf
                                                    <input type="hidden" name="case_id"
                                                           value="{{ $case->id }}">
                                                    <div class="modal-dialog modal-dialog-centered" role="document"
                                                         style="width: 30%">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    Assign case to driver</h5>
                                                                <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="false">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div>


                                                                    <div class="kt-form__control"
                                                                         style="    display: flex;     flex-direction: column;
                                                                                                align-items: center;">
                                                                        <label
                                                                            style="margin-bottom:10px !important">Delivery
                                                                            Driver:</label>
                                                                        <nav class="driversContainer">
                                                                            @foreach ($drivers as $driver)
                                                                                <br/>
                                                                                {{-- <a class="driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}"><button class="btn btn-info driverNameBtn"> --}}
                                                                                {{-- {{$driver->name_initials}} --}}
                                                                                {{-- </button></a> --}}
                                                                                <a class="btn btn-info driverNameBtn driverName"
                                                                                   href="{{ route('assign-to-delivery-person', ['driver_user' => $driver->id, 'case_id' => $case->id]) }}">
                                                                                    {{ $driver->name_initials }}
                                                                                </a>
                                                                            @endforeach
                                                                        </nav>
                                                                    </div>


                                                                </div>

                                                            </div>
                                                            <div class="modal-footer fullBtnsWidth"
                                                                 style="padding: 0px 10px 3px 10px !important">
                                                                {{-- <button type="submit" --}}
                                                                {{-- class="btn btn-primary"> --}}
                                                                {{-- Assign --}}
                                                                {{-- </button> --}}
                                                                <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        {{--                                        {{dd("key  is  " . $key)}} --}}
                                        {{-- BEGIN WAITING DIALOG --}}
                                        <div class="modal fade" tabindex="-1" role="dialog"
                                             id="waitingDialog{{ $key . $case->id }}">
                                            <form
                                                action="{{ $key == 'delivery' ? route('delivery-accept', $case->id) : route('assign-to-me', ['caseId' => $case->id, 'stage' => $stage['numericStage']]) }}"
                                                method="GET">
                                                @csrf
                                                <input type="hidden" name="case_id" value="{{ $case->id }}">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Case Completion</h5>
                                                            @if (Auth()->user()->is_admin)
                                                                <div class="tooltipY">
                                                                    <a
                                                                        href="{{ route('finish-case-completely', ['caseId' => $case->id]) }}">
                                                                        <i class="fa-solid fa-forward-fast close "></i>
                                                                    </a>
                                                                    <span class="tooltiptextY">Skip To Delivery
                                                                            Stage</span>
                                                                </div>
                                                            @endif
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="false">&times;</span>
                                                            </button>

                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group row" style="margin-bottom: 0px">
                                                                <div class="form-group col-6 "
                                                                     style="margin-bottom: 0px">
                                                                    <label for="doctor">Doctor: </label>
                                                                    <h5 id="doctor">
                                                                        <b>{{ $case->client?->name }}</b></h5>
                                                                </div>
                                                                <div class="form-group col-6 "
                                                                     style="margin-bottom: 0px">
                                                                    <label for="pat">Patient: </label>
                                                                    <h5 id="pat">
                                                                        <b>{{ $case->patient_name }}</b></h5>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="form-group row">
                                                                <div class=" col-12 ">
                                                                    <label><b>Jobs:</b></label><br>


                                                                    @foreach ($case->jobs->where('stage', $stage['numericStage']) as $job)
                                                                        @php
                                                                            $unit = explode(', ', $job->unit_num);
                                                                        @endphp

                                                                        <span>{{ $job->unit_num }}
                                                                                -
                                                                                {{ $job->jobType->name ?? 'No Job Type' }}
                                                                                -
                                                                                {{ $job->material->name ?? 'no material' }}
                                                                            {{ $job->color == '0' ? '' : ' - ' . $job->color }}
                                                                            {{ $job->style == 'None' ? '' : ' - ' . $job->style }}
                                                                            {{ isset($job->implantR) && $job->jobType->id == 6 ? ' - Implant Type: ' . $job->implantR->name : '' }}
                                                                                <br>
                                                                                {{ isset($job->abutmentR) && $job->jobType->id == 6 ? ' Abutment Type: ' . $job->abutmentR->name : '' }}
                                                                            </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            @if (count($case->notes) > 0)
                                                                <hr>
                                                                <label><b>Notes:</b></label><br>
                                                                @foreach ($case->notes as $note)
                                                                    <div class="form-control"
                                                                         style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px"
                                                                         disabled>

                                                                            <span
                                                                                class="noteHeader">{{ '[' . substr($note->created_at, 0, 16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>

                                                                        <span
                                                                            class="noteText">{{ $note->note }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer fullBtnsWidth">
                                                            <div class="row btnsRow"
                                                                 style=" margin-right: 0px; margin-left: 0px;width:100%">
                                                                <div class="col-md-3 col-sm-12 padding5px">
                                                                    <a
                                                                        href="{{ route('view-case', ['id' => $case->id, 'stage' => $stage['numericStage']]) }}">
                                                                        <button type="button"
                                                                                class="btn btn-info "><i
                                                                                class="fas fa-eye"></i> View
                                                                        </button>
                                                                    </a>
                                                                </div>

                                                                @if ($key == 'milling')
                                                                    <div class="col-md-6 col-sm-12 padding5px">
                                                                        <a>
                                                                            <button type="button" data-toggle="modal"
                                                                                    class="btn btn-success"
                                                                                    data-dismiss="modal"
                                                                                    onclick="openModal('milling',true)">
                                                                                <i
                                                                                    class="fa-solid fa-hexagon-nodes"></i>
                                                                                Nest
                                                                            </button>
                                                                        </a>
                                                                    </div>
                                                                @elseif ($key == '3dprinting' || $key == 'sintering' || $key == 'pressing')
                                                                    <div class="col-md-6 col-sm-12 padding5px">
                                                                        <button type="button" class="btn btn-success"
                                                                                data-dismiss="modal"
                                                                                onclick="openModal('{{ $key }}',true,'{{ $case->id }}')">
                                                                            <i class="fas fa-user-plus"></i> Assign To
                                                                            Me..
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <div class="col-md-6 col-sm-12 padding5px">
                                                                        <button type="submit" class="btn btn-success"
                                                                                style="width:100%"><i
                                                                                class="fas fa-user-plus"></i>
                                                                            {{ $key == 'delivery' ? (trans('ui.dom')['Take'] ?? 'Take') : (trans('ui.dom')['Assign To Me'] ?? 'Assign To Me') }}
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-3 col-sm-12 padding5px"><a
                                                                        href="{{ route('edit-case-view', $case->id) }}">
                                                                        <button type="button"
                                                                                class="btn btn-warning "
                                                                            {{ $canEditCase ? '' : 'disabled' }}>
                                                                            <i class="fas fa-edit"></i> Edit Case
                                                                        </button>
                                                                    </a></div>
                                                                @if ($key == 'qc')
                                                                    <div class="col-12 padding5px">
                                                                        <a
                                                                            href="{{ route('assign-and-finish', ['caseId' => $case->id, 'stage' => $stage['numericStage']]) }}">
                                                                            <button type="button"
                                                                                    class="btn btn-info ">
                                                                                <i
                                                                                    class="fa-solid fa-arrow-trend-up"></i>Complete
                                                                            </button>
                                                                        </a>
                                                                    </div>
                                                                @endif


                                                                @if ($key == 'delivery')
                                                                    @if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 129)))
                                                                        @if ($case->jobs[0]->assignee == null)
                                                                            <div class="col-12 padding5px">
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal{{ $case->id }} ">
                                                                                    <button type="button"
                                                                                            data-dismiss="modal"
                                                                                            class="btn btn-warning"
                                                                                            onclick="openModal('DeliveryDialog',false)">
                                                                                        Assign to..
                                                                                    </button>
                                                                                </a>
                                                                            </div>
                                                                        @else
                                                                            <div class="col-12 padding5px">
                                                                                <a data-toggle="modal"
                                                                                   data-target="#myModal{{ $case->id }},true,{{ $case->id }}">
                                                                                    <button type="button"
                                                                                            data-dismiss="modal"
                                                                                            class="btn btn-warning">
                                                                                        Re-Assign..
                                                                                    </button>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                                @if ($key == 'delivery')
                                                                    <div class="col-12 padding5px">
                                                                        <a
                                                                            href="{{ route('view-voucher', $case->id) }}">
                                                                            <button type="button"
                                                                                    class="btn btn-info ">
                                                                                <i class="fas fa-print"></i> Print
                                                                                Voucher
                                                                            </button>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div class="col-12 padding5px">
                                                                    <button type="button" class="btn btn-secondary "
                                                                            data-dismiss="modal" style="width:100%">
                                                                        Cancel
                                                                    </button>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach


                                    <!-- Begin Active tab -->
                                    </tbody>
                                </table>
                            </div>

                            {{-- ----------------ACTIVE TABLE--------------- --}}
                            {{-- ----------------ACTIVE TABLE--------------- --}}
                            {{-- ----------------ACTIVE TABLE--------------- --}}
                            <div tabindex="0" role="tabpanel" aria-labelledby="{{ 'active-' . $key . 'label' }}"
                                 id="{{ 'active-' . $key }}" hidden>
                                @php
                                    $key = strtolower($key);

                                    $millingActiveDialogBuilt = false;
                                    $sinteringActiveDialogBuilt = false;
                                    $printingActiveDialogBuilt = false;
                                    $pressingActiveDialogBuilt = false;

                                @endphp
                                @if ($key == 'milling' || $key == '3dprinting' || $key == 'sintering' || $key == 'pressing')
                                    <!-- ACTIVE DELIVERY TABLES -->
                                    <!-- ACTIVE DELIVERY TABLES -->
                                    <!-- ACTIVE DELIVERY TABLES -->
                                    <table class=" activeTable sunriseTable" style="width:100%;">
                                        <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Patient</th>
                                            <th class="deliveryToHeader">Delivery Date</th>
                                            <th class="assignedToHeader">Assigned To</th>
                                            <th class="">#</th>
                                            <th class="">Tags</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($stage['activeCases'] as $case)
                                            <tr class="clickable" style="color:{{ $color }}"
                                                data-toggle="modal"
                                                data-target="#confirmCompletion{{ $key . $case->id }}">
                                                @if ($key == 'finishing')
                                                    @php
                                                        $notReadyA = false;
                                                        $abutmentsReceived = $case->abutmentsReceived();
                                                        if (!$case->allUnitsAtFinishing()) {
                                                            $notReadyA = true;
                                                        }
                                                    @endphp
                                                @endif
                                                <td>
                                                    <p class="">{{ $case->client->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="">{{ $case->patient_name }} @if ($key == 'finishing')
                                                            @if ($notReadyA)
                                                                <span
                                                                    style="float:right;margin-left: 5px; line-height: 1;color:#ffa400;font-size: 9px;">
                                                                        Not <br>
                                                                        Ready
                                                                    </span>
                                                            @endif

                                                            @if (!$abutmentsReceived)
                                                                <span
                                                                    style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">
                                                                        Abutment <br>
                                                                        Missing
                                                                    </span>
                                                            @endif
                                                        @endif

                                                    </p>
                                                </td>
                                                <td class="">
                                                    <p class="">
                                                        {{ date_format(date_create($case->initDeliveryDate()), 'd-M') }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="">
                                                        {{ $case->jobs->where('stage', $stage['numericStage'])->first() ? ($case->jobs->where('stage', $stage['numericStage'])->first()->assignedTo ? $case->jobs->where('stage', $stage['numericStage'])->first()->assignedTo->name_initials : 'None') : 'None' }}
                                                    </p>
                                                </td>
                                                <td class="">
                                                    <p class="">{{ $case->unitsAmount($stage['numericStage']) }}
                                                    </p>
                                                </td>
                                                <td class="">

                                                    @foreach ($case->tags as $tag)
                                                        <i title="{{$tag->originalTagRecord?->text ?? ''}}"
                                                           style="color:{{$tag->originalTagRecord?->color ?? '#000'}}"
                                                           class="{{$tag->originalTagRecord?->icon ?? ''}}  fa-lg"></i>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <!-- End Active tab -->




                                            <!-- Active case actions Dialog -->
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                 id="confirmCompletion{{ $key . $case->id }}">
                                                <form
                                                    action="{{ $key == 'delivery' ? route('delivery-accept', $case->id) : route('finish-case', ['caseId' => $case->id, 'stage' => $stage['numericStage']]) }}"
                                                    method="GET">
                                                    @csrf
                                                    <input type="hidden" name="case_id"
                                                           value="{{ $case->id }}">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Case Completion</h5>

                                                                <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="false">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div class="form-group row"
                                                                     style="margin-bottom: 0px">
                                                                    <div class="form-group col-6 "
                                                                         style="margin-bottom: 0px">
                                                                        <label for="doctor">Doctor: </label>
                                                                        <h5 id="doctor">
                                                                            <b>{{ $case->client?->name }}</b>
                                                                        </h5>
                                                                    </div>
                                                                    <div class="form-group col-6 "
                                                                         style="margin-bottom: 0px">
                                                                        <label for="pat">Patient: </label>
                                                                        <h5 id="pat">
                                                                            <b>{{ $case->patient_name }}</b></h5>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="form-group row">
                                                                    <div class=" col-12 ">
                                                                        <label><b>Jobs:</b></label><br>


                                                                        @foreach ($case->jobs->where('stage', $stage['numericStage']) as $job)
                                                                            @php
                                                                                $unit = explode(
                                                                                    ', ',
                                                                                    $job->unit_num,
                                                                                );
                                                                            @endphp

                                                                            <span>{{ $job->unit_num }}
                                                                                    -
                                                                                    {{ $job->jobType->name ?? 'No Job Type' }}
                                                                                    -
                                                                                    {{ $job->material->name ?? 'no material' }}
                                                                                {{ $job->color == '0' ? '' : ' - ' . $job->color }}
                                                                                {{ $job->style == 'None' ? '' : ' - ' . $job->style }}
                                                                                {{ isset($job->implantR) && $job->jobType->id == 6 ? ' - Implant Type: ' . $job->implantR->name : '' }}
                                                                                    <br>
                                                                                    {{ isset($job->abutmentR) && $job->jobType->id == 6 ? ' Abutment Type: ' . $job->abutmentR->name : '' }}
                                                                                </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                @if (count($case->notes) > 0)
                                                                    <hr>
                                                                    <label><b>Notes:</b></label><br>
                                                                    @foreach ($case->notes as $note)
                                                                        <div class="form-control"
                                                                             style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px"
                                                                             disabled>

                                                                                <span
                                                                                    class="noteHeader">{{ '[' . substr($note->created_at, 0, 16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>
                                                                            <span
                                                                                class="noteText">{{ $note->note }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                @endif

                                                            </div>
                                                            <div class="modal-footer fullBtnsWidth">
                                                                <div class="row btnsRow"
                                                                     style=" margin-right: 0px; margin-left: 0px;width:100%">
                                                                    @if ($key == 'delivery')
                                                                        <div class="col-12 padding5px">

                                                                            <a class="dropdown-item"
                                                                               href="{{ route('delivered-in-box', $case->id) }}">
                                                                                <button type="button"
                                                                                        class="btn btn-outline-info"
                                                                                        style="width:100%">Delivered In
                                                                                    Box
                                                                                </button>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-3 padding5px">
                                                                        <a
                                                                            href="{{ route('view-case', ['id' => $case->id, 'stage' => $stage['numericStage']]) }}">
                                                                            <button type="button"
                                                                                    class="btn btn-info ">
                                                                                <i class="fas fa-eye"></i> View
                                                                            </button>
                                                                        </a>
                                                                    </div>

                                                                    <div class="col-6 padding5px">
                                                                        @php
                                                                            $isAdmin = Auth()->user()->is_admin;
                                                                            $canBeFinished = true;
                                                                            $isUserCase = false;
                                                                            $canComplete = false;
                                                                            if (
                                                                                $case->jobs
                                                                                    ->where(
                                                                                        'stage',
                                                                                        $stage['numericStage'],
                                                                                    )
                                                                                    ->first() &&
                                                                                $case->jobs
                                                                                    ->where(
                                                                                        'stage',
                                                                                        $stage['numericStage'],
                                                                                    )
                                                                                    ->first()->assignee ==
                                                                                    Auth()->user()->id
                                                                            ) {
                                                                                $canComplete = true;
                                                                                $isUserCase = true;
                                                                            }
                                                                            if ($key == 'finishing') {
                                                                                if (
                                                                                    $notReadyA ||
                                                                                    !$abutmentsReceived
                                                                                ) {
                                                                                    $canComplete = false;
                                                                                    $canBeFinished = false;
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        @if ($isAdmin && $canBeFinished && !$isUserCase)
                                                                            <a class=""
                                                                               href="{{ route('complete-by-admin', ['id' => $case->id, 'stage' => $stage['numericStage']]) }}">
                                                                                <button type="button"
                                                                                        class="btn btn-success">Override
                                                                                    Complete
                                                                                </button>
                                                                            </a>
                                                                        @else
                                                                            <button type="submit"
                                                                                    class="btn btn-success"
                                                                                    style="width:100%"
                                                                                {{ $canComplete ? '' : 'disabled' }}>{{ $canComplete ? 'Complete' : 'Case cannot be completed' }}</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-3 padding5px"><a
                                                                            href="{{ route('edit-case-view', $case->id) }}">
                                                                            <button type="button"
                                                                                    class="btn btn-warning "
                                                                                {{ $canEditCase ? '' : 'disabled' }}>
                                                                                Edit Case
                                                                            </button>
                                                                        </a></div>

                                                                    @if ($key == 'milling')
                                                                        <div class="col-12 padding5px">
                                                                            <button type="button"
                                                                                    class="btn btn-dark "
                                                                                    data-toggle="modal"
                                                                                    data-target="#MEX{{ $case->id }}"
                                                                                    data-dismiss="modal"
                                                                                    style="width:100%">
                                                                                Externally Milled
                                                                            </button>
                                                                        </div>
                                                                </div>
                                                                @endif
                                                                @if ($key == 'delivery')
                                                                    <div class="col-12 padding5px">

                                                                        <a class="dropdown-item"
                                                                           href="{{ route('view-voucher', $case->id) }}">
                                                                            <button type="button"
                                                                                    class="btn btn-outline-info">Print
                                                                                voucher
                                                                            </button>
                                                                        </a>
                                                                    </div>
                                                                    @if ($case->delivered_to_client == 1)
                                                                        @if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 9)))
                                                                            <div class="col-12 padding5px">
                                                                                <a class="dropdown-item"
                                                                                   href="{{ route('receive-voucher', $case->id) }}">
                                                                                    <button type="button"
                                                                                            class="btn btn-outline-secondary">
                                                                                        Receive Voucher
                                                                                    </button>
                                                                                </a>

                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                                <div class="col-12 padding5px">
                                                                    <a class=""
                                                                       href="{{ route('reset-to-waiting', ['id' => $case->id, 'stage' => $stage['numericStage']]) }}">
                                                                        <button type="button"
                                                                                class="btn btn-outline-danger">Reset To
                                                                            Waiting
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                                <div class="col-12 padding5px">
                                                                    <button type="button" class="btn btn-secondary "
                                                                            data-dismiss="modal"
                                                                            style="width:100%">
                                                                        Cancel
                                                                    </button>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                            {{--                                            /////////// v2 DIALOG --}}
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach



                @foreach ($devices as $device)
                    @switch($device->type)
                        @case(2)
                            <x-active-cases-dialog title="Milling Jobs" btnText="COMPLETE" type="milling"
                                                   :deviceId="$device->id"
                                                   :isBuilds="false"/>
                            @break

                        @case(3)
                            <x-active-cases-dialog title="Printer Builds" btnText="COMPLETE" type="3dprinting"
                                                   :deviceId="$device->id"
                                                   :isBuilds="true"/>
                            @break

                        @case(4)
                            <x-sintering-cases-list :sinteringCases="$sinteringCases"  title="Sintering Jobs" btnText="COMPLETE" type="sintering"
                                          deviceId="$device->id"
                                         :inactiveSinteringCases="$inactiveSinteringCases" :sinteringCases="$sinteringCases" :isBuilds="true" />
                            @break
                        @case(5)
                            <x-active-cases-dialog title="Pressing Jobs" btnText="COMPLETE" type="pressing"
                                                   :deviceId="$device->id"
                                                   :isBuilds="false"/>
                            @break

                        @default
                            @break
                    @endswitch
                @endforeach
            </div>
        </main>
    </div>



    <!-- Updated hidden forms for all operations -->
    <div class="d-none">
        @php $stagesWithDialogs = ["3dprinting", "milling", "sintering", "pressing", "delivery"]; @endphp
        @foreach ($stagesWithDialogs as $stage)
            <form id="hiddenForm{{ $stage }}" action="#" method="POST">
                @csrf
                <input type="hidden" name="deviceId-{{ $stage }}" id="deviceId-{{ $stage }}"
                       value="">
                <input type="hidden" name="type" value="{{ $stage }}">
                <input type="hidden" name="WaitingPopupCheckBoxes{{ $stage }}[]"
                       id="WaitingPopupCheckBoxes{{ $stage }}" value="">

                @if ($stage == '3dprinting')
                    <input type="hidden" name="buildName" id="hidden3dprintingBuildName" value="">
                @endif
            </form>
        @endforeach

        <!-- Hidden form for case ID from waiting dialog -->
        <input type="hidden" id="caseIdFromWaitingDialog" name="caseIdFromWaitingDialog" value="">

        <!-- Generic loading dialog -->
        <div id="loadingDialog" class="modal" tabindex="-1" role="dialog"
             style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="mb-0 mt-2">Processing your request...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--    @php--}}
    {{--        } catch (Exception $e) {--}}
    {{--            dd($e->getMessage(), $e->getTraceAsString());--}}
    {{--        }--}}
    {{--    @endphp--}}
@endsection



@push('js')
    <!-- Make sure jQuery is loaded first -->


    <!-- Then load Macaw Tabs -->
    <script src="{{ asset('https://cdn.jsdelivr.net/gh/htmlcssfreebies/macaw-tabs@v1.0.4/dist/js/macaw-tabs.js') }}"></script>

    <!-- Then load your custom scripts -->
    <script src="{{ asset('assets') }}/js/ysh-custom-js/v3scripts.js"></script>
    <script src="{{ asset('assets') }}/js/ysh-custom-js/operationsDashboardJS.js"></script>
    <script>
        function setOuterTab(element) {
            // Remove active class from all tab buttons
            $('.stageSidebar button[role="tab"]').attr('aria-selected', 'false');

            // Set current tab as selected
            $(element).attr('aria-selected', 'true');

            // Hide all tab panels
            $('div[role="tabpanel"]').attr('hidden', true);

            // Get the target panel ID
            var targetPanelId = $(element).attr('aria-controls');

            // Show the corresponding panel
            $('#' + targetPanelId).removeAttr('hidden');

            // Reinitialize Macaw Tabs to ensure proper functionality
            // if (typeof MacawTabs !== 'undefined') {
            //     MacawTabs.init();
            // }
        }
    </script>
    <script>
        // Apply device image configuration when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Macaw Tabs if available
            if (typeof MacawTabs !== 'undefined') {
                MacawTabs.init();
            }

            // Apply device image styling from configuration
            if (typeof jQuery !== 'undefined') {
                // Apply to all device images
                jQuery('.device-item img').css({
                    'width': '{{ $deviceConfig["width"] }}',
                    'max-width': '{{ $deviceConfig["max_width"] }}',
                    'height': '{{ $deviceConfig["height"] }}',
                    'padding': '{{ $deviceConfig["padding"] }}',
                    'border-radius': '{{ $deviceConfig["border_radius"] }}',
                    'background': '{{ $deviceConfig["background"] }}',
                    'object-fit': 'contain'
                });

                // Apply to device containers
                jQuery('.device-container').css({
                    'gap': '{{ $deviceConfig["container_gap"] ?? "15px" }}',
                    'width': '100%'
                });

                // Remove hover effects if disabled in config
                @if(!$deviceConfig["hover_effect"])
                jQuery('.device-item').hover(
                    function() {
                        jQuery(this).css({
                            'box-shadow': 'none',
                            'transform': 'none'
                        });
                    },
                    function() {
                        jQuery(this).css({
                            'box-shadow': 'none',
                            'transform': 'none'
                        });
                    }
                );
                @endif

                // Ensure images don't break out of containers
                jQuery('.device-item').css({
                    'overflow': 'hidden',
                    'max-width': '{{ $deviceConfig["max_width"] }}'
                });

                // Setup dialog reset functionality
                setupDialogResetHandlers();
            }
        });

        /**
         * Setup handlers to reset dialogs to their original state when closed
         */
        function setupDialogResetHandlers() {
            // Store original states when a modal is opened
            jQuery('.modal').on('show.bs.modal', function() {
                var $modal = jQuery(this);

                // Store original button states
                $modal.find('button').each(function() {
                    var $btn = jQuery(this);
                    $btn.data('original-disabled', $btn.prop('disabled'));
                    $btn.data('original-text', $btn.text());
                    $btn.data('original-class', $btn.attr('class'));
                });

                // Store original input values
                $modal.find('input, textarea, select').each(function() {
                    var $input = jQuery(this);
                    $input.data('original-value', $input.val());
                    $input.data('original-checked', $input.prop('checked'));
                    $input.data('original-disabled', $input.prop('disabled'));
                });

                // Store original image states
                $modal.find('img').each(function() {
                    var $img = jQuery(this);
                    $img.data('original-src', $img.attr('src'));
                    $img.data('original-class', $img.attr('class'));
                    $img.data('original-style', $img.attr('style'));
                });

                // Store device selection states
                $modal.find('.device-item').each(function() {
                    var $device = jQuery(this);
                    $device.data('original-class', $device.attr('class'));
                    $device.data('original-style', $device.attr('style'));
                    $device.data('original-selected', $device.hasClass('selected'));
                });

                // Store checkbox states
                $modal.find('input[type="checkbox"]').each(function() {
                    var $checkbox = jQuery(this);
                    $checkbox.data('original-checked', $checkbox.prop('checked'));
                });
            });

            // Reset to original state when a modal is hidden
            jQuery('.modal').on('hidden.bs.modal', function() {
                var $modal = jQuery(this);

                // Reset buttons
                $modal.find('button').each(function() {
                    var $btn = jQuery(this);
                    if ($btn.data('original-disabled') !== undefined) {
                        $btn.prop('disabled', $btn.data('original-disabled'));
                    }
                    if ($btn.data('original-text')) {
                        $btn.text($btn.data('original-text'));
                    }
                    if ($btn.data('original-class')) {
                        $btn.attr('class', $btn.data('original-class'));
                    }
                });

                // Reset inputs
                $modal.find('input, textarea, select').each(function() {
                    var $input = jQuery(this);
                    if ($input.data('original-value') !== undefined) {
                        $input.val($input.data('original-value'));
                    }
                    if ($input.data('original-checked') !== undefined) {
                        $input.prop('checked', $input.data('original-checked'));
                    }
                    if ($input.data('original-disabled') !== undefined) {
                        $input.prop('disabled', $input.data('original-disabled'));
                    }
                });

                // Reset images
                $modal.find('img').each(function() {
                    var $img = jQuery(this);
                    if ($img.data('original-src')) {
                        $img.attr('src', $img.data('original-src'));
                    }
                    if ($img.data('original-class')) {
                        $img.attr('class', $img.data('original-class'));
                    }
                    if ($img.data('original-style')) {
                        $img.attr('style', $img.data('original-style'));
                    }
                });

                // Reset device selections
                $modal.find('.device-item').each(function() {
                    var $device = jQuery(this);
                    if ($device.data('original-class')) {
                        $device.attr('class', $device.data('original-class'));
                    }
                    if ($device.data('original-style')) {
                        $device.attr('style', $device.data('original-style'));
                    }

                    // Handle selected state
                    if ($device.data('original-selected') === true) {
                        $device.addClass('selected');
                    } else {
                        $device.removeClass('selected');
                    }
                });

                // Reset checkboxes
                $modal.find('input[type="checkbox"]').each(function() {
                    var $checkbox = jQuery(this);
                    if ($checkbox.data('original-checked') !== undefined) {
                        $checkbox.prop('checked', $checkbox.data('original-checked'));
                    }
                });

                // Reset any "Select All" checkboxes
                $modal.find('.selectAllCases').prop('checked', false);

                // Clear any error messages
                $modal.find('.alert, .error-message').remove();

                // Reset any custom form data
                if (typeof resetCustomFormData === 'function') {
                    resetCustomFormData($modal);
                }
            });
        }

        /**
         * Reset custom form data specific to certain dialogs
         * This can be extended for specific dialog types
         */
        function resetCustomFormData($modal) {
            // Reset build name for 3D printing
            if ($modal.find('#hidden3dprintingBuildName').length) {
                $modal.find('#hidden3dprintingBuildName').val('');
            }

            // Reset device selection
            if (typeof selectedMachineId !== 'undefined') {
                selectedMachineId = null;
            }

            // Reset any selected cases arrays
            if (typeof selectedCases !== 'undefined') {
                selectedCases = [];
            }

            // Reset any waiting popup checkboxes
            jQuery('[id^="WaitingPopupCheckBoxes"]').val('');

            // Reset any device-specific selections
            jQuery('.device-item').removeClass('selected');
            jQuery('.device-item img').removeClass('selected');
        }
    </script>
@endpush
