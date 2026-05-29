<?php

namespace App\Support\Tenancy;

use App\Models\TenantDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TenantResolver
{
    public function resolve(Request $request): TenantContext
    {
        $host = TenantContext::normalizeHost($request->getHost());
        $override = $this->localOverride($request);

        if ($override !== null) {
            $host = TenantContext::normalizeHost($override);
        } elseif ($this->isLocalRuntime($request)) {
            return TenantContext::local($host, $this->activeDatabaseName());
        }

        $tenantContext = $this->resolveFromLandlord($host);
        if ($tenantContext !== null) {
            return $tenantContext;
        }

        $legacyContext = $this->resolveFromLegacyConfig($host);
        if ($legacyContext !== null) {
            return $legacyContext;
        }

        return TenantContext::unresolved($host, $host === '' ? 'missing_host' : 'unmapped_host');
    }

    private function resolveFromLandlord(string $host): ?TenantContext
    {
        if ($host === '') {
            return null;
        }

        try {
            $domain = TenantDomain::query()
                ->with('tenant')
                ->where('host', $host)
                ->first();
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }

        if (!$domain || !$domain->tenant) {
            return null;
        }

        return TenantContext::fromTenant($domain->tenant, $domain);
    }

    private function resolveFromLegacyConfig(string $host): ?TenantContext
    {
        $hostsMap = (array) config('domain_context.hosts', []);

        if ($host === '' || !array_key_exists($host, $hostsMap)) {
            return null;
        }

        $default = (array) config('domain_context.default', []);
        $context = array_merge($default, (array) $hostsMap[$host]);
        $database = (string) ($context['database'] ?? '');

        if ($database === '') {
            return TenantContext::unresolved($host, 'invalid_context');
        }

        return TenantContext::fromArray([
            'uuid' => 'legacy-' . sha1($host),
            'slug' => str_replace('.', '-', $host),
            'name' => (string) ($context['country_name'] ?? $host),
            'database' => $database,
            'status' => 'active',
            'domain' => $host,
            'currency_code' => (string) ($context['currency_code'] ?? 'JOD'),
            'branding_key' => (string) ($context['branding_key'] ?? config('branding.default_tenant', 'default')),
            'source' => 'domain_context_config',
        ]);
    }

    private function localOverride(Request $request): ?string
    {
        if (!$this->isLocalRuntime($request)) {
            return null;
        }

        $queryKey = (string) config('tenancy.local_override.query', 'tenant_domain');
        $headerKey = (string) config('tenancy.local_override.header', 'X-Tenant-Domain');
        $queryValue = (string) $request->query($queryKey, '');
        $headerValue = (string) $request->headers->get($headerKey, '');
        $override = trim($queryValue !== '' ? $queryValue : $headerValue);

        return $override !== '' ? $override : null;
    }

    private function isLocalRuntime(Request $request): bool
    {
        if (App::environment(['local', 'development', 'testing'])) {
            return true;
        }

        $host = TenantContext::normalizeHost($request->getHost());
        if ($host === '') {
            return true;
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return true;
        }

        return (bool) preg_match('/\.(test|testing|localhost|local|lan|internal|loc)$/', $host);
    }

    private function activeDatabaseName(): string
    {
        $defaultConnectionName = (string) config('database.default', 'mysql');
        $activeDatabase = config('database.connections.' . $defaultConnectionName . '.database');

        if (!is_string($activeDatabase) || trim($activeDatabase) === '') {
            $activeDatabase = (string) config('database.connections.mysql.database', '');
        }

        return (string) $activeDatabase;
    }
}
