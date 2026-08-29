<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;

class OptionalModulesIntegrationContractTest extends TestCase
{
    private function requireInstalledModules(): void
    {
        if (! is_dir(base_path('app/Modules/Stock')) || ! is_dir(base_path('app/Modules/Financing'))) {
            $this->markTestSkipped('Stock/Financing modules are not installed on this branch yet.');
        }
    }

    public function test_stock_and_financing_modules_have_route_entry_points(): void
    {
        $this->requireInstalledModules();

        $this->assertFileExists(base_path('app/Modules/Stock/Routes/web.php'));
        $this->assertFileExists(base_path('app/Modules/Financing/Routes/web.php'));
    }

    public function test_stock_and_financing_sidebar_named_routes_are_registered_and_authenticated(): void
    {
        $this->requireInstalledModules();

        $routes = app('router')->getRoutes();

        foreach (['stock.index', 'financing.dashboard'] as $name) {
            $route = $routes->getByName($name);
            $this->assertNotNull($route, "Module route [{$name}] is not registered.");
            $this->assertContains('auth', $route->gatherMiddleware(), "Module route [{$name}] is missing auth middleware.");
        }
    }

    public function test_module_php_sources_have_no_debug_termination_calls(): void
    {
        $this->requireInstalledModules();

        $violations = [];
        foreach (['Stock', 'Financing'] as $module) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(base_path("app/Modules/{$module}"), \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
                    continue;
                }

                $source = file_get_contents($file->getPathname());
                if (preg_match('/\b(dd|dump|var_dump)\s*\(/', $source)) {
                    $violations[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $violations, 'Debug termination/output calls found in module PHP sources.');
    }
}
