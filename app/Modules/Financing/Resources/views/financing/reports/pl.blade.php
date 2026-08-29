@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
<h3>{{ __('financing::financing.pl_report') }}</h3>
<div>
<a class="btn btn-outline-secondary" href="{{ route('financing.reports.pl.pdf',['from'=>$from->format('Y-m-d'),'to'=>$to->format('Y-m-d')]) }}">PDF</a>
<a class="btn btn-outline-secondary" href="{{ route('financing.reports.pl.excel',['from'=>$from->format('Y-m-d'),'to'=>$to->format('Y-m-d')]) }}">Excel</a>
</div>
</div>

<form method="GET" class="finance-filter mb-3">
<input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
<input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
<button class="btn btn-outline-primary">{{ __('financing::financing.apply') }}</button>
</form>

<div class="row">
@foreach([
    __('financing::financing.revenue')=>$revenue,
    __('financing::financing.operating_expenses')=>$expenses,
    __('financing::financing.payroll')=>$payroll,
    __('financing::financing.net')=>$net
] as $label=>$amount)
<div class="col-md-3 mb-3"><div class="finance-card"><div class="finance-card-label">{{ $label }}</div><div class="finance-card-value">{{ number_format($amount,2) }} {{ config('modules.financing.currency','JOD') }}</div></div></div>
@endforeach
</div>

<div class="finance-section">
<h5>{{ __('financing::financing.expense_breakdown') }}</h5>
<table class="table table-sm">
<thead><tr><th>{{ __('financing::financing.category') }}</th><th>{{ __('financing::financing.amount') }}</th></tr></thead>
<tbody>
@foreach($breakdown as $name=>$amount)<tr><td>{{ $name }}</td><td>{{ number_format($amount,2) }} {{ config('modules.financing.currency','JOD') }}</td></tr>@endforeach
</tbody>
</table>
</div>
@endsection
