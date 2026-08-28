<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccountStatementPrintTest extends TestCase
{
    public function test_account_statement_uses_a_dedicated_a4_print_document(): void
    {
        $statement = file_get_contents(base_path('app/Modules/Clients/Resources/views/clients/statement.blade.php'));
        $controller = file_get_contents(base_path('app/Modules/Clients/Http/Controllers/ClientsController.php'));
        $printLayout = file_get_contents(resource_path('views/layouts/print.blade.php'));

        $this->assertStringContainsString("? 'layouts.print' : 'layouts.app'", $statement);
        $this->assertStringContainsString("'print' => 1", $statement);
        $this->assertStringContainsString('target="_blank"', $statement);
        $this->assertStringContainsString('size: A4 portrait;', $statement);
        $this->assertStringContainsString("$" . "brandingLogoPath ?? config('branding.defaults.logo_path')", $statement);
        $this->assertStringContainsString('background: #ffffff;', $statement);
        $this->assertStringContainsString('max-height: 36px;', $statement);
        $this->assertStringNotContainsString('brandingMarkPath', $statement);
        $this->assertStringNotContainsString('radial-gradient', $statement);
        $this->assertStringNotContainsString('linear-gradient', $statement);
        $this->assertStringNotContainsString('filter: brightness(0) invert(1);', $statement);
        $this->assertStringContainsString("\$request->boolean('print')", $controller);
        $this->assertStringNotContainsString('onclick="PrintStatement()"', $statement);
        $this->assertStringNotContainsString("layouts.navbars.leftsidebar", $printLayout);
        $this->assertStringContainsString("@yield('content')", $printLayout);
        $this->assertStringContainsString("@stack('js')", $printLayout);
    }

    public function test_arabic_print_mode_uses_rtl_localized_statement_content(): void
    {
        app()->setLocale('ar');

        $html = view('clients.statement', [
            'client' => (object) ['id' => 8, 'name' => 'اختبار الطباعة'],
            'transactions' => collect(),
            'from' => '2026-08-01',
            'to' => '2026-08-15',
            'openingBalance' => 0,
            'currencyLabel' => 'JOD',
            'printMode' => true,
        ])->render();

        $this->assertStringContainsString('<body class="print-document">', $html);
        $this->assertStringContainsString('<html lang="ar" dir="rtl">', $html);
        $this->assertStringContainsString('كشف الحساب', $html);
        $this->assertStringContainsString('سجل الحركات', $html);
        $this->assertStringContainsString('الرصيد الختامي', $html);
        $this->assertStringNotContainsString('>Account Statement<', $html);
        $this->assertStringContainsString('text-align: start;', $html);
        $this->assertStringContainsString('direction: ltr;', $html);
        $this->assertStringContainsString('window.print();', $html);
        $this->assertStringNotContainsString('layouts.navbars.leftsidebar', $html);
        $this->assertStringNotContainsString('<div class="solent-floating-topbar"', $html);
    }

    public function test_english_print_mode_uses_ltr_english_statement_content(): void
    {
        app()->setLocale('en');

        $html = view('clients.statement', [
            'client' => (object) ['id' => 8, 'name' => 'Print Test'],
            'transactions' => collect(),
            'from' => '2026-08-01',
            'to' => '2026-08-15',
            'openingBalance' => 0,
            'currencyLabel' => 'JOD',
            'printMode' => true,
        ])->render();

        $this->assertStringContainsString('<html lang="en" dir="ltr">', $html);
        $this->assertStringContainsString('Account Statement', $html);
        $this->assertStringContainsString('Transaction history', $html);
        $this->assertStringContainsString('Closing balance', $html);
        $this->assertStringNotContainsString('كشف الحساب', $html);
    }
}
