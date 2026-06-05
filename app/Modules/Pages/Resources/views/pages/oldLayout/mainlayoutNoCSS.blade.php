
<html lang="en">
<head>

    @include('oldLayout.partials.head')
    @yield("head")
    @include('oldLayout.partials.header')


</head>


<body>
@include('oldLayout.partials.KorvexSidebar')

<!-- body content start-->
    <div class="body-content">

            @yield('content')

            <!--end Right Slidebar-->
            <!-- <footer class="footer">
                 2021 &copy; alsolent LAB.
             </footer>-->
            <!--footer section end-->
        </div><!--end container-->





    <!--end body content-->
    <!--footer section start-->



</body>
@include('oldLayout.partials.footer-scripts')

@yield('scripts')
</html>
