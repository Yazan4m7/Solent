<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="description" content="">
<meta name="author" content="Mannat Themes">
<meta name="keyword" content="">
<link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />


<title>{{ $brandingName ?? 'Korvex' }}</title>

<link rel="stylesheet" href="{{asset('https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css')}}" media="all" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l')" crossorigin="anonymous">

<link rel="stylesheet" href="{{asset('https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css')}}">
<link href="{{asset('assets/css/fontawesome-iconpicker.css')}}" rel="stylesheet">

<!-- Theme icon -->
<link rel="shortcut icon" href="{{ asset($brandingFaviconPath ?? config('branding.defaults.favicon_path')) }}?v=20260603-favwhite1">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/css/sweetalert2.min.css')}}">
<!-- Theme Css -->
<link href="{{asset('assets/css/slidebars.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
<link href="{{asset('assets/icons/css/themify-icons.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/menu.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('assets/css/style.css')}}" rel="stylesheet">

<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: var(--color-primary-teal, #22B8A5);
    }

    input:focus + .slider {
        box-shadow: 0 0 1px var(--color-primary-teal, #22B8A5);
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    .reOverlay {
        position: absolute;
        background: rgba(0, 0, 0, 0.7);
        left: 0em;
        right: 0em;
        height: 100%;
        text-align: center;
        color:white;
        font-weight: bold;
    }


    tr {
        position: relative;
    }
</style>
<link href="{{ asset('assets/css/site-typography.css') }}" rel="stylesheet">
