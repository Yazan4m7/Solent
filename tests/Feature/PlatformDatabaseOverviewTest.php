<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PlatformDatabaseOverviewTest extends TestCase
{
    public function test_database_overview_route_is_isolated_to_the_platform_admin_host(): void
    {
        $route = Route::getRoutes()->getByName('system.databases.index');

        $this->assertNotNull($route);
        $this->assertSame('system/databases', $route->uri());
        $this->assertSame('admin.solentjo.com', $route->getDomain());
        $this->assertContains('platform.admin', $route->gatherMiddleware());
    }

    public function test_database_overview_uses_registered_databases_and_bound_metadata_queries(): void
    {
        $controller = file_get_contents(app_path('Modules/System/Http/Controllers/DatabaseController.php'));

        $this->assertStringContainsString('Tenant::query()', $controller);
        $this->assertStringContainsString('information_schema.SCHEMATA', $controller);
        $this->assertStringContainsString('WHERE schemata.SCHEMA_NAME IN ({$placeholders})', $controller);
        $this->assertStringNotContainsString('SHOW DATABASES', strtoupper($controller));
    }

    public function test_database_overview_has_mobile_cards_and_phpmyadmin_link(): void
    {
        $view = file_get_contents(resource_path('views/system/databases/index.blade.php'));

        $this->assertStringContainsString('Open phpMyAdmin', $view);
        $this->assertStringContainsString('Last table update', $view);
        $this->assertStringContainsString('@media (max-width: 767.98px)', $view);
        $this->assertStringContainsString("content: attr(data-label)", $view);
    }
}
