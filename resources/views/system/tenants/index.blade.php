@extends('layouts.app', ['pageSlug' => 'Tenants'])

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Tenants</h1>
                <div class="text-muted">Landlord registry</div>
            </div>
            <a href="{{ route('system.tenants.create') }}" class="btn btn-primary">Create tenant</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Database</th>
                        <th>Status</th>
                        <th>Currency</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tenants as $tenant)
                        <tr>
                            <td>
                                <strong>{{ $tenant->name }}</strong>
                                <div class="text-muted">{{ $tenant->slug }}</div>
                            </td>
                            <td>{{ optional($tenant->primaryDomain)->host }}</td>
                            <td><code>{{ $tenant->database_name }}</code></td>
                            <td>
                                <span class="badge badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td>{{ $tenant->currency_code }}</td>
                            <td class="text-right">
                                <a href="{{ route('system.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No tenants yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $tenants->links() }}
        </div>
    </div>
@endsection
