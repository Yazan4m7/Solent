@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card">
    <div class="stock-card-head"><div><h2>Purchases received</h2><p>Each receipt adds stock and creates movement records.</p></div><a class="stock-btn stock-btn-primary" href="{{ route('stock.purchases.create') }}">+ Receive purchase</a></div>
    <div class="stock-table-wrap">
        <table class="stock-table">
            <thead><tr><th>Date</th><th>Reference</th><th>Supplier</th><th>Lines</th><th>Total cost</th></tr></thead>
            <tbody>
            @forelse($purchases as $purchase)
                @php $total = $purchase->lines->sum(fn($l) => (float)$l->quantity * (float)($l->unit_cost ?? 0)); @endphp
                <tr>
                    <td>{{ $purchase->purchased_at->format('d M Y') }}</td>
                    <td>{{ $purchase->reference_no ?: '#'.$purchase->id }}</td>
                    <td>{{ optional($purchase->supplier)->name ?: '—' }}</td>
                    <td>{{ $purchase->lines->count() }}</td>
                    <td>{{ number_format($total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="stock-empty">No purchases received yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="stock-pagination">{{ $purchases->links() }}</div>
</section>
@include('stock.partials.footer')
@endsection
