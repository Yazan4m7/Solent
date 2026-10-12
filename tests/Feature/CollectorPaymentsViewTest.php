<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CollectorPaymentsViewTest extends TestCase
{
    public function test_collector_payments_content_renders_filters_totals_and_rows(): void
    {
        $this->be(new User(['id' => 1, 'is_admin' => true]));

        $html = $this->renderContent([
            'from' => '2026-10-01 00:00',
            'to' => '2026-10-12 23:59',
            'currencyContext' => ['display' => 'USD'],
            'clients' => collect([(object) ['id' => 7, 'name' => 'Test Doctor']]),
            'collectors' => collect([(object) ['id' => 9, 'first_name' => 'Test', 'last_name' => 'Collector']]),
            'selectedClients' => [7],
            'selectedCollectors' => [9],
            'payments' => collect([(object) [
                'id' => 42,
                'amount' => 1250,
                'collectorUserRecord' => (object) ['name_initials' => 'TC'],
                'client' => (object) ['name' => 'Test Doctor'],
                'from_bank' => false,
                'created_at' => '2026-10-12 10:30:00',
                'recieved_on' => null,
            ]]),
        ]);

        $this->assertStringContainsString('value="2026-10-01 00:00"', $html);
        $this->assertStringContainsString('value="2026-10-12 23:59"', $html);
        $this->assertStringContainsString('value="7" selected', $html);
        $this->assertStringContainsString('value="9" selected', $html);
        $this->assertStringContainsString('Test Collector', $html);
        $this->assertStringContainsString('1,250</span>', $html);
        $this->assertStringContainsString('1250 USD', $html);
        $this->assertStringContainsString('actionsDialog42', $html);
        $this->assertStringContainsString('Receive </button>', $html);
        $this->assertStringContainsString('Delete Payment</button>', $html);
        $this->assertNoRawBlade($html);
    }

    public function test_collector_payments_content_renders_empty_results_without_optional_filters(): void
    {
        $this->be(new User(['id' => 1]));

        $html = $this->renderContent(['payments' => collect()]);

        $this->assertStringContainsString('0</span>', $html);
        $this->assertStringContainsString('JOD</span>', $html);
        $this->assertStringNotContainsString('name="doctor[]"', $html);
        $this->assertStringNotContainsString('name="collectors[]"', $html);
        $this->assertNoRawBlade($html);
    }

    private function renderContent(array $data): string
    {
        $source = file_get_contents(app_path('Modules/Delivery/Resources/views/delivery/receive-payment.blade.php'));
        // Exercise the actual content without the shared layout's unrelated data queries.
        $source = preg_replace('/^@extends\([^\r\n]+\)\s*/', '', $source, 1);
        $compiled = Blade::compileString($source);
        $__env = app('view');
        extract($data, EXTR_SKIP);

        ob_start();
        try {
            eval('?>' . $compiled);

            return $__env->yieldContent('content');
        } finally {
            ob_end_clean();
            $__env->flushState();
        }
    }

    private function assertNoRawBlade(string $html): void
    {
        $this->assertStringNotContainsString('@if', $html);
        $this->assertStringNotContainsString('@endif', $html);
        $this->assertStringNotContainsString('@php', $html);
        $this->assertStringNotContainsString('{{', $html);
    }
}
