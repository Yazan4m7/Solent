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
            'admin_username' => 'owner',
            'admin_email' => 'admin@xlab.test',
            'admin_password' => 'password123',
            'client_name' => 'First Client',
            'client_username' => 'client',
            'client_password' => 'client123',
        ]);

        $this->assertSame('xlab_kordent', $payload['database']);
        $this->assertSame('owner', $payload['admin_username']);
        $this->assertSame('client', $payload['client_username']);
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

    public function test_it_randomizes_seeded_job_types_with_teeth_majority_after_migrations(): void
    {
        $service = file_get_contents(app_path('Support/Tenancy/TenantProvisioningService.php'));

        $this->assertStringContainsString('randomizeSeededJobTypeSelection();', $service);
        $this->assertStringContainsString("'job_types', 'success'", $service);
        $this->assertStringContainsString("update(['teeth_or_jaw' => 0])", $service);
        $this->assertStringContainsString("update(['teeth_or_jaw' => 1])", $service);
        $this->assertStringContainsString("floor(\$total * 0.3)", $service);
        $this->assertStringContainsString("floor((\$total - 1) / 2)", $service);
    }

    public function test_seeded_job_types_use_the_job_type_screen_value_convention(): void
    {
        $foundationSeed = file_get_contents(database_path('migrations/2026_05_16_000001_seed_demo_foundation_data.php'));
        $clinicalSeed = file_get_contents(database_path('migrations/2026_05_30_001800_seed_demo_doctors_and_clinical_cases.php'));
        $expandedSeed = file_get_contents(database_path('migrations/2026_07_19_000001_expand_demo_clinical_dataset.php'));

        foreach ([$foundationSeed, $clinicalSeed, $expandedSeed] as $seed) {
            $this->assertStringNotContainsString("'teeth_or_jaw' => 2", $seed);
        }

        $this->assertStringContainsString("'Crown', 'teeth_or_jaw' => 0", $foundationSeed);
        $this->assertStringContainsString("'Surgical Guide', 'teeth_or_jaw' => 1", $foundationSeed);
        $this->assertStringContainsString("'Full Contour Crown', 'teeth_or_jaw' => 0", $expandedSeed);
        $this->assertStringContainsString("'Denture', 'teeth_or_jaw' => 1", $expandedSeed);
    }
}
