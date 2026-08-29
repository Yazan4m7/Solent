@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.add_expense') }}</h3>
<form method="POST" action="{{ route('financing.expenses.store') }}" enctype="multipart/form-data" class="finance-form">
    @csrf
    @include('financing.expenses._form')
</form>
@endsection
