@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card">
    <div class="stock-card-head">
        <form class="stock-filter" method="get">
            <select name="item_id"><option value="">All items</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected(request('item_id') == $item->id)>{{ $item->name }}</option>@endforeach</select>
            <select name="type">
                <option value="">All movement types</option>
                @foreach(['purchase','job_usage','adjustment_in','adjustment_out','return'] as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_',' ',ucfirst($type)) }}</option>@endforeach
            </select>
            <button class="stock-btn">Filter</button>
        </form>
        <a class="stock-btn stock-btn-primary" href="{{ route('stock.adjustments.create') }}">+ Adjustment</a>
    </div>
    <div class="stock-table-wrap">
        <table class="stock-table">
            <thead><tr><th>Date</th><th>Item</th><th>Location</th><th>Type</th><th>Quantity</th><th>Reference</th><th>Notes</th></tr></thead>
            <tbody>
            @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->occurred_at->format('d M Y H:i') }}</td>
                    <td><a href="{{ route('stock.items.show', $movement->item) }}"><strong>{{ $movement->item->name }}</strong></a></td>
                    <td>{{ $movement->location->name }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($movement->type)) }}</td>
                    <td class="stock-qty {{ (float)$movement->quantity > 0 ? 'in' : 'out' }}">{{ (float)$movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                    <td>{{ $movement->reference_type ? $movement->reference_type.' #'.$movement->reference_id : '—' }}</td>
                    <td>{{ $movement->notes ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="stock-empty">No movements found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="stock-pagination">{{ $movements->links() }}</div>
</section>
@include('stock.partials.footer')
@endsection
