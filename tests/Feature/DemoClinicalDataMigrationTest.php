<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoClinicalDataMigrationTest extends TestCase
{
    public function test_demo_clinical_seed_migration_uses_real_tables_and_idempotent_guards(): void
    {
        $migration = $this->migrationContents();

        foreach (['clients', 'cases', 'jobs', 'notes', 'case_logs', 'invoices', 'payments'] as $table) {
            $this->assertStringContainsString("Schema::hasTable('{$table}')", $migration);
            $this->assertStringContainsString("DB::table('{$table}')", $migration);
        }

        foreach (['doctor_id', 'case_id', 'patient_name', 'initial_delivery_date', 'actual_delivery_date'] as $column) {
            $this->assertStringContainsString($column, $migration);
        }

        $this->assertStringContainsString('updateOrInsert', $migration);
        $this->assertStringContainsString('SOLENT-DEMO-', $migration);
        $this->assertStringContainsString("'stage' => -1", $migration);
        $this->assertStringContainsString("'actual_delivery_date' => null", $migration);
        $this->assertStringContainsString('seedDemoCaseLogs', $migration);
    }

    private function migrationContents(): string
    {
        $files = glob(database_path('migrations/*_seed_demo_doctors_and_clinical_cases.php'));

        $this->assertNotEmpty($files, 'Expected demo clinical seed migration to exist.');

        return file_get_contents($files[0]);
    }
}
