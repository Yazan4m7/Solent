@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.edit_account') }}</h3>
<form method="POST" action="{{ route('financing.accounts.update', $account->id) }}" class="finance-form">
    @csrf
    @include('financing.accounts._form')
</form>
@endsection
