@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3>{{ __('financing::financing.payroll') }} {{ str_pad($run->period_month,2,'0',STR_PAD_LEFT) }}/{{ $run->period_year }}</h3>
        <span class="badge badge-{{ $run->posted_at ? 'success' : 'warning' }}">{{ $run->posted_at ? __('financing::financing.posted') : __('financing::financing.draft') }}</span>
    </div>
    <div class="finance-card-value">{{ number_format($run->total, 2) }} {{ config('modules.financing.currency', 'JOD') }}</div>
</div>

<form method="POST" action="{{ route('financing.payroll.lines.update', $run->id) }}">
@csrf
<div class="finance-table-wrap">
<table class="table table-sm">
<thead><tr>
    <th>{{ __('financing::financing.employee') }}</th>
    <th>{{ __('financing::financing.base_salary') }}</th>
    <th>{{ __('financing::financing.bonus') }}</th>
    <th>{{ __('financing::financing.deductions') }}</th>
    <th>{{ __('financing::financing.net') }}</th>
    <th>{{ __('financing::financing.notes') }}</th>
    <th></th>
</tr></thead>
<tbody>
@foreach($run->lines as $line)
<tr>
    <td>{{ optional($line->user)->name }}</td>
    <td>{{ number_format($line->base_salary, 2) }}</td>
    <td><input {{ $run->posted_at ? 'disabled' : '' }} class="form-control form-control-sm" type="number" step="0.01" min="0" name="lines[{{ $line->id }}][bonus]" value="{{ $line->bonus }}"></td>
    <td><input {{ $run->posted_at ? 'disabled' : '' }} class="form-control form-control-sm" type="number" step="0.01" min="0" name="lines[{{ $line->id }}][deductions]" value="{{ $line->deductions }}"></td>
    <td>{{ number_format($line->net, 2) }}</td>
    <td><input {{ $run->posted_at ? 'disabled' : '' }} dir="auto" class="form-control form-control-sm" name="lines[{{ $line->id }}][notes]" value="{{ $line->notes }}"></td>
    <td><a href="{{ route('financing.payroll.payslip', [$run->id, $line->id]) }}">{{ __('financing::financing.payslip') }}</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
@if(!$run->posted_at)
<button class="btn btn-primary">{{ __('financing::financing.save_changes') }}</button>
@endif
</form>

@if(!$run->posted_at && auth()->user()->is_admin)
<hr>
<form method="POST" action="{{ route('financing.payroll.post', $run->id) }}" class="finance-inline-form" onsubmit="return confirm('{{ __('financing::financing.confirm_post_payroll') }}')">
@csrf
<select name="account_id" class="form-control" required>
    <option value="">{{ __('financing::financing.pay_from_account') }}</option>
    @foreach($accounts as $account)
        <option value="{{ $account->id }}">{{ $account->name }} — {{ number_format($account->balance,2) }} {{ $account->currency }}</option>
    @endforeach
</select>
<button class="btn btn-danger">{{ __('financing::financing.post_and_lock') }}</button>
</form>
@endif
@endsection
