<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Support\DemoMode;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDemoDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class ProvisionTenantDemoDatabases extends Command
{
    protected $signature = 'tenants:demo-databases {--tenant=* : Tenant IDs} {--all : All active tenants} {--local : Current local database} {--apply : Create missing sample databases}';
    protected $description = 'Prepare isolated, populated sample databases without changing real tenant records';

    // Copy clinical/reference data only. Authentication, branding, uploads, queues, logs,
    // integrations, tokens, sessions and cached data are deliberately not copied.
    private const COPY_TABLES = [
        'abutments', 'banks', 'builds', 'case_logs', 'case_tags', 'cases',
        'client_discounts', 'client_materials', 'clients', 'colors', 'devices',
        'discounts', 'failure_causes', 'failure_logs', 'impression_types', 'implants',
        'invoices', 'job_types', 'jobs', 'material_jobtypes', 'material_types',
        'materials', 'migrations', 'monthly_case_counters', 'notes', 'payments',
        'permissions', 'rejected_jobs', 'repeat_cases', 'repeat_causes',
        'stages_for_view_only', 'tags', 'types', 'users', 'user_permissions',
    ];

    public function handle(TenantDemoDatabase $service): int
    {
        if ((int) $this->option('all') + (int) $this->option('local') + (int) !empty($this->option('tenant')) !== 1) {
            $this->error('Choose --tenant=ID, --all, or --local. Omit --apply for a dry run.');
            return 1;
        }
        if ($this->option('local') && ! app()->environment(['local', 'testing'])) {
            $this->error('--local is only available in local/testing environments.');
            return 1;
        }
        $source = (string) config('domain_context.demo.database');
        if ($source === '') {
            $this->error('DEMO_DB_DATABASE must identify a populated sample template.');
            return 1;
        }
        $this->identifier($source);
        $connection = DB::connection($this->option('local') ? null : config('tenancy.landlord_connection', 'landlord'));
        if ($connection->getDriverName() !== 'mysql') {
            $this->error('Provisioning requires MySQL/MariaDB.');
            return 1;
        }
        $query = Tenant::query()->where('status', 'active')->where('database_name', '!=', $source);
        if ($this->option('tenant')) {
            $query->whereIn('id', $this->option('tenant'));
        }
        $contexts = $this->option('local')
            ? collect([TenantContext::local('solent.test', $connection->getDatabaseName())])
            : $query->get()->map(fn ($tenant) => TenantContext::fromTenant($tenant));
        if ($contexts->isEmpty()) {
            $this->error('No eligible tenant matched.');
            return 1;
        }
        // Refuse to treat an active customer's database as the sample template.
        if (! DemoMode::hasIsolatedDatabase(TenantContext::fromArray(['database' => $source]))
            || $source === $connection->getDatabaseName()
            || Tenant::where('database_name', $source)->whereDoesntHave('domains', function ($query) {
                $query->whereIn('host', (array) config('domain_context.demo.hosts'));
            })->exists()) {
            throw new RuntimeException('The configured sample template is not isolated.');
        }
        if (! $connection->table($source . '.cases')->exists()) {
            throw new RuntimeException('The sample template is empty. Seed the public demo first.');
        }
        $tables = collect($connection->select('SELECT TABLE_NAME AS name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ?', [$source, 'BASE TABLE']))->pluck('name');
        $prepared = 0;
        foreach ($contexts as $context) {
            $target = $service->databaseName($context);
            if (Tenant::where('database_name', $target)->exists() || $source === $target || $context->database === $target) {
                throw new RuntimeException('Refusing to overwrite a registered or source database.');
            }
            $exists = $connection->selectOne('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?', [$target]);
            if ($exists) {
                $identity = $connection->table($target . '.' . TenantDemoDatabase::MARKER)->first();
                if (! $identity || $identity->tenant_uuid !== $context->uuid || $identity->real_database !== $context->database
                    || ! $connection->table($target . '.cases')->exists()) {
                    throw new RuntimeException('An existing sample database is incomplete or has a different owner; it was not overwritten.');
                }
                $prepared++;
                continue;
            }
            if (! $this->option('apply')) {
                continue;
            }
            $this->copyDatabase($connection, $source, $target, $tables->all(), $context);
            $prepared++;
        }
        $this->info($this->option('apply')
            ? $prepared . ' isolated sample databases ready. Real records were not changed.'
            : $contexts->count() . ' eligible websites; ' . $prepared . ' already prepared. No changes made.');
        return 0;
    }

    private function copyDatabase($connection, string $source, string $target, array $tables, TenantContext $context): void
    {
        $connection->statement('CREATE DATABASE ' . $this->identifier($target) . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        // CREATE TABLE LIKE does not copy triggers, foreign keys, views or external routines.
        // On failure retain the incomplete copy, without an identity marker; never drop data automatically.
        foreach ($tables as $table) {
            $connection->statement('CREATE TABLE ' . $this->identifier($target) . '.' . $this->identifier($table)
                . ' LIKE ' . $this->identifier($source) . '.' . $this->identifier($table));
        }
        $connection->transaction(function () use ($connection, $source, $target, $tables) {
            foreach (array_intersect(self::COPY_TABLES, $tables) as $table) {
                $connection->statement('INSERT INTO ' . $this->identifier($target) . '.' . $this->identifier($table)
                    . ' SELECT * FROM ' . $this->identifier($source) . '.' . $this->identifier($table));
            }
            $connection->table($target . '.cases')->update([
                'patient_name' => DB::raw("CONCAT('Demo Patient ', id)"),
                'case_id' => DB::raw("CONCAT('DEMO-', id)"),
            ]);
            $connection->table($target . '.users')->update([
                'first_name' => 'Demo', 'last_name' => DB::raw("CONCAT('Employee ', id)"),
                'name_initials' => DB::raw("CONCAT('DE', id)"), 'username' => DB::raw("CONCAT('demo_employee_', id)"),
                'email' => null, 'phone' => null, 'password' => Hash::make(Str::random(64)),
                'remember_token' => null, 'has_photo' => 0, 'is_developer' => 0,
            ]);
            $connection->table($target . '.clients')->update([
                'name' => DB::raw("CONCAT('Demo Clinic ', id)"), 'phone' => '', 'clinic_phone' => null,
                'address' => 'Sample address', 'username' => DB::raw("CONCAT('demo_clinic_', id)"),
                'email' => null, 'password' => Hash::make(Str::random(64)), 'remember_token' => null,
                'doc_password' => null, 'clinic_password' => null, 'doc_notification_token' => null,
                'clinic_notification_token' => null,
            ]);
            foreach ([
                'notes' => ['note' => 'Sample production note.'],
                'payments' => ['notes' => 'Sample payment', 'additional_notes' => null],
                'discounts' => ['reason' => 'Sample discount'],
                'invoices' => ['discount_title' => 'Sample discount'],
                'failure_logs' => ['explanation' => 'Sample production record'],
                'devices' => ['name' => DB::raw("CONCAT('Demo device ', id)"), 'img' => null],
            ] as $table => $values) {
                if (in_array($table, $tables, true)) $connection->table($target . '.' . $table)->update($values);
            }
        });
        $connection->statement('CREATE TABLE ' . $this->identifier($target) . '.' . TenantDemoDatabase::MARKER
            . ' (tenant_uuid VARCHAR(191) NOT NULL, real_database VARCHAR(191) NOT NULL, prepared_at TIMESTAMP NOT NULL)');
        $connection->table($target . '.' . TenantDemoDatabase::MARKER)->insert([
            'tenant_uuid' => $context->uuid, 'real_database' => $context->database, 'prepared_at' => now(),
        ]);
    }

    private function identifier(string $name): string
    {
        if (! preg_match('/^[A-Za-z0-9_]{1,64}$/D', $name)) {
            throw new RuntimeException('Invalid database/table identifier.');
        }
        return '`' . $name . '`';
    }
}
