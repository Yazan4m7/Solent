<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class InvoiceViewCompilationTest extends TestCase
{
    public function test_invoice_view_initializes_totals_in_a_valid_blade_php_block(): void
    {
        $source = file_get_contents(resource_path('views/generic/invoice-view.blade.php'));
        $compiled = Blade::compileString($source);

        $this->assertStringNotContainsString('@php', $compiled);
        $this->assertStringContainsString('$totalInvoiceAmount = 0;', $compiled);
        $this->assertStringContainsString('$rowCount = 0;', $compiled);
        $this->assertStringContainsString('$caseCost = $case->invoice->amount ?? $totalInvoiceAmount ?? 0;', $compiled);
        $this->assertLessThan(
            strpos($compiled, '$caseCost ='),
            strpos($compiled, '$totalInvoiceAmount = 0;')
        );
    }

    public function test_invoice_view_renders_an_invoice_with_no_billable_job_rows(): void
    {
        $html = view('generic.invoice-view', [
            'case' => (object) [
                'id' => 42,
                'patient_name' => 'Invoice Test',
                'jobs' => collect(),
                'invoice' => (object) ['amount' => 125.50],
                'client' => (object) ['name' => 'Test Doctor', 'balance' => 300],
            ],
        ])->render();

        $this->assertStringContainsString('Invoice Test', $html);
        $this->assertStringContainsString('Test Doctor', $html);
        $this->assertStringContainsString('174.50', $html);
    }

    public function test_invoice_print_css_uses_a4_content_driven_geometry(): void
    {
        $source = file_get_contents(resource_path('views/generic/invoice-view.blade.php'));

        $this->assertStringContainsString('size: A4 portrait;', $source);
        $this->assertStringContainsString('margin: 12mm;', $source);
        $this->assertStringContainsString('box-sizing: border-box;', $source);
        $this->assertStringContainsString('padding: 8mm;', $source);
        $this->assertStringContainsString('height: auto;', $source);
        $this->assertStringContainsString('position: relative;', $source);
        $this->assertStringContainsString('table-layout: fixed;', $source);
        $this->assertStringNotContainsString('size: A6 portrait;', $source);
        $this->assertStringNotContainsString('position: absolute;', $this->printMediaBlock($source));
    }

    private function printMediaBlock(string $source): string
    {
        return substr($source, strpos($source, '@media print'));
    }
}
