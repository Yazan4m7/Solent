<?php

namespace Tests\Feature;

use Tests\TestCase;

class JordanAreasMigrationTest extends TestCase
{
    public function test_landlord_areas_migration_and_seeder_are_defined(): void
    {
        $migrationFiles = glob(database_path('migrations/*_create_areas_table.php'));
        $seederPath = database_path('seeders/JordanAreasSeeder.php');

        $this->assertNotEmpty($migrationFiles, 'Expected landlord areas migration to exist.');
        $this->assertFileExists($seederPath);

        $migration = file_get_contents($migrationFiles[0]);
        $seeder = file_get_contents($seederPath);

        $this->assertStringContainsString("config('tenancy.landlord_connection', 'landlord')", $migration);
        $this->assertStringContainsString("Schema::connection(\$connection)->create('areas'", $migration);
        $this->assertStringContainsString("\$table->string('name', 100);", $migration);
        $this->assertStringContainsString("\$table->string('city', 100);", $migration);
        $this->assertStringContainsString("\$table->decimal('latitude', 9, 6);", $migration);
        $this->assertStringContainsString("\$table->decimal('longitude', 9, 6);", $migration);
        $this->assertStringContainsString("\$table->unique(['name', 'city'], 'uq_areas_name_city');", $migration);
        $this->assertStringContainsString("DB::connection(\$connection)->table('areas')->updateOrInsert(", $seeder);
        $this->assertSame(59, substr_count($seeder, "['name' => \""));
    }

    public function test_area_seed_dataset_has_expected_grouped_city_counts(): void
    {
        $seeder = file_get_contents(database_path('seeders/JordanAreasSeeder.php'));

        preg_match_all(
            "/\\['name' => \"([^\"]+)\", 'city' => \"([^\"]+)\", 'latitude' => [-\\d.]+, 'longitude' => [-\\d.]+\\]/",
            $seeder,
            $matches,
            PREG_SET_ORDER
        );

        $counts = array_count_values(array_column($matches, 2));
        ksort($counts);

        $this->assertSame([
            'Ajloun' => 2,
            'Amman' => 18,
            'Aqaba' => 4,
            'As-Salt' => 3,
            'Irbid' => 8,
            'Jerash' => 3,
            'Karak' => 3,
            "Ma'an" => 4,
            'Madaba' => 3,
            'Mafraq' => 3,
            'Tafilah' => 2,
            'Zarqa' => 6,
        ], $counts);
    }
}
