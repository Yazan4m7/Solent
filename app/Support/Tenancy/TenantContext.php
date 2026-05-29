<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;

class TenantContext
{
    public ?int $tenantId;
    public string $uuid;
    public string $slug;
    public string $name;
    public string $database;
    public string $status;
    public string $domain;
    public string $currencyCode;
    public string $brandingKey;
    public string $source;
    public ?string $reason;

    public function __construct(
        ?int $tenantId,
        string $uuid,
        string $slug,
        string $name,
        string $database,
        string $status,
        string $domain,
        string $currencyCode = 'JOD',
        string $brandingKey = '',
        string $source = 'landlord',
        ?string $reason = null
    ) {
        $this->tenantId = $tenantId;
        $this->uuid = $uuid;
        $this->slug = $slug;
        $this->name = $name;
        $this->database = $database;
        $this->status = strtolower(trim($status ?: 'unknown'));
        $this->domain = self::normalizeHost($domain);
        $this->currencyCode = strtoupper(trim($currencyCode ?: 'JOD'));
        $this->brandingKey = $brandingKey !== '' ? $brandingKey : ($slug !== '' ? $slug : $uuid);
        $this->source = $source;
        $this->reason = $reason;
    }

    public static function fromTenant(Tenant $tenant, ?TenantDomain $domain = null): self
    {
        return new self(
            $tenant->id,
            (string) $tenant->uuid,
            (string) $tenant->slug,
            (string) $tenant->name,
            (string) $tenant->database_name,
            (string) $tenant->status,
            (string) optional($domain)->host,
            (string) ($tenant->currency_code ?? 'JOD'),
            (string) ($tenant->branding_key ?? $tenant->slug),
            'landlord'
        );
    }

    public static function fromArray(array $data): self
    {
        $slug = (string) ($data['slug'] ?? $data['tenant'] ?? $data['uuid'] ?? '');
        $uuid = (string) ($data['uuid'] ?? ($slug !== '' ? $slug : 'unresolved'));

        return new self(
            isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
            $uuid,
            $slug,
            (string) ($data['name'] ?? $data['tenant_name'] ?? $slug),
            (string) ($data['database'] ?? $data['database_name'] ?? ''),
            (string) ($data['status'] ?? 'active'),
            (string) ($data['domain'] ?? $data['host'] ?? ''),
            (string) ($data['currency_code'] ?? 'JOD'),
            (string) ($data['branding_key'] ?? $slug),
            (string) ($data['source'] ?? 'array'),
            $data['reason'] ?? null
        );
    }

    public static function local(?string $host, ?string $database): self
    {
        return new self(
            null,
            'local',
            'local',
            'Local',
            (string) $database,
            'active',
            (string) $host,
            (string) config('domain_context.default.currency_code', 'JOD'),
            (string) config('branding.default_tenant', 'default'),
            'local_bypass'
        );
    }

    public static function unresolved(?string $host, string $reason): self
    {
        return new self(
            null,
            'unresolved',
            'unresolved',
            'Unresolved Tenant',
            '',
            'unresolved',
            (string) $host,
            'JOD',
            '',
            'unresolved',
            $reason
        );
    }

    public function isResolved(): bool
    {
        return $this->status !== 'unresolved' && $this->database !== '';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isLocal(): bool
    {
        return $this->source === 'local_bypass';
    }

    public function cacheKey(): string
    {
        return $this->uuid !== '' ? $this->uuid : $this->slug;
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'name' => $this->name,
            'database' => $this->database,
            'status' => $this->status,
            'domain' => $this->domain,
            'currency_code' => $this->currencyCode,
            'branding_key' => $this->brandingKey,
            'source' => $this->source,
            'reason' => $this->reason,
        ];
    }

    public function domainContext(): array
    {
        return array_merge((array) config('domain_context.default', []), [
            'tenant_id' => $this->tenantId,
            'tenant_uuid' => $this->uuid,
            'tenant_slug' => $this->slug,
            'tenant_name' => $this->name,
            'matched' => $this->isResolved(),
            'source' => $this->source,
            'host' => $this->domain,
            'database' => $this->database,
            'currency_code' => $this->currencyCode,
            'branding_key' => $this->brandingKey,
        ]);
    }

    public static function normalizeHost(?string $host): string
    {
        $normalized = strtolower(trim((string) $host));

        if (strpos($normalized, 'www.') === 0) {
            return substr($normalized, 4);
        }

        return $normalized;
    }
}
