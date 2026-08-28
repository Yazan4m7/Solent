<?php

namespace Tests\Feature;

use Tests\TestCase;

class VoucherPrintLayoutTest extends TestCase
{
    public function test_voucher_prints_without_the_application_shell_at_full_width(): void
    {
        $source = file_get_contents(
            base_path('app/Modules/Delivery/Resources/views/delivery/view-voucher.blade.php')
        );
        $printBlock = substr($source, strpos($source, '@media  print'));

        $this->assertStringContainsString('size: A4 portrait;', $printBlock);
        $this->assertStringNotContainsString('size: A6 portrait;', $printBlock);
        $this->assertStringContainsString('html body.white-content .wrapper > .sidebar', $printBlock);
        $this->assertStringContainsString('.solent-floating-topbar', $printBlock);
        $this->assertStringContainsString('.solent-quick-nav', $printBlock);
        $this->assertStringContainsString('.wrapper > .main-panel', $printBlock);
        $this->assertStringContainsString('.voucher-page > .col-md-12', $printBlock);
        $this->assertStringContainsString('class="row voucher-page"', $source);
        $this->assertStringNotContainsString('voucher-release', $source);
    }
}
