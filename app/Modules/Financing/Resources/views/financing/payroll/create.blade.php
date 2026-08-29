@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.new_payroll_run') }}</h3>
<form method="POST" action="{{ route('financing.payroll.store') }}" class="finance-form">
@csrf
<div class="form-row">
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.month') }}</label>
        <select name="period_month" class="form-control" required>
            @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" {{ old('period_month', now()->month) == $m ? 'selected' : '' }}>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
            @endfor
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.year') }}</label>
        <input type="number" name="period_year" class="form-control" min="2000" max="2100" required value="{{ old('period_year', now()->year) }}">
    </div>
</div>
<div class="form-group">
    <label>{{ __('financing::financing.notes') }}</label>
    <textarea dir="auto" name="notes" class="form-control">{{ old('notes') }}</textarea>
</div>
<button class="btn btn-primary">{{ __('financing::financing.create') }}</button>
</form>
@endsection
