@extends('layouts.app', ['pageSlug' => 'Tenant Details'])

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">{{ $tenant->name }}</h1>
                <div class="text-muted">{{ $tenant->slug }} · {{ $tenant->uuid }}</div>
            </div>
            <a href="{{ route('system.tenants.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card p-3 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-muted">Status</div>
                    <strong>{{ $tenant->status }}</strong>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Database</div>
                    <code>{{ $tenant->database_name }}</code>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Currency</div>
                    <strong>{{ $tenant->currency_code }}</strong>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Activated</div>
                    <strong>{{ optional($tenant->activated_at)->format('Y-m-d H:i') ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Domains</div>
            <ul class="list-group list-group-flush">
                @foreach($tenant->domains as $domain)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $domain->host }}</span>
                        @if($domain->is_primary)
                            <span class="badge badge-primary">Primary</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Provisioning Events</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Time</th>
                        <th>Step</th>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tenant->provisioningEvents as $event)
                        <tr>
                            <td>{{ $event->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $event->step }}</td>
                            <td>{{ $event->status }}</td>
                            <td>{{ $event->message }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
