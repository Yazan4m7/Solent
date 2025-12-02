<?php

namespace Tests\Unit\Support;

use App\Modules\Support\FilesystemModuleRegistry;
use Tests\TestCase;

class ModuleRegistryTest extends TestCase
{
    public function test_it_discovers_modules(): void
    {
        $registry = new FilesystemModuleRegistry(app_path('Modules'));
        $modules = $registry->all();

        $this->assertNotEmpty($modules, 'No modules were discovered under app/Modules');
        $first = $modules[0];

        $this->assertNotEmpty($first->name);
        $this->assertDirectoryExists($first->basePath);
    }
}
