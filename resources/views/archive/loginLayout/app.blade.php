
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <!-- Extra details for Live View on GitHub Pages -->
  <title>
    Korvex
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" />
  <link href="{{ asset('assets') }}/css/now-ui-dashboard.css?v=1.3.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->

  <style>
    .wrapper { height:auto !important;}
    .full-page>.content {padding-bottom: 50px !important;padding-top: 50px !important;}
    .section-image {
      background: linear-gradient(0deg,rgba(44,44,44,.2),rgba(24,206,15,.4));
    }
    .btn-block{background-color: #37b44a;}
    .login-page .card-login .logo-container img {
      width:100px !important;
      max-width: initial;
    }
    .page-header:before {
      background-color: rgba(0,0,0,.3);
    }
  </style>
</head>

<body class="{{ $class ?? '' }}">
  <div class="wrapper">
    @auth
      @include('loginLayout.page_template.auth')
    @endauth
    @guest
      @include('loginLayout.page_template.guest')
    @endguest
  </div>
  <!--   Core JS Files   -->

  <script src="{{ asset('white') }}/js/core/jquery.min.js"></script>
  <script src="{{ asset('white') }}/js/core/popper.min.js"></script>
  {{--<script src="{{ asset('white') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>--}}

  <!-- Control Center  Dashboard: parallax effects, scripts for the example pages etc -->
  {{--<script src="{{ asset('assets') }}/js/now-ui-dashboard.min.js?v=1.3.0" type="text/javascript"></script>--}}
  <!--Dashboard DEMO methods, don't include it in your project! -->

  @stack('js')
</body>

</html>
