@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card stock-form-card">
    <div class="stock-card-head"><div><h2>Receive purchase</h2><p>Record items purchased and immediately add them to available stock.</p></div></div>

    <form method="post" action="{{ route('stock.purchases.store') }}" id="stock-purchase-form">
        @csrf
        <div class="stock-form-grid">
            <div class="stock-field">
                <label>Purchase date *</label>
                <input type="date" name="purchased_at" value="{{ old('purchased_at', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="stock-field">
                <label>Supplier</label>
                <select name="supplier_id"><option value="">— None —</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select>
            </div>
            <div class="stock-field">
                <label>Invoice / reference</label>
                <input name="reference_no" value="{{ old('reference_no') }}" placeholder="INV-2041">
            </div>
            <div class="stock-field stock-span-2">
                <label>Notes</label>
                <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="stock-lines-head"><h3>Items</h3><button class="stock-btn" type="button" id="add-stock-line">+ Add line</button></div>
        <div class="stock-table-wrap">
            <table class="stock-table stock-lines-table">
                <thead><tr><th>Item *</th><th>Location *</th><th>Qty *</th><th>Unit cost</th><th>Lot</th><th>Expiry</th><th></th></tr></thead>
                <tbody id="stock-lines-body"></tbody>
            </table>
        </div>

        <template id="stock-line-template">
            <tr class="stock-line-row">
                <td><select data-name="stock_item_id" required><option value="">Select item</option>@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->name }}{{ $item->sku ? ' · '.$item->sku : '' }}</option>@endforeach</select></td>
                <td><select data-name="location_id" required><option value="">Select location</option>@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></td>
                <td><input data-name="quantity" type="number" min="0.001" step="0.001" required></td>
                <td><input data-name="unit_cost" type="number" min="0" step="0.0001"></td>
                <td><input data-name="lot_number"></td>
                <td><input data-name="expires_at" type="date"></td>
                <td><button class="stock-icon-btn stock-remove-line" type="button" title="Remove">×</button></td>
            </tr>
        </template>

        <div class="stock-form-actions"><a class="stock-btn" href="{{ route('stock.purchases.index') }}">Cancel</a><button class="stock-btn stock-btn-primary" type="submit">Receive into stock</button></div>
    </form>
</section>
<script src="{{ asset('js/stock.js') }}"></script>
@include('stock.partials.footer')
@endsection
