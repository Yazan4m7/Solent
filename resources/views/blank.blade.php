@extends('layouts.app', ['pageSlug' => 'Welcome Screen'])

@section('content')
@php($brandLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path')))

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Switch Template</title>
    <link href="https://fonts.googleapis.com/css?family=Heebo:400,700|IBM+Plex+Sans:600" rel="stylesheet">
    <script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>
    <link href="{{ asset('assets') }}/css/blank_page_style.css" rel="stylesheet"/>
</head>
<body class="is-boxed has-animations">
<div class="body-wrap boxed-container" style="
    width: 100%;
    position: absolute;
    top: 0;
    right: 0;
        z-index: 0;
        background: #ebebeb;
">
    <header class="site-header">
        <div class="container">
            <div class="site-header-inner">
                <div class="brand header-brand">
                    <h1 class="m-0">
                        <a href="#">
                            <img class="header-logo-image asset-light" src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }}">
                            <img class="header-logo-image asset-dark" src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }}">
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-inner" style="position: absolute;
    right: 0;">
                    <div class="hero-copy">
                        <h1 class="hero-title mt-0">Welcome to {{ $brandingName ?? 'Korvex' }}</h1>
                        <p class="hero-paragraph">Click on one of the options on the left sidebar to get started, if none exists contact admin.</p>

                    </div>
                    <div class="hero-media">
                        <div class="header-illustration">
                            <img class="header-illustration-image asset-light" src="{{ asset('assets') }}/images/header-illustration-light.svg" alt="Header illustration">
                            <img class="header-illustration-image asset-dark" src="{{ asset('assets') }}/images/header-illustration-dark.svg" alt="Header illustration">
                        </div>
                        <div class="hero-media-illustration">
                            <img class="hero-media-illustration-image asset-light" src="{{ asset('assets') }}/images/hero-media-illustration-light.svg" alt="Hero media illustration">
                            <img class="hero-media-illustration-image asset-dark" src="{{ asset('assets') }}/images/hero-media-illustration-dark.svg" alt="Hero media illustration">
                        </div>
                        <div class="hero-media-container">
                            <img class="hero-media-image asset-light" src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }}">
                            <img class="hero-media-image asset-dark" src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }}">
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main>


</div>

<script src="{{ asset('assets') }}/js/main.min.js"></script>
</body>
</html>

@endsection


