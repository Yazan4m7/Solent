<div class="wrapper wrapper-full-page ">
    @include('loginLayout.navbars.navs.guest')
            <div class="fue" filter-color="black" data-image="{{ asset('assets/bg.png') }}">
        @yield('content')
        @include('loginLayout.footer')
    </div>
</div>
