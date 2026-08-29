@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')

<section class="stock-card">
    <div class="stock-card-head">
        <div><h2>Need to buy</h2><p>Automatically generated from minimum and target stock levels.</p></div>
        <a class="stock-btn stock-btn-primary" href="{{ route('stock.purchases.create') }}">Receive purchase</a>
    </div>
    <div class="stock-table-wrap">
        <table class="stock-table">
            <thead><tr><th>Item</th><th>Current</th><th>Minimum</th><th>Target</th><th>Suggested order</th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td><a href="{{ route('stock.items.show', $item) }}"><strong>{{ $item->name }}</strong></a><div class="stock-muted">{{ $item->sku ?: $item->category }}</div></td>
                    <td>{{ rtrim(rtrim(number_format((float)($item->quantity ?? 0), 3, '.', ''), '0'), '.') }} {{ $item->unit }}</td>
                    <td>{{ rtrim(rtrim(number_format((float)$item->minimum_stock, 3, '.', ''), '0'), '.') }}</td>
                    <td>{{ $item->target_stock !== null ? rtrim(rtrim(number_format((float)$item->target_stock, 3, '.', ''), '0'), '.') : '—' }}</td>
                    <td><strong>{{ rtrim(rtrim(number_format((float)$item->suggested_order, 3, '.', ''), '0'), '.') }} {{ $item->unit }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="5" class="stock-empty">All items are above their minimum levels.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

@include('stock.partials.footer')
@endsection
