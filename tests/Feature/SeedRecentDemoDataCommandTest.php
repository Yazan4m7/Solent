<?php

namespace Tests\Feature;

use App\Console\Commands\SeedRecentDemoData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use ReflectionMethod;
use Tests\TestCase;

class SeedRecentDemoDataCommandTest extends TestCase
{
    public function test_recent_demo_data_command_is_registered_and_dry_run_is_the_default(): void
    {
        $this->assertArrayHasKey('demo:data', Artisan::all());

        config()->set('domain_context.demo.database', null);

        $this->artisan('demo:data')
            ->expectsOutput('DEMO_DB_DATABASE must be configured before managing demo accounts.')
            ->assertExitCode(1);

        $source = $this->commandSource();
        $this->assertStringContainsString('{--apply', $source);
        $this->assertStringContainsString('Dry run only.', $source);
    }

    public function test_each_client_blueprint_has_ten_recent_cases_and_three_to_six_existing_pairs(): void
    {
        $blueprints = $this->blueprints(17, 2);

        $this->assertCount(10, $blueprints);
        $pairCount = count(array_unique(array_column($blueprints, 'pair_key')));
        $this->assertGreaterThanOrEqual(3, $pairCount);
        $this->assertLessThanOrEqual(6, $pairCount);
        $this->assertGreaterThanOrEqual(3, count(array_unique(array_map(
            fn (array $case): int => (int) $case['pair']['material_id'],
            $blueprints
        ))));
        $this->assertGreaterThanOrEqual(3, count(array_unique(array_map(
            fn (array $case): int => (int) $case['pair']['job_type_id'],
            $blueprints
        ))));

        foreach ($blueprints as $blueprint) {
            $this->assertStringStartsWith('DEMO-RECENT-', $blueprint['case_id']);
            $this->assertGreaterThanOrEqual(
                Carbon::parse('2026-08-19')->subDays(45),
                Carbon::parse($blueprint['created_at'])
            );
        }
    }

    public function test_blueprints_include_real_discounts_repeat_and_modification_shapes(): void
    {
        $blueprints = $this->blueprints(9, 0);
        $repeatCases = array_values(array_filter($blueprints, fn (array $case): bool => $case['is_repeat']));
        $modificationCases = array_values(array_filter($blueprints, fn (array $case): bool => $case['is_modification']));
        $discountedCases = array_values(array_filter($blueprints, fn (array $case): bool => $case['discount_rate'] > 0));

        $this->assertCount(1, $repeatCases);
        $this->assertSame(1, $repeatCases[0]['repeat_of']);
        $this->assertStringEndsWith('_REP', $repeatCases[0]['case_id']);
        $this->assertContains($repeatCases[0]['stage'], $repeatCases[0]['pair']['stage_path']);
        $this->assertCount(1, $modificationCases);
        $this->assertCount(2, $discountedCases);

        $source = $this->commandSource();
        foreach (['repeated_job_id', 'original_job_id', 'modified_job_id', 'insertFailureLog(', "'failure_type' => \$failureType"] as $marker) {
            $this->assertStringContainsString($marker, $source);
        }
        $this->assertStringContainsString("'amount_before_discount' => \$gross", $source);
        $this->assertStringContainsString("'amount' => \$net", $source);
    }

    public function test_each_client_gets_between_two_and_seven_deterministic_payments(): void
    {
        $method = new ReflectionMethod(SeedRecentDemoData::class, 'paymentCountForClient');
        $method->setAccessible(true);
        $command = new SeedRecentDemoData();
        $counts = [];

        foreach (range(1, 40) as $clientId) {
            $count = $method->invoke($command, $clientId);
            $this->assertGreaterThanOrEqual(2, $count);
            $this->assertLessThanOrEqual(7, $count);
            $this->assertSame($count, $method->invoke($command, $clientId));
            $counts[] = $count;
        }

        $this->assertSame(2, min($counts));
        $this->assertSame(7, max($counts));
    }

    public function test_command_never_creates_or_attaches_tags(): void
    {
        $source = $this->commandSource();

        $this->assertStringNotContainsString("table('tags')", $source);
        $this->assertDoesNotMatchRegularExpression(
            "/table\\(['\"]case_tags['\"]\\).*?(insert|updateOrInsert)/s",
            $source
        );
        $this->assertStringContainsString("['New tags', 0]", $source);
        $this->assertStringContainsString("['Tags', 0]", $source);
    }

    private function blueprints(int $clientId, int $clientIndex): array
    {
        $method = new ReflectionMethod(SeedRecentDemoData::class, 'buildClientCaseBlueprints');
        $method->setAccessible(true);

        return $method->invoke(
            new SeedRecentDemoData(),
            $clientId,
            $clientIndex,
            $this->compatiblePairs(),
            Carbon::parse('2026-08-19')->startOfDay()
        );
    }

    private function compatiblePairs(): array
    {
        $pairs = [];
        foreach (range(1, 8) as $index) {
            $pairs[] = [
                'pair_key' => $index . ':' . $index,
                'material_id' => $index,
                'material_name' => 'Material ' . $index,
                'material_price' => 20 + $index,
                'job_type_id' => $index,
                'job_type_name' => $index % 2 === 0 ? 'Bridge' : 'Crown',
                'teeth_or_jaw' => 0,
                'design' => 1,
                'mill' => 1,
                'print_3d' => 0,
                'sinter_furnace' => 1,
                'press_furnace' => 0,
                'metal_work' => 0,
                'finish' => 1,
                'qc' => 1,
                'delivery' => 1,
                'stage_path' => [1, 2, 4, 6, 7, 8],
            ];
        }

        return $pairs;
    }

    private function commandSource(): string
    {
        return file_get_contents(app_path('Console/Commands/SeedRecentDemoData.php'));
    }
}
