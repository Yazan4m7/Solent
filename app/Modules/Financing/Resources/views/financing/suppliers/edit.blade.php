@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.edit_supplier') }}</h3>
<form method="POST" action="{{ route('financing.suppliers.update',$supplier->id) }}" class="finance-form">@csrf @include('financing.suppliers._form')</form>
@endsection
