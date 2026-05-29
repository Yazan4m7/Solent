@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="alert alert-warning">
            <h4 class="mb-2">Tenant unavailable</h4>
            <p class="mb-1">This workspace is currently {{ $tenantContext->status }}.</p>
            <p class="mb-0 text-muted">Host: {{ $requestedHost }}</p>
        </div>
    </div>
@endsection
