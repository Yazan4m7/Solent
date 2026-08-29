@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.add_bill') }}</h3>
<form method="POST" action="{{ route('financing.bills.store') }}" class="finance-form">@csrf @include('financing.bills._form')</form>
@endsection
