@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.my_collections') }}</h3>
<div class="finance-table-wrap">
<table class="table table-sm">
<thead><tr><th>{{ __('financing::financing.invoice') }}</th><th>{{ __('financing::financing.collected') }}</th><th>{{ __('financing::financing.submitted') }}</th><th>{{ __('financing::financing.gap') }}</th><th></th></tr></thead>
<tbody>
@foreach($rows as $row)
<tr>
<td>#{{ $row->invoice_id }}</td>
<td>{{ number_format($row->collected_amount,2) }}</td>
<td>{{ number_format($row->submitted_amount,2) }}</td>
<td>{{ number_format($row->outstanding_gap,2) }}</td>
<td>
@if(!$row->is_fully_submitted)
<form method="POST" action="{{ route('financing.collections.mine.submit',$row->id) }}" class="finance-inline-form">
@csrf
<input type="number" step="0.01" min="0.01" max="{{ $row->collected_amount }}" name="submitted_amount" class="form-control form-control-sm" value="{{ $row->collected_amount }}" required>
<select name="account_id" class="form-control form-control-sm" required><option value="">{{ __('financing::financing.account') }}</option>@foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select>
<button class="btn btn-sm btn-success">{{ __('financing::financing.submit') }}</button>
</form>
@else
<span class="badge badge-success">{{ __('financing::financing.reconciled') }}</span>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
