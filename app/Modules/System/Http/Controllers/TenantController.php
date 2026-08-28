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
        $showDisabled = request()->query('status') === 'disabled';
        $tenants = Tenant::query()
            ->with('primaryDomain')
            ->when(
                $showDisabled,
                fn ($query) => $query->where('status', 'disabled'),
                fn ($query) => $query->where('status', '!=', 'disabled')
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('system.tenants.index', compact('tenants', 'showDisabled'));
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

    public function disable(Tenant $tenant)
    {
        if ($tenant->status !== 'active') {
            return back()->with('error', 'Only an active tenant can be disabled.');
        }

        $tenant->forceFill(['status' => 'disabled'])->save();
        $tenant->provisioningEvents()->create([
            'step' => 'access_status',
            'status' => 'success',
            'message' => 'Tenant access disabled from platform administration.',
        ]);

        return back()->with('success', 'Tenant disabled successfully.');
    }

    public function enable(Tenant $tenant)
    {
        if ($tenant->status !== 'disabled') {
            return back()->with('error', 'Only a disabled tenant can be enabled.');
        }

        $tenant->forceFill(['status' => 'active'])->save();
        $tenant->provisioningEvents()->create([
            'step' => 'access_status',
            'status' => 'success',
            'message' => 'Tenant access enabled from platform administration.',
        ]);

        return back()->with('success', 'Tenant enabled successfully.');
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

        $settingExtra = $setting && isset($setting->extra)
            ? (json_decode((string) $setting->extra, true) ?: [])
            : [];
        $registryBranding = is_array($tenant->branding) ? $tenant->branding : [];

        $currentLoginLogoPath = $settingExtra['login_logo_path']
            ?? data_get($registryBranding, 'login_logo_path')
            ?? config('branding.defaults.login_logo_path');
        $currentSidebarLogoPath = $settingExtra['sidebar_mark_path']
            ?? data_get($registryBranding, 'sidebar_mark_path')
            ?? config('branding.defaults.sidebar_mark_path');

        return view('system.tenants.logo', compact('tenant', 'currentLoginLogoPath', 'currentSidebarLogoPath'));
    }

    public function updateLogo(Request $request, Tenant $tenant, TenantDatabaseManager $databases)
    {
        $logoRules = [
            'required',
            'file',
            'mimetypes:image/png,image/jpeg,image/webp',
            'max:5120',
            'dimensions:min_width=64,min_height=64,max_width=4096,max_height=4096',
        ];
        $data = $request->validate([
            'login_logo' => $logoRules,
            'sidebar_logo' => $logoRules,
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
            return back()->with('error', 'This tenant does not support custom logos yet.');
        }

        $relativeDirectory = 'tenants/' . $tenant->uuid . '/branding';
        $absoluteDirectory = public_path($relativeDirectory);
        $relativePaths = [];
        $filePrefixes = [
            'login_logo' => 'login-logo',
            'sidebar_logo' => 'sidebar-logo',
        ];

        foreach ($filePrefixes as $field => $prefix) {
            $file = $data[$field];
            $extension = [
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
            ][$file->getMimeType()] ?? null;

            if ($extension === null) {
                return back()->withErrors([$field => 'The logo must be a PNG, JPG, or WebP image.']);
            }

            $relativePaths[$field] = $relativeDirectory . '/' . $prefix . '-' . Str::uuid() . '.' . $extension;
        }

        try {
            File::ensureDirectoryExists($absoluteDirectory, 0755, true);
            foreach ($relativePaths as $field => $relativePath) {
                $data[$field]->move($absoluteDirectory, basename($relativePath));
            }
        } catch (\Throwable $exception) {
            foreach ($relativePaths as $relativePath) {
                File::delete(public_path($relativePath));
            }
            report($exception);

            return back()->with('error', 'Unable to store the uploaded logos.');
        }

        try {
            DB::connection($connection)->transaction(function () use ($connection, $columns, $brandingKey, $relativePaths, $tenant): void {
                $existing = DB::connection($connection)
                    ->table('brand_settings')
                    ->where('tenant', $brandingKey)
                    ->first();

                $extra = [];
                if ($existing && isset($existing->extra)) {
                    $extra = json_decode((string) $existing->extra, true) ?: [];
                }
                $extra['mark_path'] = $relativePaths['login_logo'];
                $extra['login_logo_path'] = $relativePaths['login_logo'];
                $extra['sidebar_mark_path'] = $relativePaths['sidebar_logo'];

                $values = [
                    'name' => $tenant->name,
                    'logo_path' => $relativePaths['login_logo'],
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
            $registryBranding['logo_path'] = $relativePaths['login_logo'];
            $registryBranding['login_logo_path'] = $relativePaths['login_logo'];
            $registryBranding['sidebar_mark_path'] = $relativePaths['sidebar_logo'];
            $registryBranding['updated_at'] = now()->toIso8601String();
            $tenant->forceFill(['branding' => $registryBranding])->save();

            $tenant->provisioningEvents()->create([
                'step' => 'branding_logos',
                'status' => 'success',
                'message' => 'Tenant login and sidebar logos updated from platform administration.',
                'payload' => [
                    'login_logo_path' => $relativePaths['login_logo'],
                    'sidebar_mark_path' => $relativePaths['sidebar_logo'],
                ],
            ]);

            Cache::forget('branding:' . $brandingKey);
        } catch (\Throwable $exception) {
            foreach ($relativePaths as $relativePath) {
                File::delete(public_path($relativePath));
            }
            report($exception);

            return back()->with('error', 'Unable to update the tenant logos.');
        }

        return redirect()
            ->route('system.tenants.logo.edit', $tenant)
            ->with('success', 'Tenant login and sidebar logos updated successfully.');
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
