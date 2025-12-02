<!DOCTYPE html>
<html lang="en">
<head>

    @include('layout.partials.head')
    @yield("head")
    @include('layout.partials.header')


</head>


<body>
@include('layout.partials.korvionSidebar')

<!-- body content start-->
    <div class="body-content">

            @yield('content')

            <!--end Right Slidebar-->
            <!-- <footer class="footer">
                 2021 &copy; korvion LAB.
             </footer>-->
            <!--footer section end-->
        </div><!--end container-->





    <!--end body content-->
    <!--footer section start-->



</body>
@include('layout.partials.footer-scripts')

@yield('scripts')
</html>