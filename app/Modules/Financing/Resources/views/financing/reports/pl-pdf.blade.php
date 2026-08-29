<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#222}table{width:100%;border-collapse:collapse;margin-top:15px}td,th{border:1px solid #ccc;padding:7px;text-align:left}.metric{margin:8px 0;font-size:15px}.net{font-weight:bold;font-size:18px}
</style>
</head>
<body dir="auto">
<h2>{{ __('financing::financing.pl_report') }}</h2>
<p>{{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</p>
<div class="metric">{{ __('financing::financing.revenue') }}: {{ number_format($revenue,2) }} {{ config('modules.financing.currency','JOD') }}</div>
<div class="metric">{{ __('financing::financing.operating_expenses') }}: {{ number_format($expenses,2) }} {{ config('modules.financing.currency','JOD') }}</div>
<div class="metric">{{ __('financing::financing.payroll') }}: {{ number_format($payroll,2) }} {{ config('modules.financing.currency','JOD') }}</div>
<div class="metric net">{{ __('financing::financing.net') }}: {{ number_format($net,2) }} {{ config('modules.financing.currency','JOD') }}</div>
<table>
<thead><tr><th>{{ __('financing::financing.category') }}</th><th>{{ __('financing::financing.amount') }}</th></tr></thead>
<tbody>@foreach($breakdown as $name=>$amount)<tr><td>{{ $name }}</td><td>{{ number_format($amount,2) }}</td></tr>@endforeach</tbody>
</table>
</body>
</html>
