@extends('financing._layout')
@section('finance-content')
<h3>{{ __('financing::financing.driver_collections') }}</h3>

@forelse($collections as $driverId => $rows)
@php
    $driver = optional($rows->first())->user;
    $collected = $rows->sum('collected_amount');
    $submitted = $rows->sum('submitted_amount');
@endphp
<div class="finance-section mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>{{ optional($driver)->name ?: ('User #'.$driverId) }}</strong>
        <span>
            {{ __('financing::financing.collected') }} {{ number_format($collected,2) }} /
            {{ __('financing::financing.submitted') }} {{ number_format($submitted,2) }} /
            {{ __('financing::financing.gap') }} {{ number_format($collected-$submitted,2) }}
        </span>
    </div>
    <div class="finance-table-wrap">
    <table class="table table-sm">
    <thead><tr><th>{{ __('financing::financing.invoice') }}</th><th>{{ __('financing::financing.created') }}</th><th>{{ __('financing::financing.collected') }}</th><th>{{ __('financing::financing.submitted') }}</th><th>{{ __('financing::financing.gap') }}</th><th></th></tr></thead>
    <tbody>
    @foreach($rows as $row)
    <tr class="{{ (!$row->is_fully_submitted && $row->created_at->lt(now()->subDays(3))) ? 'finance-overdue' : '' }}">
        <td>#{{ $row->invoice_id }}</td>
        <td>{{ $row->created_at->format('d/m/Y') }}</td>
        <td>{{ number_format($row->collected_amount,2) }}</td>
        <td>{{ number_format($row->submitted_amount,2) }}</td>
        <td>{{ number_format($row->outstanding_gap,2) }}</td>
        <td>
        @if(!$row->is_fully_submitted)
            <form method="POST" action="{{ route('financing.collections.submit',$row->id) }}" class="finance-inline-form">
                @csrf
                <input type="number" step="0.01" min="0.01" max="{{ $row->collected_amount }}" name="submitted_amount" class="form-control form-control-sm" value="{{ $row->collected_amount }}" required>
                <select name="account_id" class="form-control form-control-sm" required>
                    <option value="">{{ __('financing::financing.account') }}</option>
                    @foreach($accounts as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach
                </select>
                <button class="btn btn-sm btn-success">{{ __('financing::financing.reconcile') }}</button>
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
</div>
@empty
<p class="text-muted">{{ __('financing::financing.no_records') }}</p>
@endforelse
@endsection
