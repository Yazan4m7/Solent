@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between mb-3">
<div>
<h3>{{ $supplier->name }}</h3>
<div class="text-muted">{{ $supplier->phone }} {{ $supplier->email }}</div>
<div class="finance-balance mt-2">{{ __('financing::financing.balance_owed') }}: {{ number_format($supplier->balance_owed,2) }} {{ config('modules.financing.currency','JOD') }}</div>
</div>
<div>
<a href="{{ route('financing.bills.create',['supplier_id'=>$supplier->id]) }}" class="btn btn-primary">{{ __('financing::financing.add_bill') }}</a>
<a href="{{ route('financing.suppliers.edit',$supplier->id) }}" class="btn btn-outline-primary">{{ __('financing::financing.edit') }}</a>
</div>
</div>
<div class="finance-table-wrap">
<table class="table table-sm">
<thead><tr><th>#</th><th>{{ __('financing::financing.amount') }}</th><th>{{ __('financing::financing.paid_amount') }}</th><th>{{ __('financing::financing.due_date') }}</th><th>{{ __('financing::financing.status') }}</th></tr></thead>
<tbody>
@foreach($bills as $bill)
<tr class="{{ $bill->is_overdue ? 'finance-overdue' : '' }}">
<td><a href="{{ route('financing.bills.show',$bill->id) }}">#{{ $bill->id }}</a></td>
<td>{{ number_format($bill->amount,2) }}</td>
<td>{{ number_format($bill->paid_amount,2) }}</td>
<td>{{ $bill->due_date->format('d/m/Y') }}</td>
<td>{{ ucfirst($bill->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
