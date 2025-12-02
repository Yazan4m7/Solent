<?php

namespace App\Support\Branding;

use App\Modules\Contracts\Branding\BrandingResolverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestBrandingResolver implements BrandingResolverInterface
{
    public function resolveTenant(Request $request): string
    {
        $header = config('branding.resolver.header', 'X-Tenant');
        $queryKey = config('branding.resolver.query', 'tenant');
        $defaultTenant = config('branding.default_tenant', 'default');

        $headerValue = $request->headers->get($header);
        if ($headerValue) {
            return $this->sanitize($headerValue);
        }

        $queryValue = $request->query($queryKey);
        if ($queryValue) {
            return $this->sanitize($queryValue);
        }

        $hostMap = config('branding.resolver.host_map', []);
        $host = $request->getHost();
        if ($host && array_key_exists($host, $hostMap)) {
            return $this->sanitize($hostMap[$host]);
        }

        return $this->sanitize($defaultTenant);
    }

    private function sanitize(string $tenant): string
    {
        return Str::slug($tenant);
    }
}
