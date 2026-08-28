<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\TenantProvisioningEvent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TenantProvisioningService
{
    private TenantDatabaseManager $databaseManager;

    public function __construct(TenantDatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function create(array $input, bool $resume = false): Tenant
    {
        $payload = $this->normalizePayload($input);
        $originalDefaultConnection = config('database.default');

        $tenant = $this->findExistingTenant($payload);
        if ($tenant && !$resume) {
            throw new InvalidArgumentException('Tenant slug, domain, or database already exists. Use --resume only for failed/provisioning tenants.');
        }

        if ($tenant && !in_array($tenant->status, ['failed', 'provisioning'], true)) {
            throw new InvalidArgumentException('Only failed or provisioning tenants can be resumed.');
        }

        try {
            if (!$tenant) {
                $tenant = $this->createRegistryRecords($payload);
            }

            $tenant->update([
                'status' => 'provisioning',
                'failed_at' => null,
                'failed_step' => null,
                'failure_message' => null,
            ]);

            $domain = $tenant->primaryDomain ?: $tenant->domains()->first();

            $this->record($tenant, 'registry', 'success', 'Tenant registry is ready.');
            $this->createTenantDatabase($tenant);
            $this->record($tenant, 'database', 'success', 'Tenant database is ready.');

            $context = TenantContext::fromTenant($tenant, $domain);
            $this->databaseManager->connect($context);

            $this->runTenantBaseSchema();
            $this->record($tenant, 'schema', 'success', 'Tenant base schema is ready.');

            if ((bool) config('tenancy.provisioning.run_migrations', true)) {
                $this->runTenantMigrations();
            }
            $this->record($tenant, 'migrations', 'success', 'Tenant schema is ready.');

            $this->randomizeSeededJobTypeSelection();
            $this->record($tenant, 'job_types', 'success', 'Seeded job types have a teeth-majority teeth/jaw mix.');

            $this->createAdminUser($payload);
            $this->record($tenant, 'admin_user', 'success', 'First tenant admin is ready.');

            if ($payload['client_username'] !== '') {
                $this->createClientUser($payload);
                $this->record($tenant, 'client_user', 'success', 'First client portal account is ready.');
            }

            $this->createBranding($tenant, $payload);
            $this->record($tenant, 'branding', 'success', 'Tenant branding is ready.');

            $tenant->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);
            $this->record($tenant, 'activation', 'success', 'Tenant is active.');

            return $tenant->fresh(['domains', 'provisioningEvents']);
        } catch (\Throwable $exception) {
            if ($tenant) {
                $tenant->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failed_step' => $this->inferFailedStep($tenant),
                    'failure_message' => $exception->getMessage(),
                ]);
                $this->record($tenant, $tenant->failed_step ?: 'unknown', 'failed', $exception->getMessage());
            }

            throw $exception;
        } finally {
            config()->set('database.default', $originalDefaultConnection);
        }
    }

    private function normalizePayload(array $input): array
    {
        $slug = Str::slug((string) ($input['slug'] ?? ''));
        if ($slug === '') {
            throw new InvalidArgumentException('Tenant slug is required.');
        }

        $domain = TenantContext::normalizeHost((string) ($input['domain'] ?? ''));
        $domainSuffix = trim((string) config('tenancy.provisioning.default_domain_suffix', ''));
        if ($domain === '' && $domainSuffix !== '') {
            $domain = $slug . '.' . ltrim($domainSuffix, '.');
        }
        if ($domain === '') {
            throw new InvalidArgumentException('Tenant domain is required.');
        }

        $databaseName = (string) ($input['database'] ?? '');
        if ($databaseName === '') {
            $databaseName = (string) config('tenancy.provisioning.database_prefix', '')
                . str_replace('-', '_', $slug)
                . (string) config('tenancy.provisioning.database_suffix', '_kordent');
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $databaseName)) {
            throw new InvalidArgumentException('Tenant database name may only contain letters, numbers, and underscores.');
        }

        $adminEmail = strtolower(trim((string) ($input['admin_email'] ?? '')));
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('A valid admin email is required.');
        }

        $adminPassword = (string) ($input['admin_password'] ?? '');
        if (strlen($adminPassword) < 8) {
            throw new InvalidArgumentException('Admin password must be at least 8 characters.');
        }

        $adminUsername = trim((string) ($input['admin_username'] ?? ''));
        if ($adminUsername === '') {
            $adminUsername = $adminEmail;
        }

        $clientUsername = trim((string) ($input['client_username'] ?? ''));
        $clientPassword = (string) ($input['client_password'] ?? '');
        if ($clientUsername !== '' && strlen($clientPassword) < 8) {
            throw new InvalidArgumentException('Client password must be at least 8 characters.');
        }

        $clientEmail = strtolower(trim((string) ($input['client_email'] ?? '')));
        if ($clientEmail !== '' && !filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Client email must be valid when provided.');
        }

        $name = trim((string) ($input['name'] ?? $slug));

        return [
            'slug' => $slug,
            'name' => $name !== '' ? $name : $slug,
            'domain' => $domain,
            'database' => $databaseName,
            'currency_code' => strtoupper((string) ($input['currency_code'] ?? 'JOD')),
            'branding_key' => Str::slug((string) ($input['branding_key'] ?? $slug)),
            'admin_name' => trim((string) ($input['admin_name'] ?? 'Tenant Admin')),
            'admin_username' => $adminUsername,
            'admin_email' => $adminEmail,
            'admin_password' => $adminPassword,
            'client_name' => trim((string) ($input['client_name'] ?? 'Client')) ?: 'Client',
            'client_username' => $clientUsername,
            'client_email' => $clientEmail,
            'client_password' => $clientPassword,
        ];
    }

    private function findExistingTenant(array $payload): ?Tenant
    {
        $tenant = Tenant::query()
            ->where('slug', $payload['slug'])
            ->orWhere('database_name', $payload['database'])
            ->first();

        if ($tenant) {
            return $tenant;
        }

        $domain = TenantDomain::query()->where('host', $payload['domain'])->first();

        return $domain ? $domain->tenant : null;
    }

    private function createRegistryRecords(array $payload): Tenant
    {
        return DB::connection(config('tenancy.landlord_connection', 'landlord'))->transaction(function () use ($payload): Tenant {
            $tenant = Tenant::query()->create([
                'uuid' => (string) Str::uuid(),
                'slug' => $payload['slug'],
                'name' => $payload['name'],
                'database_name' => $payload['database'],
                'status' => 'provisioning',
                'currency_code' => $payload['currency_code'],
                'branding_key' => $payload['branding_key'],
                'context' => [
                    'domain' => $payload['domain'],
                ],
            ]);

            $tenant->domains()->create([
                'host' => $payload['domain'],
                'is_primary' => true,
            ]);

            return $tenant->fresh(['domains']);
        });
    }

    private function createTenantDatabase(Tenant $tenant): void
    {
        $template = (string) config('tenancy.tenant_connection_template', 'mysql');
        $config = (array) config('database.connections.' . $template, config('database.connections.mysql', []));
        $driver = (string) ($config['driver'] ?? 'mysql');

        if ($driver === 'sqlite') {
            $path = (string) $tenant->database_name;
            if ($path !== ':memory:' && !file_exists($path)) {
                touch($path);
            }

            return;
        }

        if ($driver !== 'mysql') {
            throw new InvalidArgumentException('Tenant database provisioning currently supports mysql and sqlite connections.');
        }

        $databaseName = str_replace('`', '``', $tenant->database_name);
        DB::connection(config('tenancy.landlord_connection', 'landlord'))
            ->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    private function runTenantMigrations(): void
    {
        Artisan::call('migrate', [
            '--database' => config('tenancy.tenant_connection', 'tenant'),
            '--force' => true,
        ]);
    }

    private function runTenantBaseSchema(): void
    {
        $connection = (string) config('tenancy.tenant_connection', 'tenant');
        $schemaPath = (string) config('tenancy.provisioning.base_schema_path', database_path('database_schema.sql'));

        if ($schemaPath === '' || !is_file($schemaPath)) {
            return;
        }

        if (Schema::connection($connection)->hasTable('cases')) {
            $this->markBaseSchemaMigrationsApplied($connection);

            return;
        }

        $schemaSql = trim((string) file_get_contents($schemaPath));
        if ($schemaSql === '') {
            return;
        }

        foreach ($this->schemaStatements($schemaSql) as $statement) {
            DB::connection($connection)->unprepared($statement);
        }

        $this->markBaseSchemaMigrationsApplied($connection);
    }

    private function schemaStatements(string $schemaSql): array
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $schemaSql);
        $statements = preg_split('/;\s*(?:\n|$)/', $normalized);

        return array_values(array_filter(array_map('trim', $statements ?: []), function (string $statement): bool {
            return $statement !== '';
        }));
    }

    private function markBaseSchemaMigrationsApplied(string $connection): void
    {
        if (!Schema::connection($connection)->hasTable('migrations')) {
            return;
        }

        $query = DB::connection($connection)->table('migrations');
        $batch = (int) $query->max('batch');
        $batch = $batch > 0 ? $batch : 1;

        foreach ($this->baseSchemaCoveredMigrations() as $migration) {
            DB::connection($connection)->table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => $batch]
            );
        }
    }

    private function baseSchemaCoveredMigrations(): array
    {
        return array_values(array_filter(array_map('strval', (array) config('tenancy.provisioning.base_schema_migrations', [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000002_create_jobs_table',
            '2019_12_14_000001_create_personal_access_tokens_table',
        ]))));
    }

    private function randomizeSeededJobTypeSelection(): void
    {
        $connection = (string) config('tenancy.tenant_connection', 'tenant');
        if (
            !Schema::connection($connection)->hasTable('job_types') ||
            !Schema::connection($connection)->hasColumn('job_types', 'teeth_or_jaw')
        ) {
            return;
        }

        $query = DB::connection($connection)->table('job_types')->orderBy('id');
        if (Schema::connection($connection)->hasColumn('job_types', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $ids = $query->pluck('id')->all();
        $total = count($ids);
        if ($total === 0) {
            return;
        }

        shuffle($ids);

        $jawCount = $total >= 3
            ? max(1, min((int) floor($total * 0.3), (int) floor(($total - 1) / 2)))
            : 0;
        $jawIds = array_slice($ids, 0, $jawCount);

        DB::connection($connection)->table('job_types')->whereIn('id', $ids)->update(['teeth_or_jaw' => 0]);
        if (count($jawIds) > 0) {
            DB::connection($connection)->table('job_types')->whereIn('id', $jawIds)->update(['teeth_or_jaw' => 1]);
        }
    }

    private function createAdminUser(array $payload): void
    {
        $connection = (string) config('tenancy.tenant_connection', 'tenant');
        if (!Schema::connection($connection)->hasTable('users')) {
            throw new InvalidArgumentException('Tenant users table does not exist after migration.');
        }

        $columns = Schema::connection($connection)->getColumnListing('users');
        $nameParts = preg_split('/\s+/', trim($payload['admin_name']));
        $firstName = $nameParts[0] ?? 'Tenant';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Admin';

        $candidate = [
            'name' => $payload['admin_name'],
            'email' => $payload['admin_email'],
            'username' => $payload['admin_username'],
            'password' => Hash::make($payload['admin_password']),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name_initials' => strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)),
            'phone' => null,
            'active' => 1,
            'status' => 1,
            'is_admin' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $payload = array_intersect_key($candidate, array_flip($columns));

        if (!array_key_exists('username', $payload) && array_key_exists('email', $payload)) {
            $payload['email'] = $candidate['username'];
        }

        $identityColumn = array_key_exists('username', $payload) ? 'username' : 'email';
        $existing = DB::connection($connection)->table('users')->where($identityColumn, $payload[$identityColumn])->first();
        if ($existing) {
            DB::connection($connection)->table('users')->where('id', $existing->id)->update($payload);

            return;
        }

        DB::connection($connection)->table('users')->insert($payload);
    }

    private function createClientUser(array $payload): void
    {
        $connection = (string) config('tenancy.tenant_connection', 'tenant');
        if (!Schema::connection($connection)->hasTable('clients')) {
            throw new InvalidArgumentException('Tenant clients table does not exist after migration.');
        }

        $columns = Schema::connection($connection)->getColumnListing('clients');
        $candidate = [
            'name' => $payload['client_name'],
            'phone' => '',
            'address' => '',
            'active' => 1,
            'username' => $payload['client_username'],
            'email' => $payload['client_email'] !== '' ? $payload['client_email'] : null,
            'password' => Hash::make($payload['client_password']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $client = array_intersect_key($candidate, array_flip($columns));

        $existing = DB::connection($connection)
            ->table('clients')
            ->where('username', $payload['client_username'])
            ->first();

        if ($existing) {
            DB::connection($connection)->table('clients')->where('id', $existing->id)->update($client);

            return;
        }

        DB::connection($connection)->table('clients')->insert($client);
    }

    private function createBranding(Tenant $tenant, array $payload): void
    {
        $connection = (string) config('tenancy.tenant_connection', 'tenant');
        if (!Schema::connection($connection)->hasTable('brand_settings')) {
            return;
        }

        DB::connection($connection)->table('brand_settings')->updateOrInsert(
            ['tenant' => $payload['branding_key']],
            [
                'name' => $tenant->name,
                'primary_color' => config('branding.defaults.primary_color'),
                'secondary_color' => config('branding.defaults.secondary_color'),
                'accent_color' => config('branding.defaults.accent_color'),
                'background_color' => config('branding.defaults.background_color'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    private function record(?Tenant $tenant, string $step, string $status, ?string $message = null, array $payload = []): void
    {
        TenantProvisioningEvent::query()->create([
            'tenant_id' => $tenant ? $tenant->id : null,
            'step' => $step,
            'status' => $status,
            'message' => $message,
            'payload' => $payload ?: null,
        ]);
    }

    private function inferFailedStep(Tenant $tenant): string
    {
        $lastSuccess = $tenant->provisioningEvents()->where('status', 'success')->latest()->first();
        $order = ['registry', 'database', 'schema', 'migrations', 'admin_user', 'client_user', 'branding', 'activation'];
        $lastStep = $lastSuccess ? $lastSuccess->step : null;
        $index = array_search($lastStep, $order, true);

        return $order[$index === false ? 0 : min($index + 1, count($order) - 1)];
    }
}
