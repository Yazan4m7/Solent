<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TenantPortalRoutingTest extends TestCase
{
    public function test_client_portal_login_does_not_require_employee_authentication(): void
    {
        $route = Route::getRoutes()->getByName('portal.login');

        $this->assertNotNull($route);
        $this->assertNotContains('auth', $route->gatherMiddleware());
        $this->assertNotContains('App\\Http\\Middleware\\Authenticate', $route->gatherMiddleware());
    }

    public function test_client_portal_dashboard_still_requires_client_authentication(): void
    {
        $route = Route::getRoutes()->getByName('portal.dashboard');

        $this->assertNotNull($route);
        $this->assertContains(\App\Http\Middleware\AuthenticateClient::class, $route->gatherMiddleware());
    }
}
