<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExceptionMessagingTest extends TestCase
{
    public function test_generic_error_copy_distinguishes_tenants_from_the_demo_domain(): void
    {
        $tenantPage = view('errors.generic', [
            'statusCode' => 500,
            'isDemoRequest' => false,
            'developerMessage' => null,
        ])->render();
        $demoPage = view('errors.generic', [
            'statusCode' => 500,
            'isDemoRequest' => true,
            'developerMessage' => null,
        ])->render();
        $handler = file_get_contents(app_path('Exceptions/Handler.php'));

        $this->assertStringContainsString('Something went wrong. Please try again or return to the previous page.', $tenantPage);
        $this->assertStringNotContainsString('not available in the demo version', $tenantPage);
        $this->assertStringContainsString('The feature/page is not available in the demo version.', $demoPage);
        $this->assertStringContainsString('DemoMode::isDemoRequest($request)', $handler);
    }
}
