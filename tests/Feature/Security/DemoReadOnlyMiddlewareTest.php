<?php

namespace Tests\Feature\Security;

use App\Http\Middleware\EnforceDemoReadOnly;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class DemoReadOnlyMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('domain_context.demo.enabled', true);
        config()->set('domain_context.demo.hosts', ['demo.test']);
        config()->set('domain_context.demo.read_only', true);
    }

    public function test_mutating_request_on_demo_host_is_blocked_with_locked_status_for_json_clients(): void
    {
        $request = Request::create('https://demo.test/cases/1', 'POST', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $response = (new EnforceDemoReadOnly())->handle($request, fn () => response('should-not-run', 200));

        $this->assertSame(423, $response->getStatusCode());
        $this->assertStringContainsString('read-only', $response->getContent());
    }

    public function test_safe_get_request_on_demo_host_is_allowed(): void
    {
        $request = Request::create('https://demo.test/home', 'GET');

        $response = (new EnforceDemoReadOnly())->handle($request, fn () => response('allowed', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('allowed', $response->getContent());
    }

    public function test_login_post_is_allowed_in_read_only_demo_mode(): void
    {
        $request = Request::create('https://demo.test/login', 'POST');

        $response = (new EnforceDemoReadOnly())->handle($request, fn () => response('allowed', 200));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_mutating_get_route_is_blocked_even_though_get_is_normally_safe(): void
    {
        $request = Request::create('https://demo.test/delete-payment/10', 'GET', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $route = new Route(['GET'], 'delete-payment/10', ['uses' => static function (): void {}]);
        $route->name('delete-payment');
        $request->setRouteResolver(fn () => $route);

        $response = (new EnforceDemoReadOnly())->handle($request, fn () => response('should-not-run', 200));

        $this->assertSame(423, $response->getStatusCode());
    }

    public function test_non_demo_host_is_never_blocked_by_demo_read_only_middleware(): void
    {
        $request = Request::create('https://production.test/cases/1', 'POST');

        $response = (new EnforceDemoReadOnly())->handle($request, fn () => response('allowed', 200));

        $this->assertSame(200, $response->getStatusCode());
    }
}
