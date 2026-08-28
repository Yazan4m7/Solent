@extends('layouts.app', ['pageSlug' => 'Create Tenant', 'platformAdminPage' => true])

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Create Tenant</h1>
                <div class="text-muted">Provision isolated database and first admin</div>
            </div>
            <a href="{{ route('system.tenants.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <form method="POST" action="{{ route('system.tenants.store') }}" class="card p-3">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="slug">Slug</label>
                    <input id="slug" name="slug" value="{{ old('slug') }}" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="currency_code">Currency</label>
                    <input id="currency_code" name="currency_code" value="{{ old('currency_code', 'JOD') }}" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="domain">Primary domain</label>
                    <input id="domain" name="domain" value="{{ old('domain') }}" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="database">Database name</label>
                    <input id="database" name="database" value="{{ old('database') }}" class="form-control" placeholder="Leave blank for xlab_kordent">
                </div>
            </div>

            <h2 class="h5 mt-2">Lab administrator</h2>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="admin_name">Admin name</label>
                    <input id="admin_name" name="admin_name" value="{{ old('admin_name', 'Tenant Admin') }}" class="form-control" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="admin_username">Admin username</label>
                    <input id="admin_username" name="admin_username" value="{{ old('admin_username') }}" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <label for="admin_email">Admin email</label>
                    <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}" class="form-control" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="admin_password">Admin password</label>
                    <input id="admin_password" type="password" name="admin_password" class="form-control" required minlength="8">
                </div>
            </div>

            <h2 class="h5 mt-2">First client portal account <small class="text-muted">(optional)</small></h2>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="client_name">Client name</label>
                    <input id="client_name" name="client_name" value="{{ old('client_name') }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="client_username">Client username</label>
                    <input id="client_username" name="client_username" value="{{ old('client_username') }}" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <label for="client_email">Client email</label>
                    <input id="client_email" type="email" name="client_email" value="{{ old('client_email') }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="client_password">Client password</label>
                    <input id="client_password" type="password" name="client_password" class="form-control" minlength="8">
                </div>
            </div>

            <div class="form-check mb-3">
                <input id="resume" type="checkbox" name="resume" value="1" class="form-check-input" {{ old('resume') ? 'checked' : '' }}>
                <label for="resume" class="form-check-label">Resume failed/provisioning tenant</label>
            </div>

            <div class="text-right">
                <button class="btn btn-primary" type="submit">Provision tenant</button>
            </div>
        </form>
    </div>
@endsection
