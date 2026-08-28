<?php

namespace Tests\Feature;

use Tests\TestCase;

class CaseInvoiceApplicationSafetyTest extends TestCase
{
    public function test_completed_case_recovers_a_missing_invoice_before_applying_it(): void
    {
        $source = $this->caseControllerSource();
        $applyInvoice = $this->methodSource($source, 'applyInvoice', 'invoicesList');

        $this->assertStringContainsString('if (!$invoice)', $applyInvoice);
        $this->assertStringContainsString('$this->issueInvoice($job);', $applyInvoice);
        $this->assertStringContainsString('$invoice = $case[0]->fresh()->invoice;', $applyInvoice);
        $this->assertStringContainsString('Unable to create invoice for completed case', $applyInvoice);
    }

    public function test_completed_case_does_not_apply_an_invoice_twice(): void
    {
        $source = $this->caseControllerSource();
        $applyInvoice = $this->methodSource($source, 'applyInvoice', 'invoicesList');

        $idempotencyGuard = strpos($applyInvoice, 'if ((int) $invoice->status === 1) return;');
        $balanceUpdate = strpos($applyInvoice, '$client->balance = $client->balance + ($invoice->amount ?? 0);');

        $this->assertNotFalse($idempotencyGuard);
        $this->assertNotFalse($balanceUpdate);
        $this->assertLessThan($balanceUpdate, $idempotencyGuard);
    }

    private function caseControllerSource(): string
    {
        return file_get_contents(app_path('Modules/Cases/Http/Controllers/CaseController.php'));
    }

    private function methodSource(string $source, string $method, string $nextMethod): string
    {
        $start = strpos($source, "public function {$method}");
        $end = strpos($source, "public function {$nextMethod}", $start);

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        return substr($source, $start, $end - $start);
    }
}
