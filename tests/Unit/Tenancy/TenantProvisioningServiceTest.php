<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantProvisioningService;
use ReflectionClass;
use Tests\TestCase;

class TenantProvisioningServiceTest extends TestCase
{
    public function test_it_generates_default_database_name_from_slug_with_kordent_suffix(): void
    {
        $service = new TenantProvisioningService(new TenantDatabaseManager());
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('normalizePayload');
        $method->setAccessible(true);

        $payload = $method->invoke($service, [
            'slug' => 'xlab',
            'name' => 'X Lab',
            'domain' => 'xlab.kordent.korviongroup.com',
            'admin_email' => 'admin@xlab.test',
            'admin_password' => 'password123',
        ]);

        $this->assertSame('xlab_kordent', $payload['database']);
    }

    public function test_it_marks_migrations_that_are_already_covered_by_the_base_schema(): void
    {
        $service = new TenantProvisioningService(new TenantDatabaseManager());
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('baseSchemaCoveredMigrations');
        $method->setAccessible(true);

        $migrations = $method->invoke($service);

        $this->assertContains('0001_01_01_000000_create_users_table', $migrations);
        $this->assertContains('0001_01_01_000002_create_jobs_table', $migrations);
        $this->assertContains('2019_12_14_000001_create_personal_access_tokens_table', $migrations);
    }

    public function test_it_splits_base_schema_sql_into_individual_statements(): void
    {
        $service = new TenantProvisioningService(new TenantDatabaseManager());
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('schemaStatements');
        $method->setAccessible(true);

        $statements = $method->invoke($service, "SET SQL_MODE = '';\nCREATE TABLE `cases` (`id` bigint not null);\nCOMMIT;\n");

        $this->assertSame([
            "SET SQL_MODE = ''",
            'CREATE TABLE `cases` (`id` bigint not null)',
            'COMMIT',
        ], $statements);
    }
}
