@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between">
    <h3>{{ __('financing::financing.edit_expense') }}</h3>
    <form method="POST" action="{{ route('financing.expenses.destroy', $expense->id) }}" onsubmit="return confirm('{{ __('financing::financing.confirm_delete') }}')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">{{ __('financing::financing.delete') }}</button>
    </form>
</div>
<form method="POST" action="{{ route('financing.expenses.update', $expense->id) }}" enctype="multipart/form-data" class="finance-form">
    @csrf
    @include('financing.expenses._form')
</form>
@endsection
