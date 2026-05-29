<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Support\Tenancy\TenantProvisioningService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::query()
            ->with('primaryDomain')
            ->latest()
            ->paginate(20);

        return view('system.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('system.tenants.create');
    }

    public function store(Request $request, TenantProvisioningService $provisioning)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:160'],
            'domain' => ['required', 'string', 'max:160'],
            'database' => ['nullable', 'string', 'max:160'],
            'currency_code' => ['required', 'string', 'max:8'],
            'admin_name' => ['required', 'string', 'max:160'],
            'admin_email' => ['required', 'email', 'max:160'],
            'admin_password' => ['required', 'string', 'min:8', 'max:160'],
            'resume' => ['nullable', 'boolean'],
        ]);

        try {
            $tenant = $provisioning->create($data, (bool) $request->boolean('resume'));
        } catch (\Throwable $exception) {
            return back()
                ->withInput($request->except('admin_password'))
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('system.tenants.show', $tenant)
            ->with('success', 'Tenant provisioned successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['domains', 'provisioningEvents' => function ($query): void {
            $query->latest();
        }]);

        return view('system.tenants.show', compact('tenant'));
    }
}
