@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.edit_bill') }}</h3>
<form method="POST" action="{{ route('financing.bills.update',$bill->id) }}" class="finance-form">@csrf @include('financing.bills._form')</form>
@endsection
