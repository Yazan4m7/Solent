@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card stock-form-card">
    <div class="stock-card-head"><div><h2>New stock item</h2><p>Create an item master record. Different sizes/shades should normally have different SKUs.</p></div></div>
    <form method="post" action="{{ route('stock.items.store') }}">
        @csrf
        @include('stock.partials.item-form')
        <div class="stock-form-actions"><a class="stock-btn" href="{{ route('stock.items.index') }}">Cancel</a><button class="stock-btn stock-btn-primary">Create item</button></div>
    </form>
</section>
@include('stock.partials.footer')
@endsection
