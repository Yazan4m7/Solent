@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')

<section class="stock-card">
    <div class="stock-card-head">
        <form class="stock-search" method="get">
            <input name="q" value="{{ request('q') }}" placeholder="Search name, SKU or category">
            <button class="stock-btn" type="submit">Search</button>
        </form>
        <a class="stock-btn stock-btn-primary" href="{{ route('stock.items.create') }}">+ New item</a>
    </div>
    <div class="stock-table-wrap">
        <table class="stock-table">
            <thead><tr><th>Item</th><th>Category</th><th>On hand</th><th>Minimum</th><th>Target</th><th></th></tr></thead>
            <tbody>
            @forelse($items as $item)
                @php $qty = (float) ($item->quantity ?? 0); @endphp
                <tr>
                    <td><a href="{{ route('stock.items.show', $item) }}"><strong>{{ $item->name }}</strong></a><div class="stock-muted">{{ $item->sku ?: 'No SKU' }}</div></td>
                    <td>{{ $item->category ?: '—' }}</td>
                    <td><strong>{{ rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.') }}</strong> {{ $item->unit }} @if($qty <= (float)$item->minimum_stock)<span class="stock-badge {{ $qty <= 0 ? 'danger' : 'warn' }}">{{ $qty <= 0 ? 'Out' : 'Low' }}</span>@endif</td>
                    <td>{{ rtrim(rtrim(number_format((float)$item->minimum_stock, 3, '.', ''), '0'), '.') }}</td>
                    <td>{{ $item->target_stock !== null ? rtrim(rtrim(number_format((float)$item->target_stock, 3, '.', ''), '0'), '.') : '—' }}</td>
                    <td><a href="{{ route('stock.items.edit', $item) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="stock-empty">No stock items found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="stock-pagination">{{ $items->links() }}</div>
</section>

@include('stock.partials.footer')
@endsection
