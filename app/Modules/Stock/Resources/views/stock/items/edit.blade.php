@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
@include('stock.partials.header')
<section class="stock-card stock-form-card">
    <div class="stock-card-head"><div><h2>Edit {{ $item->name }}</h2><p>Changing these settings does not rewrite movement history.</p></div></div>
    <form method="post" action="{{ route('stock.items.update', $item) }}">
        @csrf @method('PUT')
        @include('stock.partials.item-form')
        <div class="stock-form-actions"><a class="stock-btn" href="{{ route('stock.items.show', $item) }}">Cancel</a><button class="stock-btn stock-btn-primary">Save changes</button></div>
    </form>
</section>
@include('stock.partials.footer')
@endsection
