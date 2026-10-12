<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use App\Modules\Documentation\Http\Controllers\DocumentationController;
use App\sCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tests\TestCase;

class RouteAuditRepairsTest extends TestCase
{
    public function test_payment_invoice_renders_balances_and_currency_without_raw_blade(): void
    {
        $html = view('generic.payment-invoice', [
            'client' => (object) ['name' => 'Audit Doctor', 'balance' => 1250.50],
            'currencyContext' => ['display' => 'USD', 'unit_ar' => 'دولار', 'name_ar' => 'دولار أمريكي'],
        ])->render();

        $this->assertStringContainsString('Audit Doctor', $html);
        $this->assertStringContainsString('id="previousBalance" class="editable-value">1,250.50', $html);
        $this->assertStringContainsString('const initialBalance = 1250.5;', $html);
        $this->assertStringNotContainsString('@php', $html);
        $this->assertStringNotContainsString('{{', $html);
    }

    public function test_documentation_is_printable_when_pdf_renderer_is_not_installed(): void
    {
        if (class_exists('PDF')) {
            $this->markTestSkipped('The PDF renderer is installed.');
        }

        $html = (new DocumentationController())->generatePDF()->render();

        $this->assertStringContainsString('Software Features Documentation', $html);
        $this->assertStringContainsString('Print / Save as PDF', $html);
        $this->assertStringContainsString('window.print()', $html);
    }

    public function test_missing_model_is_a_not_found_response_instead_of_a_server_error(): void
    {
        $exception = (new ModelNotFoundException())->setModel(sCase::class, [99999]);
        $response = app(Handler::class)->render(Request::create('/view-case/99999'), $exception);

        $this->assertSame(404, $response->getStatusCode());
    }
}
