<?php

namespace Tests\Feature\Integration;

use Illuminate\Routing\Route as LaravelRoute;
use Tests\TestCase;

class CoreRouteContractTest extends TestCase
{
    public function test_critical_routes_keep_expected_uri_and_security_middleware_contracts(): void
    {
        $routes = app('router')->getRoutes();
        $contracts = [
            'homeScreen' => ['uri' => 'home', 'middleware' => ['web', 'auth']],
            'admin-dashboard-v2' => ['uri' => 'operations-dashboard', 'middleware' => ['web', 'auth']],
            'invoices-index' => ['uri' => 'invoices', 'middleware' => ['web', 'auth']],
            'payments-index' => ['uri' => 'payments/index', 'middleware' => ['web', 'ViewPayments']],
            'new-case-post' => ['uri' => 'new-case-post', 'middleware' => ['web', 'auth', 'CreateCase']],
        ];

        foreach ($contracts as $name => $contract) {
            $route = $routes->getByName($name);
            $this->assertNotNull($route, "Missing named route [{$name}].");
            $this->assertSame($contract['uri'], $route->uri(), "Route [{$name}] changed URI unexpectedly.");

            $middleware = $route->gatherMiddleware();
            foreach ($contract['middleware'] as $expectedMiddleware) {
                $this->assertContains(
                    $expectedMiddleware,
                    $middleware,
                    "Route [{$name}] lost middleware [{$expectedMiddleware}]."
                );
            }
        }
    }

    public function test_every_controller_route_points_to_an_existing_controller_method(): void
    {
        /** @var LaravelRoute $route */
        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure') {
                continue;
            }

            if (strpos($action, '@') === false) {
                $this->assertTrue(class_exists($action), "Route [{$route->uri()}] points to missing controller [{$action}].");
                continue;
            }

            [$class, $method] = explode('@', $action, 2);
            $this->assertTrue(class_exists($class), "Route [{$route->uri()}] points to missing controller [{$class}].");
            $this->assertTrue(method_exists($class, $method), "Route [{$route->uri()}] points to missing method [{$class}::{$method}].");
        }
    }

    public function test_every_state_changing_web_route_stays_in_web_middleware_for_csrf_protection(): void
    {
        /** @var LaravelRoute $route */
        foreach (app('router')->getRoutes() as $route) {
            $methods = array_diff($route->methods(), ['HEAD', 'GET', 'OPTIONS']);
            if ($methods === []) {
                continue;
            }

            $middleware = $route->gatherMiddleware();

            // Stateless API routes intentionally use the API middleware stack rather than CSRF.
            if (in_array('api', $middleware, true)) {
                continue;
            }

            $this->assertContains(
                'web',
                $middleware,
                sprintf('Mutating route [%s %s] is outside both the web and API middleware stacks.', implode('|', $methods), $route->uri())
            );
        }
    }
}
