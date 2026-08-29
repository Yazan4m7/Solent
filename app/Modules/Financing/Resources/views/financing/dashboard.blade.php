@extends('financing._layout')

@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ __('financing::financing.financing_dashboard') }}</h3>
    <small class="text-muted">{{ now()->format('m/Y') }}</small>
</div>

<div class="row">
    @php
        $cards = [
            [__('financing::financing.revenue'), $revenue, 'money'],
            [__('financing::financing.expenses'), $expenses, 'money'],
            [__('financing::financing.payroll'), $payroll, 'money'],
            [__('financing::financing.net'), $revenue - $expenses - $payroll, 'money'],
            [__('financing::financing.overdue_installments'), $overdueInstallments, 'count'],
            [__('financing::financing.overdue_supplier_bills'), $overdueSupplierBills, 'count'],
            [__('financing::financing.unsubmitted_collections'), $unsubmittedCollections, 'count'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="finance-card">
                <div class="finance-card-label">{{ $card[0] }}</div>
                <div class="finance-card-value">
                    @if($card[2] === 'money')
                        {{ number_format($card[1], 2) }} {{ config('modules.financing.currency', 'JOD') }}
                    @else
                        {{ $card[1] }}
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
