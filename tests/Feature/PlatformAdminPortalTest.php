<?php

namespace Tests\Feature;

use App\Http\Middleware\ApplyDomainContext;
use Tests\TestCase;

class PlatformAdminPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('tenancy.platform_admin_host', 'admin.solentjo.com');
        $this->withoutMiddleware(ApplyDomainContext::class);
    }

    public function test_admin_host_root_points_to_tenant_management(): void
    {
        $this->get('http://admin.solentjo.com/')
            ->assertRedirect('http://admin.solentjo.com/system/tenants');
    }

    public function test_admin_host_has_an_isolated_platform_login_page(): void
    {
        $response = $this->get('http://admin.solentjo.com/login');

        $response->assertOk();
        $response->assertSee('Solent');
        $response->assertSee('data-portal="platform-admin"', false);
        $response->assertSee('platform-login__shell', false);
        $response->assertDontSee('tooth_only_logo.png', false);
        $response->assertDontSee('branding.defaults', false);
    }

    public function test_regular_hosts_keep_the_standard_tenant_login(): void
    {
        $response = $this->get('http://solent.test/login');

        $response->assertOk();
        $response->assertSee('class="login-card"', false);
        $response->assertDontSee('data-portal="platform-admin"', false);
        $response->assertDontSee('platform-login__shell', false);
    }
}
