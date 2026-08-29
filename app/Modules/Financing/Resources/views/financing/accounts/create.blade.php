@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.add_account') }}</h3>
<form method="POST" action="{{ route('financing.accounts.store') }}" class="finance-form">
    @csrf
    @include('financing.accounts._form')
</form>
@endsection
