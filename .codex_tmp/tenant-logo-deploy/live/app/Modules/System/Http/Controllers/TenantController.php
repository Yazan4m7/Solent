<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'admin_username' => ['required', 'string', 'max:160'],
            'admin_email' => ['required', 'email', 'max:160'],
            'admin_password' => ['required', 'string', 'min:8', 'max:160'],
            'client_name' => ['nullable', 'required_with:client_username', 'string', 'max:160'],
            'client_username' => ['nullable', 'string', 'max:160'],
            'client_email' => ['nullable', 'email', 'max:160'],
            'client_password' => ['nullable', 'required_with:client_username', 'string', 'min:8', 'max:160'],
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

    public function editLogo(Tenant $tenant, TenantDatabaseManager $databases)
    {
        try {
            [$connection, $brandingKey] = $this->configureTenantConnection($tenant, $databases);
            $setting = Schema::connection($connection)->hasTable('brand_settings')
                ? DB::connection($connection)->table('brand_settings')->where('tenant', $brandingKey)->first()
                : null;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('system.tenants.index')
                ->with('error', 'Unable to read tenant branding.');
        }

        $currentLogoPath = $setting->logo_path ?? data_get($tenant->branding, 'logo_path');

        return view('system.tenants.logo', compact('tenant', 'currentLogoPath'));
    }

    public function updateLogo(Request $request, Tenant $tenant, TenantDatabaseManager $databases)
    {
        $data = $request->validate([
            'logo' => [
                'required',
                'file',
                'mimetypes:image/png,image/jpeg,image/webp',
                'max:5120',
                'dimensions:min_width=64,min_height=64,max_width=4096,max_height=4096',
            ],
        ]);

        try {
            [$connection, $brandingKey] = $this->configureTenantConnection($tenant, $databases);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to connect to the tenant database.');
        }

        if (!Schema::connection($connection)->hasTable('brand_settings')) {
            return back()->with('error', 'This tenant does not have a branding settings table.');
        }

        $columns = Schema::connection($connection)->getColumnListing('brand_settings');
        if (!in_array('logo_path', $columns, true)) {
            return back()->with('error', 'This tenant does not support a custom logo yet.');
        }

        $file = $data['logo'];
        $extension = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
        ][$file->getMimeType()] ?? null;

        if ($extension === null) {
            return back()->withErrors(['logo' => 'The logo must be a PNG, JPG, or WebP image.']);
        }

        $relativeDirectory = 'tenants/' . $tenant->uuid . '/branding';
        $absoluteDirectory = public_path($relativeDirectory);
        $fileName = 'logo-' . Str::uuid() . '.' . $extension;
        $relativePath = $relativeDirectory . '/' . $fileName;

        try {
            File::ensureDirectoryExists($absoluteDirectory, 0755, true);
            $file->move($absoluteDirectory, $fileName);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to store the uploaded logo.');
        }

        try {
            DB::connection($connection)->transaction(function () use ($connection, $columns, $brandingKey, $relativePath, $tenant): void {
                $existing = DB::connection($connection)
                    ->table('brand_settings')
                    ->where('tenant', $brandingKey)
                    ->first();

                $extra = [];
                if ($existing && isset($existing->extra)) {
                    $extra = json_decode((string) $existing->extra, true) ?: [];
                }
                $extra['mark_path'] = $relativePath;
                $extra['sidebar_mark_path'] = $relativePath;

                $values = [
                    'name' => $tenant->name,
                    'logo_path' => $relativePath,
                    'extra' => json_encode($extra, JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ];
                if (!$existing) {
                    $values['created_at'] = now();
                }
                $values = array_intersect_key($values, array_flip($columns));

                DB::connection($connection)->table('brand_settings')->updateOrInsert(
                    ['tenant' => $brandingKey],
                    $values
                );
            });

            $registryBranding = is_array($tenant->branding) ? $tenant->branding : [];
            $registryBranding['logo_path'] = $relativePath;
            $registryBranding['updated_at'] = now()->toIso8601String();
            $tenant->forceFill(['branding' => $registryBranding])->save();

            $tenant->provisioningEvents()->create([
                'step' => 'branding_logo',
                'status' => 'success',
                'message' => 'Tenant logo updated from platform administration.',
                'payload' => ['logo_path' => $relativePath],
            ]);

            Cache::forget('branding:' . $brandingKey);
        } catch (\Throwable $exception) {
            File::delete(public_path($relativePath));
            report($exception);

            return back()->with('error', 'Unable to update the tenant logo.');
        }

        return redirect()
            ->route('system.tenants.logo.edit', $tenant)
            ->with('success', 'Tenant logo updated successfully.');
    }

    private function configureTenantConnection(Tenant $tenant, TenantDatabaseManager $databases): array
    {
        $tenant->loadMissing('primaryDomain');
        $context = TenantContext::fromTenant($tenant, $tenant->primaryDomain);
        $connection = (string) config('tenancy.tenant_connection', 'tenant');

        config()->set('database.connections.' . $connection, $databases->buildTenantConnectionConfig($context));
        DB::purge($connection);
        DB::reconnect($connection);

        return [$connection, (string) ($tenant->branding_key ?: $tenant->slug)];
    }
}
