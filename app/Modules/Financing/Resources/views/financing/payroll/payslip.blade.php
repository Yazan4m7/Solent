<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:13px;color:#222} table{width:100%;border-collapse:collapse} td,th{border:1px solid #ccc;padding:8px;text-align:left}
h2{margin-bottom:4px}.total{font-size:18px;font-weight:bold}
</style>
</head>
<body dir="auto">
<h2>{{ __('financing::financing.payslip') }}</h2>
<p>{{ __('financing::financing.employee') }}: {{ optional($line->user)->name }}</p>
<p>{{ __('financing::financing.period') }}: {{ str_pad($run->period_month,2,'0',STR_PAD_LEFT) }}/{{ $run->period_year }}</p>
<table>
<tr><th>{{ __('financing::financing.base_salary') }}</th><td>{{ number_format($line->base_salary,2) }} {{ config('modules.financing.currency','JOD') }}</td></tr>
<tr><th>{{ __('financing::financing.bonus') }}</th><td>{{ number_format($line->bonus,2) }} {{ config('modules.financing.currency','JOD') }}</td></tr>
<tr><th>{{ __('financing::financing.deductions') }}</th><td>{{ number_format($line->deductions,2) }} {{ config('modules.financing.currency','JOD') }}</td></tr>
<tr><th>{{ __('financing::financing.net') }}</th><td class="total">{{ number_format($line->net,2) }} {{ config('modules.financing.currency','JOD') }}</td></tr>
</table>
@if($line->notes)<p>{{ $line->notes }}</p>@endif
</body>
</html>
