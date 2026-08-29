@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/financing.css') }}">
@endpush

@section('content')
<div class="container-fluid financing-module" dir="auto">
    @include('financing.partials.navigation')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('finance-content')
</div>
@endsection
