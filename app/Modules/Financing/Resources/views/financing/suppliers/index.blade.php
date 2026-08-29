@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>{{ __('financing::financing.suppliers') }}</h3>
    <a href="{{ route('financing.suppliers.create') }}" class="btn btn-primary">{{ __('financing::financing.add_supplier') }}</a>
</div>
<div class="finance-table-wrap">
<table class="table table-sm table-hover">
<thead><tr><th>{{ __('financing::financing.name') }}</th><th>{{ __('financing::financing.phone') }}</th><th>{{ __('financing::financing.payment_terms') }}</th><th>{{ __('financing::financing.bills') }}</th></tr></thead>
<tbody>
@foreach($suppliers as $supplier)
<tr>
    <td><a href="{{ route('financing.suppliers.show',$supplier->id) }}">{{ $supplier->name }}</a></td>
    <td>{{ $supplier->phone }}</td>
    <td>{{ $supplier->payment_terms_days }} {{ __('financing::financing.days') }}</td>
    <td>{{ $supplier->bills_count }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $suppliers->links() }}
@endsection
