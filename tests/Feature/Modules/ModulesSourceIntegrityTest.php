<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;

class ModulesSourceIntegrityTest extends TestCase
{
    public function test_modules_are_discoverable_by_solent_module_registry(): void
    {
        $registry = app(\App\Modules\Contracts\Modules\ModuleRegistryInterface::class);
        $names = collect($registry->all())->pluck('name')->all();

        $this->assertContains('Stock', $names);
        $this->assertContains('Financing', $names);
    }

    public function test_module_views_use_solent_css_stack_and_separate_stylesheets(): void
    {
        $financeLayout = file_get_contents(base_path('app/Modules/Financing/Resources/views/financing/_layout.blade.php'));
        $stockViews = glob(base_path('app/Modules/Stock/Resources/views/stock/*.blade.php'));

        $this->assertStringContainsString("@push('css')", $financeLayout);
        $this->assertStringContainsString("asset('css/financing.css')", $financeLayout);

        foreach ($stockViews as $view) {
            $source = file_get_contents($view);
            if (strpos($source, "@extends('layouts.app')") !== false) {
                $this->assertStringContainsString("@push('css')", $source, basename($view) . ' must load stock.css through Solent css stack.');
                $this->assertStringContainsString("asset('css/stock.css')", $source);
            }
        }
    }


    public function test_solent_sidebar_exposes_stock_and_conditionally_exposes_financing(): void
    {
        $sidebar = file_get_contents(base_path('resources/views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString("route('stock.index')", $sidebar);
        $this->assertStringContainsString("route('financing.dashboard')", $sidebar);
        $this->assertStringContainsString("setting('module_financing', '0')", $sidebar);
    }

    public function test_financing_source_has_no_unadapted_generic_namespace_or_config_references(): void
    {
        $violations = [];
        $root = base_path('app/Modules/Financing');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! $file->isFile() || ! in_array(strtolower($file->getExtension()), ['php'], true)) {
                continue;
            }

            $source = file_get_contents($file->getPathname());
            foreach ([
                'namespace App\\\\Http\\\\Controllers\\\\Financing',
                'namespace App\\\\Services\\\\Financing',
                'use App\\\\Models\\\\Finance',
                "config('financing.",
                "role:admin,accountant",
            ] as $needle) {
                if (strpos($source, $needle) !== false) {
                    $violations[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname()) . ' => ' . $needle;
                }
            }
        }

        $this->assertSame([], $violations, 'Unadapted financing module references remain.');
    }
}
