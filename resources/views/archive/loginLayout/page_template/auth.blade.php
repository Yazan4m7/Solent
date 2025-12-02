
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@include('loginLayout.navbars.sidebar')
<div class="main-panel">
    @include('loginLayout.navbars.navs.auth')
    @yield('content')
    @include('loginLayout.footer')
</div>
