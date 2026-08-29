@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')

<div class="stock-kpi-grid">
    <div class="stock-kpi"><span>Active items</span><strong>{{ $items->count() }}</strong></div>
    <div class="stock-kpi stock-kpi-warn"><span>Low stock</span><strong>{{ $lowStock->count() }}</strong></div>
    <div class="stock-kpi stock-kpi-danger"><span>Out of stock</span><strong>{{ $outOfStock->count() }}</strong></div>
    <div class="stock-kpi"><span>Estimated stock value</span><strong>{{ number_format($inventoryValue, 2) }}</strong></div>
</div>

<div class="stock-grid-2">
    <section class="stock-card">
        <div class="stock-card-head">
            <div>
                <h2>Need attention</h2>
                <p>Items at or below their minimum level.</p>
            </div>
            <a href="{{ route('stock.needs') }}">View all</a>
        </div>
        <div class="stock-table-wrap">
            <table class="stock-table">
                <thead><tr><th>Item</th><th>On hand</th><th>Minimum</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($lowStock->take(10) as $item)
                    @php $qty = (float) ($item->quantity ?? 0); @endphp
                    <tr>
                        <td><a href="{{ route('stock.items.show', $item) }}"><strong>{{ $item->name }}</strong></a><div class="stock-muted">{{ $item->sku ?: $item->category }}</div></td>
                        <td>{{ rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.') }} {{ $item->unit }}</td>
                        <td>{{ rtrim(rtrim(number_format((float)$item->minimum_stock, 3, '.', ''), '0'), '.') }}</td>
                        <td><span class="stock-badge {{ $qty <= 0 ? 'danger' : 'warn' }}">{{ $qty <= 0 ? 'Out' : 'Low' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="stock-empty">Nothing needs attention.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="stock-card">
        <div class="stock-card-head"><div><h2>Recent movements</h2><p>Audit trail of stock going in and out.</p></div><a href="{{ route('stock.movements.index') }}">View all</a></div>
        <div class="stock-list">
            @forelse($recentMovements as $movement)
                <div class="stock-list-row">
                    <div><strong>{{ $movement->item->name }}</strong><div class="stock-muted">{{ str_replace('_', ' ', ucfirst($movement->type)) }} · {{ $movement->location->name }} · {{ $movement->occurred_at->format('d M Y H:i') }}</div></div>
                    <div class="stock-qty {{ (float)$movement->quantity > 0 ? 'in' : 'out' }}">{{ (float)$movement->quantity > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format((float)$movement->quantity, 3, '.', ''), '0'), '.') }}</div>
                </div>
            @empty
                <div class="stock-empty">No stock movements yet.</div>
            @endforelse
        </div>
    </section>
</div>

@include('stock.partials.footer')
@endsection
