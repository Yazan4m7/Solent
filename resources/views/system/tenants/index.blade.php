@extends('layouts.app', ['pageSlug' => 'Tenants', 'platformAdminPage' => true])

@section('content')
    <style>
        .tenant-logo-cell {
            min-width: 120px;
            width: 150px;
        }

        .tenant-logo-cell__image {
            background: #f8fafc;
            border-radius: 10px;
            display: block;
            height: 44px;
            object-fit: contain;
            padding: 4px;
            width: 112px;
        }

        .tenant-logo-cell__fallback {
            align-items: center;
            background: #e2e8f0;
            border-radius: 10px;
            color: #334155;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            line-height: 1.2;
            min-height: 44px;
            padding: 7px 10px;
            text-align: center;
            width: 112px;
        }
    </style>

    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">{{ $showDisabled ? 'Disabled tenants' : 'Tenants' }}</h1>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <span class="text-muted">Landlord registry</span>
                    @if($showDisabled)
                        <a href="{{ route('system.tenants.index') }}" class="text-muted" style="font-size:11px; text-decoration:underline;">Back to tenants</a>
                    @else
                        <a href="{{ route('system.tenants.index', ['status' => 'disabled']) }}" class="text-muted" style="font-size:11px; text-decoration:underline;">View disabled</a>
                    @endif
                </div>
            </div>
            <a href="{{ route('system.tenants.create') }}" class="btn btn-primary">Create tenant</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Database</th>
                        <th>Status</th>
                        <th>Currency</th>
                        <th>Last sign-in</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tenants as $tenant)
                        <tr>
                            <td class="tenant-logo-cell">
                                @if(data_get($tenant->branding, 'logo_path'))
                                    <img class="tenant-logo-cell__image" src="{{ asset(data_get($tenant->branding, 'logo_path')) }}" alt="{{ $tenant->name }} logo" onerror="this.hidden=true;this.nextElementSibling.hidden=false;">
                                    <span class="tenant-logo-cell__fallback" hidden>{{ $tenant->name }}</span>
                                @else
                                    <span class="tenant-logo-cell__fallback">{{ $tenant->name }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $tenant->name }}</strong>
                                <div class="text-muted">{{ $tenant->slug }}</div>
                            </td>
                            <td>
                                @if(optional($tenant->primaryDomain)->host)
                                    <a href="https://{{ $tenant->primaryDomain->host }}" target="_blank" rel="noopener noreferrer">
                                        {{ $tenant->primaryDomain->host }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><code>{{ $tenant->database_name }}</code></td>
                            <td>
                                <span class="badge badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'disabled' ? 'secondary' : ($tenant->status === 'failed' ? 'danger' : 'warning')) }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td>{{ $tenant->currency_code }}</td>
                            <td>
                                @if($tenant->last_login_at)
                                    <strong>{{ $tenant->last_login_at->format('Y-m-d H:i') }}</strong>
                                    @if($tenant->last_login_username)
                                        <div>{{ $tenant->last_login_username }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="d-flex flex-wrap justify-content-end" style="gap:6px;">
                                    <a href="{{ route('system.tenants.logo.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary">Edit logos</a>
                                    <a href="{{ route('system.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                    @if($tenant->status === 'active')
                                        <form method="POST" action="{{ route('system.tenants.disable', $tenant) }}" onsubmit="return confirm('Disable access to this tenant?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Disable</button>
                                        </form>
                                    @elseif($tenant->status === 'disabled')
                                        <form method="POST" action="{{ route('system.tenants.enable', $tenant) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Enable</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted">{{ $showDisabled ? 'No disabled tenants.' : 'No tenants yet.' }}</td>
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
