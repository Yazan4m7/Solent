<?php

namespace Tests\Feature;

use ReflectionClass;
use Tests\TestCase;

class DemoExpandedDatasetTest extends TestCase
{
    private object $migration;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();

        require_once database_path('migrations/2026_07_19_000001_expand_demo_clinical_dataset.php');
        $this->migration = new \ExpandDemoClinicalDataset();
        $this->reflection = new ReflectionClass($this->migration);
    }

    public function test_expansion_exactly_doubles_the_original_relational_dataset(): void
    {
        $cases = $this->invoke('cases');
        $newDoctors = $this->property('doctors');
        $existingDoctors = $this->property('existingArabicDoctors');

        $this->assertCount(8, array_merge($existingDoctors, array_column($newDoctors, 'name')));
        $this->assertCount(13, $cases);
        $this->assertSame(16, array_sum(array_map(fn (array $case): int => count($case['jobs']), $cases)));
        $this->assertSame(15, array_sum(array_map(fn (array $case): int => count($case['notes']), $cases)));
        $this->assertSame(54, array_sum(array_map(fn (array $case): int => count($this->invoke('caseLogEntries', [$case])), $cases)));
        $this->assertSame(4, count(array_filter($cases, fn (array $case): bool => $case['invoice_amount'] !== null)));
        $this->assertSame(3, count(array_filter($cases, fn (array $case): bool => ($case['payment_amount'] ?? 0) > 0)));
    }

    public function test_every_demo_client_and_patient_name_contains_arabic_text(): void
    {
        $clientNames = array_merge(
            array_values($this->property('existingArabicDoctors')),
            array_column($this->property('doctors'), 'name')
        );
        $patientNames = array_merge(
            array_values($this->property('existingArabicPatients')),
            array_column($this->invoke('cases'), 'patient_name')
        );

        foreach (array_merge($clientNames, $patientNames) as $name) {
            $this->assertMatchesRegularExpression('/\p{Arabic}/u', $name);
            $this->assertDoesNotMatchRegularExpression('/[A-Za-z]/', $name);
        }
    }

    public function test_configuration_dataset_has_full_demo_breadth(): void
    {
        $this->assertCount(10, $this->property('materialDefinitions'));
        $this->assertCount(7, $this->property('extraJobTypes'));
        $this->assertCount(4, $this->property('extraImpressionTypes'));
        $this->assertCount(8, $this->property('tags'));
        $this->assertCount(8, $this->property('implants'));
        $this->assertCount(8, $this->property('failureCauses'));
        $this->assertCount(16, $this->property('colors'));
    }

    private function property(string $name)
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->migration);
    }

    private function invoke(string $method, array $arguments = [])
    {
        $reflectionMethod = $this->reflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($this->migration, $arguments);
    }
}
