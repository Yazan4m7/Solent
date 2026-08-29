@extends('financing._layout')
@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>{{ __('financing::financing.payroll') }}</h3>
    <div>
        <a href="{{ route('financing.payroll.salaries') }}" class="btn btn-outline-primary">{{ __('financing::financing.salary_config') }}</a>
        <a href="{{ route('financing.payroll.create') }}" class="btn btn-primary">{{ __('financing::financing.new_payroll_run') }}</a>
    </div>
</div>
<div class="finance-table-wrap">
<table class="table table-sm table-hover">
<thead><tr><th>{{ __('financing::financing.period') }}</th><th>{{ __('financing::financing.employees') }}</th><th>{{ __('financing::financing.total') }}</th><th>{{ __('financing::financing.status') }}</th></tr></thead>
<tbody>
@foreach($runs as $run)
<tr>
    <td><a href="{{ route('financing.payroll.show', $run->id) }}">{{ str_pad($run->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $run->period_year }}</a></td>
    <td>{{ $run->lines_count }}</td>
    <td>{{ number_format($run->total, 2) }} {{ config('modules.financing.currency', 'JOD') }}</td>
    <td><span class="badge badge-{{ $run->posted_at ? 'success' : 'warning' }}">{{ $run->posted_at ? __('financing::financing.posted') : __('financing::financing.draft') }}</span></td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $runs->links() }}
@endsection
