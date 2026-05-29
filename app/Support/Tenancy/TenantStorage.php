<?php

namespace App\Support\Tenancy;

use Illuminate\Http\UploadedFile;

class TenantStorage
{
    private TenantContext $context;

    public function __construct(?TenantContext $context = null)
    {
        $this->context = $context ?? (app()->bound('app.tenant_context')
            ? app('app.tenant_context')
            : TenantContext::local(request()->getHost(), config('database.connections.' . config('database.default') . '.database')));
    }

    public function path(string $relativePath): string
    {
        $path = trim(str_replace('\\', '/', $relativePath), '/');

        if ($path === '') {
            return $this->tenantPrefix();
        }

        if (strpos($path, 'tenants/') === 0) {
            return $path;
        }

        if (!$this->context->isResolved()) {
            return $path;
        }

        return $this->tenantPrefix() . '/' . $path;
    }

    public function moveUploadedFile(UploadedFile $file, string $directory, string $name): string
    {
        $tenantDirectory = $this->path($directory);
        if (!is_dir(public_path($tenantDirectory))) {
            mkdir(public_path($tenantDirectory), 0755, true);
        }

        $file->move(public_path($tenantDirectory), $name);

        return trim($tenantDirectory . '/' . $name, '/');
    }

    private function tenantPrefix(): string
    {
        return 'tenants/' . $this->context->cacheKey();
    }
}
