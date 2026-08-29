@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')

<div class="stock-card stock-item-hero">
    <div>
        <div class="stock-eyebrow">{{ $item->sku ?: 'NO SKU' }}</div>
        <h2>{{ $item->name }}</h2>
        <p>{{ $item->description ?: $item->category }}</p>
    </div>
    <a class="stock-btn" href="{{ route('stock.items.edit', $item) }}">Edit item</a>
</div>

<div class="stock-grid-2">
    <section class="stock-card">
        <div class="stock-card-head"><div><h2>Stock by location</h2></div></div>
        <div class="stock-table-wrap">
            <table class="stock-table">
                <thead><tr><th>Location</th><th>Quantity</th></tr></thead>
                <tbody>
                @forelse($item->balances as $balance)
                    <tr><td>{{ $balance->location->name }}</td><td><strong>{{ rtrim(rtrim(number_format((float)$balance->quantity, 3, '.', ''), '0'), '.') }}</strong> {{ $item->unit }}</td></tr>
                @empty
                    <tr><td colspan="2" class="stock-empty">No stock received yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="stock-card">
        <div class="stock-card-head"><div><h2>Planning</h2></div></div>
        @php $total = $item->balances->sum(fn($b) => (float)$b->quantity); @endphp
        <dl class="stock-definition">
            <div><dt>Total on hand</dt><dd>{{ rtrim(rtrim(number_format($total, 3, '.', ''), '0'), '.') }} {{ $item->unit }}</dd></div>
            <div><dt>Minimum</dt><dd>{{ $item->minimum_stock }} {{ $item->unit }}</dd></div>
            <div><dt>Target</dt><dd>{{ $item->target_stock ?? '—' }} {{ $item->target_stock !== null ? $item->unit : '' }}</dd></div>
            <div><dt>Default cost</dt><dd>{{ $item->default_unit_cost !== null ? number_format((float)$item->default_unit_cost, 4) : '—' }}</dd></div>
        </dl>
    </section>
</div>

<section class="stock-card">
    <div class="stock-card-head"><div><h2>Movement history</h2><p>Append-only audit trail for this item.</p></div></div>
    <div class="stock-table-wrap">
        <table class="stock-table">
            <thead><tr><th>Date</th><th>Type</th><th>Location</th><th>Qty</th><th>Reference</th><th>Notes</th></tr></thead>
            <tbody>
            @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->occurred_at->format('d M Y H:i') }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($movement->type)) }}</td>
                    <td>{{ $movement->location->name }}</td>
                    <td class="stock-qty {{ (float)$movement->quantity > 0 ? 'in' : 'out' }}">{{ (float)$movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                    <td>{{ $movement->reference_type ? $movement->reference_type.' #'.$movement->reference_id : '—' }}</td>
                    <td>{{ $movement->notes ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="stock-empty">No movement history.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="stock-pagination">{{ $movements->links() }}</div>
</section>

@include('stock.partials.footer')
@endsection
