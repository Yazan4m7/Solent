<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    @include('components.i18n-assets')
    @include('layout.partials.head')
    @yield("head")


</head>
<body class="sticky-header">

<section>


<!-- body content start-->
    <div class="body-content">
        <!-- header section start-->
        @if (config('site_vars.environment') == 'testing')
        <div style="pointer-events: none;opacity:0.2;z-index:10000;position: fixed;bottom: 30px;right:20px;color:red;font-size: 55px;font-weight: bolder"> {{strtoupper(config('site_vars.environment') . ' ' . 'environment') }}</div>
        @endif
        <div class="container-fluid">



            @yield('content')

            <!--end Right Slidebar-->
            <!-- <footer class="footer">
                 2021 &copy; Korvex LAB.
             </footer>-->
            <!--footer section end-->
        </div><!--end container-->





    </div>
    <!--end body content-->
    <!--footer section start-->

</section>
</body>
@include('layout.partials.footer-scripts')


@yield('scripts')
</html>
