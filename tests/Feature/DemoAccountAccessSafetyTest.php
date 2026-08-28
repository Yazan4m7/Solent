<?php

namespace Tests\Feature;

use App\Http\Middleware\EnforceDemoReadOnly;
use App\Support\DemoMode;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class DemoAccountAccessSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('domain_context.demo.enabled', true);
        config()->set('domain_context.demo.read_only', false);
    }

    public function test_solent_demo_domain_is_always_recognized_as_demo_mode(): void
    {
        $request = Request::create('https://demo.solentjo.com/main-dashboard', 'GET');

        $this->assertContains('demo.solentjo.com', config('domain_context.demo.hosts'));
        $this->assertTrue(DemoMode::isDemoRequest($request));
    }

    public function test_demo_read_only_blocks_legacy_mutating_get_routes(): void
    {
        config()->set('domain_context.demo.read_only', true);

        $request = $this->requestForRoute('GET', '/cases/lock/12', 'lock-case');

        $response = (new EnforceDemoReadOnly())->handle($request, function () {
            return response('allowed');
        });

        $this->assertSame(423, $response->getStatusCode());
        $this->assertSame(
            'Demo mode is read-only. Changes are not saved on demo.solentjo.com.',
            $response->getData(true)['message']
        );
    }

    public function test_demo_write_mode_allows_crud_api_writes_and_legacy_actions(): void
    {
        $requests = [
            $this->requestForRoute('POST', '/cases', 'cases-create'),
            $this->requestForRoute('PATCH', '/cases/12', 'cases-update'),
            $this->requestForRoute('DELETE', '/cases/12', 'cases-delete'),
            $this->requestForRoute('GET', '/cases/lock/12', 'lock-case'),
            $this->requestForRoute('POST', '/api/set-notification-token', 'api.set-notification-token'),
        ];

        foreach ($requests as $request) {
            $response = (new EnforceDemoReadOnly())->handle($request, function () {
                return response('allowed');
            });

            $this->assertSame('allowed', $response->getContent());
        }
    }

    public function test_demo_read_only_still_allows_safe_pages_and_login(): void
    {
        config()->set('domain_context.demo.read_only', true);

        $safeRequest = $this->requestForRoute('GET', '/cases', 'cases-index');
        $safeResponse = (new EnforceDemoReadOnly())->handle($safeRequest, function () {
            return response('allowed');
        });

        $loginRequest = $this->requestForRoute('POST', '/login', 'login');
        $loginResponse = (new EnforceDemoReadOnly())->handle($loginRequest, function () {
            return response('login allowed');
        });

        $this->assertSame('allowed', $safeResponse->getContent());
        $this->assertSame('login allowed', $loginResponse->getContent());
    }

    public function test_demo_read_only_blocks_api_writes_and_is_registered_for_api_routes(): void
    {
        config()->set('domain_context.demo.read_only', true);

        $request = $this->requestForRoute('POST', '/api/set-notification-token', 'api.set-notification-token');
        $response = (new EnforceDemoReadOnly())->handle($request, function () {
            return response('allowed');
        });
        $kernel = file_get_contents(app_path('Http/Kernel.php'));
        $apiGroup = substr($kernel, strpos($kernel, "'api' => ["));

        $this->assertSame(423, $response->getStatusCode());
        $this->assertStringContainsString(EnforceDemoReadOnly::class, $apiGroup);
    }

    public function test_demo_database_must_match_the_isolated_database(): void
    {
        config([
            'domain_context.demo.database' => 'solent_demo',
            'domain_context.default.database' => 'solent_live',
            'database.connections.mysql.database' => 'solent_live',
            'database.connections.landlord.database' => 'solent_landlord',
        ]);

        $isolatedContext = TenantContext::fromArray([
            'database' => 'solent_demo',
            'domain' => 'demo.solentjo.com',
            'status' => 'active',
        ]);
        $mismatchedContext = TenantContext::fromArray([
            'database' => 'another_database',
            'domain' => 'demo.solentjo.com',
            'status' => 'active',
        ]);

        $this->assertTrue(DemoMode::hasIsolatedDatabase($isolatedContext));
        $this->assertFalse(DemoMode::hasIsolatedDatabase($mismatchedContext));

        config()->set('domain_context.demo.database', 'solent_live');
        $liveContext = TenantContext::fromArray([
            'database' => 'solent_live',
            'domain' => 'demo.solentjo.com',
            'status' => 'active',
        ]);
        $this->assertFalse(DemoMode::hasIsolatedDatabase($liveContext));

        config()->set('domain_context.demo.database', null);
        $this->assertFalse(DemoMode::hasIsolatedDatabase($isolatedContext));
    }

    public function test_demo_database_guard_is_applied_before_connection(): void
    {
        $middleware = file_get_contents(app_path('Http/Middleware/ApplyDomainContext.php'));

        $this->assertStringContainsString('DemoMode::hasIsolatedDatabase($tenantContext)', $middleware);
    }

    public function test_new_case_and_invoice_form_get_requests_remain_viewable_without_side_effects(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Cases/Http/Controllers/CaseController.php'));
        $middleware = file_get_contents(app_path('Http/Middleware/EnforceDemoReadOnly.php'));

        $this->assertStringNotContainsString('CREATE TABLE monthly_case_counters', $controller);
        $this->assertStringNotContainsString("'create-invoice',", $middleware);
        $this->assertStringNotContainsString("'createInvoice',", $middleware);
    }

    public function test_disabled_accounts_are_rejected_at_login_and_during_existing_sessions(): void
    {
        $login = file_get_contents(app_path('Http/Controllers/Auth/LoginController.php'));
        $authenticate = file_get_contents(app_path('Http/Middleware/Authenticate.php'));

        $this->assertStringContainsString("Schema::hasColumn(\$table, 'status')", $login);
        $this->assertStringContainsString("Schema::hasColumn(\$table, 'active')", $login);
        $this->assertStringContainsString("'This account is disabled.'", $login);
        $this->assertStringContainsString("array_key_exists('status', \$attributes)", $authenticate);
        $this->assertStringContainsString("array_key_exists('active', \$attributes)", $authenticate);
        $this->assertStringContainsString('$this->auth->logout();', $authenticate);
        $this->assertStringContainsString('$request->session()->invalidate();', $authenticate);
    }

    private function requestForRoute(string $method, string $path, string $name): Request
    {
        $request = Request::create('https://demo.solentjo.com' . $path, $method, [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $route = (new Route([$method], ltrim($path, '/'), function (): void {
        }))->name($name);
        $request->setRouteResolver(function () use ($route): Route {
            return $route;
        });

        return $request;
    }
}
