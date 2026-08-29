@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<div class="stock-grid-2 stock-grid-sidebar">
    <section class="stock-card stock-form-card">
        <div class="stock-card-head"><div><h2>Add stock location</h2><p>Shelf, room, warehouse or department.</p></div></div>
        <form method="post" action="{{ route('stock.locations.store') }}">@csrf
            <div class="stock-field"><label>Name *</label><input name="name" required placeholder="Main storage"></div>
            <div class="stock-field"><label>Code</label><input name="code" placeholder="MAIN"></div>
            <div class="stock-field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="stock-btn stock-btn-primary">Add location</button>
        </form>
    </section>
    <section class="stock-card">
        <div class="stock-card-head"><div><h2>Locations</h2></div></div>
        <div class="stock-table-wrap"><table class="stock-table"><thead><tr><th>Name</th><th>Code</th><th>Status</th></tr></thead><tbody>
        @forelse($locations as $location)<tr><td><strong>{{ $location->name }}</strong></td><td>{{ $location->code ?: '—' }}</td><td><span class="stock-badge {{ $location->is_active ? 'ok' : '' }}">{{ $location->is_active ? 'Active' : 'Inactive' }}</span></td></tr>@empty<tr><td colspan="3" class="stock-empty">No locations yet.</td></tr>@endforelse
        </tbody></table></div>
        <div class="stock-pagination">{{ $locations->links() }}</div>
    </section>
</div>
@include('stock.partials.footer')
@endsection
