@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.installments') }}</h3>

<form method="GET" class="finance-filter mb-3">
    <select name="client_id" class="form-control">
        <option value="">{{ __('financing::financing.all_clients') }}</option>
        @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
        @endforeach
    </select>
    <label class="finance-check">
        <input type="checkbox" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}>
        {{ __('financing::financing.overdue_only') }}
    </label>
    <button class="btn btn-outline-primary">{{ __('financing::financing.filter') }}</button>
</form>

<div class="finance-table-wrap">
<table class="table table-sm table-hover">
<thead><tr>
    <th>{{ __('financing::financing.invoice') }}</th>
    <th>{{ __('financing::financing.client') }}</th>
    <th>{{ __('financing::financing.amount') }}</th>
    <th>{{ __('financing::financing.due_date') }}</th>
    <th>{{ __('financing::financing.status') }}</th>
    <th></th>
</tr></thead>
<tbody>
@forelse($installments as $installment)
<tr class="{{ $installment->is_overdue ? 'finance-overdue' : '' }}">
    <td>#{{ $installment->invoice_id }}</td>
    <td>{{ optional($installment->client)->name }}</td>
    <td>{{ number_format($installment->amount,2) }} {{ config('modules.financing.currency','JOD') }}</td>
    <td>{{ $installment->due_date->format('d/m/Y') }}</td>
    <td>
        @if($installment->paid_at)
            <span class="badge badge-success">{{ __('financing::financing.paid') }}</span>
        @elseif($installment->is_overdue)
            <span class="badge badge-danger">{{ __('financing::financing.overdue') }}</span>
        @else
            <span class="badge badge-warning">{{ __('financing::financing.unpaid') }}</span>
        @endif
    </td>
    <td>
        @if(!$installment->paid_at)
        <form method="POST" action="{{ route('financing.installments.pay', $installment->id) }}" class="finance-inline-form">
            @csrf
            <select name="account_id" class="form-control form-control-sm" required>
                <option value="">{{ __('financing::financing.deposit_to') }}</option>
                @foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach
            </select>
            <button class="btn btn-sm btn-success">{{ __('financing::financing.mark_paid') }}</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">{{ __('financing::financing.no_records') }}</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $installments->links() }}
@endsection
