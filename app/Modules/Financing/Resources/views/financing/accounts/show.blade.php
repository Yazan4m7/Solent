@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h3>{{ $account->name }}</h3>
        <div class="finance-balance">{{ number_format($account->balance, 2) }} {{ $account->currency }}</div>
    </div>
    <a href="{{ route('financing.accounts.edit', $account->id) }}" class="btn btn-outline-primary">{{ __('financing::financing.edit') }}</a>
</div>

<div class="finance-table-wrap">
<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th>{{ __('financing::financing.date') }}</th>
            <th>{{ __('financing::financing.description') }}</th>
            <th>{{ __('financing::financing.inflow') }}</th>
            <th>{{ __('financing::financing.outflow') }}</th>
        </tr>
    </thead>
    <tbody>
    @forelse($transactions as $transaction)
        <tr>
            <td>{{ $transaction->date->format('d/m/Y') }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->direction === 'inflow' ? number_format($transaction->amount, 2) : '' }}</td>
            <td>{{ $transaction->direction === 'outflow' ? number_format($transaction->amount, 2) : '' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted">{{ __('financing::financing.no_transactions') }}</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $transactions->links() }}
@endsection
