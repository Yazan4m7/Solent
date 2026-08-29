@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between mb-3">
<div>
<h3>{{ __('financing::financing.bill') }} #{{ $bill->id }}</h3>
<div>{{ optional($bill->supplier)->name }}</div>
<div class="finance-balance">{{ number_format($bill->remaining_amount,2) }} {{ config('modules.financing.currency','JOD') }} {{ __('financing::financing.remaining') }}</div>
</div>
<a href="{{ route('financing.bills.edit',$bill->id) }}" class="btn btn-outline-primary">{{ __('financing::financing.edit') }}</a>
</div>
<div class="finance-table-wrap">
<table class="table table-sm">
<thead><tr><th>{{ __('financing::financing.date') }}</th><th>{{ __('financing::financing.account') }}</th><th>{{ __('financing::financing.amount') }}</th><th>{{ __('financing::financing.notes') }}</th></tr></thead>
<tbody>
@foreach($bill->payments as $payment)
<tr><td>{{ $payment->date->format('d/m/Y') }}</td><td>{{ optional($payment->account)->name }}</td><td>{{ number_format($payment->amount,2) }}</td><td>{{ $payment->notes }}</td></tr>
@endforeach
</tbody>
</table>
</div>

@if($bill->status !== 'paid')
<hr>
<h5>{{ __('financing::financing.record_payment') }}</h5>
<form method="POST" action="{{ route('financing.bills.pay',$bill->id) }}" class="finance-inline-form">
@csrf
<select name="account_id" class="form-control" required><option value="">{{ __('financing::financing.account') }}</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select>
<input type="number" step="0.01" min="0.01" max="{{ $bill->remaining_amount }}" name="amount" class="form-control" required value="{{ $bill->remaining_amount }}">
<input type="date" name="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
<input dir="auto" name="notes" class="form-control" placeholder="{{ __('financing::financing.notes') }}">
<button class="btn btn-success">{{ __('financing::financing.pay') }}</button>
</form>
@endif
@endsection
