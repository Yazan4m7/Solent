@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
<h3>{{ __('financing::financing.supplier_bills') }}</h3>
<a href="{{ route('financing.bills.create') }}" class="btn btn-primary">{{ __('financing::financing.add_bill') }}</a>
</div>
<form class="finance-filter mb-3" method="GET">
<select name="supplier_id" class="form-control"><option value="">{{ __('financing::financing.all_suppliers') }}</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" {{ request('supplier_id')==$supplier->id?'selected':'' }}>{{ $supplier->name }}</option>@endforeach</select>
<label class="finance-check"><input type="checkbox" name="overdue" value="1" {{ request('overdue')?'checked':'' }}> {{ __('financing::financing.overdue_only') }}</label>
<button class="btn btn-outline-primary">{{ __('financing::financing.filter') }}</button>
</form>
<div class="finance-table-wrap">
<table class="table table-sm table-hover">
<thead><tr><th>#</th><th>{{ __('financing::financing.supplier') }}</th><th>{{ __('financing::financing.amount') }}</th><th>{{ __('financing::financing.paid_amount') }}</th><th>{{ __('financing::financing.due_date') }}</th><th>{{ __('financing::financing.status') }}</th></tr></thead>
<tbody>
@foreach($bills as $bill)
<tr class="{{ $bill->is_overdue?'finance-overdue':'' }}">
<td><a href="{{ route('financing.bills.show',$bill->id) }}">#{{ $bill->id }}</a></td>
<td>{{ optional($bill->supplier)->name }}</td>
<td>{{ number_format($bill->amount,2) }}</td>
<td>{{ number_format($bill->paid_amount,2) }}</td>
<td>{{ $bill->due_date->format('d/m/Y') }}</td>
<td>{{ ucfirst($bill->status) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $bills->links() }}
@endsection
