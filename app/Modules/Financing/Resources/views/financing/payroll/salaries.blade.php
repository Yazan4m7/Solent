@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.salary_config') }}</h3>
<form method="POST" action="{{ route('financing.payroll.salaries.save') }}" class="finance-form">
@csrf
<div class="finance-table-wrap">
<table class="table table-sm">
<thead><tr><th>{{ __('financing::financing.employee') }}</th><th>{{ __('financing::financing.base_salary') }}</th></tr></thead>
<tbody>
@foreach($users as $user)
<tr>
    <td>{{ $user->name }}</td>
    <td><input type="number" step="0.01" min="0" name="salaries[{{ $user->id }}]" class="form-control" value="{{ old('salaries.'.$user->id, isset($salaryMap[$user->id]) ? $salaryMap[$user->id]->base_salary : '') }}"></td>
</tr>
@endforeach
</tbody>
</table>
</div>
<button class="btn btn-primary">{{ __('financing::financing.save') }}</button>
</form>
@endsection
