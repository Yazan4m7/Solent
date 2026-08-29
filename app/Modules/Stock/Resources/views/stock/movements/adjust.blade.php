@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card stock-form-card stock-narrow">
    <div class="stock-card-head"><div><h2>Stock adjustment</h2><p>For corrections, damaged material, opening balances or stock count differences. A reason is required for the audit trail.</p></div></div>
    <form method="post" action="{{ route('stock.adjustments.store') }}">
        @csrf
        <div class="stock-form-grid">
            <div class="stock-field stock-span-2"><label>Item *</label><select name="stock_item_id" required><option value="">Select item</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected(old('stock_item_id') == $item->id)>{{ $item->name }}{{ $item->sku ? ' · '.$item->sku : '' }}</option>@endforeach</select></div>
            <div class="stock-field"><label>Location *</label><select name="location_id" required><option value="">Select location</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>@endforeach</select></div>
            <div class="stock-field"><label>Direction *</label><select name="direction"><option value="in" @selected(old('direction') === 'in')>Add stock</option><option value="out" @selected(old('direction') === 'out')>Remove stock</option></select></div>
            <div class="stock-field"><label>Quantity *</label><input type="number" name="quantity" step="0.001" min="0.001" value="{{ old('quantity') }}" required></div>
            <div class="stock-field stock-span-2"><label>Reason *</label><textarea name="notes" rows="3" required placeholder="e.g. Physical count correction, damaged block, opening balance">{{ old('notes') }}</textarea></div>
        </div>
        <div class="stock-form-actions"><a class="stock-btn" href="{{ route('stock.movements.index') }}">Cancel</a><button class="stock-btn stock-btn-primary">Post adjustment</button></div>
    </form>
</section>
@include('stock.partials.footer')
@endsection
