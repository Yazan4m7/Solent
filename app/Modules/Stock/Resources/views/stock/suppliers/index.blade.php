@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<div class="stock-grid-2 stock-grid-sidebar">
    <section class="stock-card stock-form-card">
        <div class="stock-card-head"><div><h2>Add supplier</h2></div></div>
        <form method="post" action="{{ route('stock.suppliers.store') }}">@csrf
            <div class="stock-field"><label>Name *</label><input name="name" required></div>
            <div class="stock-field"><label>Contact person</label><input name="contact_person"></div>
            <div class="stock-field"><label>Phone</label><input name="phone"></div>
            <div class="stock-field"><label>Email</label><input type="email" name="email"></div>
            <div class="stock-field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="stock-btn stock-btn-primary">Add supplier</button>
        </form>
    </section>
    <section class="stock-card">
        <div class="stock-card-head"><div><h2>Suppliers</h2></div></div>
        <div class="stock-table-wrap"><table class="stock-table"><thead><tr><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th></tr></thead><tbody>
        @forelse($suppliers as $supplier)<tr><td><strong>{{ $supplier->name }}</strong></td><td>{{ $supplier->contact_person ?: '—' }}</td><td>{{ $supplier->phone ?: '—' }}</td><td>{{ $supplier->email ?: '—' }}</td></tr>@empty<tr><td colspan="4" class="stock-empty">No suppliers yet.</td></tr>@endforelse
        </tbody></table></div>
        <div class="stock-pagination">{{ $suppliers->links() }}</div>
    </section>
</div>
@include('stock.partials.footer')
@endsection
