<?php

namespace App\Modules\Support;

use App\Modules\Contracts\Modules\ModuleRegistryInterface;

class FilesystemModuleRegistry implements ModuleRegistryInterface
{
    private string $modulesRoot;

    public function __construct(string $modulesRoot)
    {
        $this->modulesRoot = $modulesRoot;
    }

    public function all(): array
    {
        if (! is_dir($this->modulesRoot)) {
            return [];
        }

        $modules = [];
        foreach (scandir($this->modulesRoot) as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $basePath = $this->modulesRoot . DIRECTORY_SEPARATOR . $dir;
            if (! is_dir($basePath)) {
                continue;
            }

            $modules[] = new ModuleMetadata(
                $dir,
                $basePath,
                "App\\Modules\\{$dir}",
                $basePath . '/Routes/web.php',
                $basePath . '/Resources/views',
                $basePath . '/Http/Controllers',
                $this->detectConfigPath($basePath),
                $this->detectProviderClass($dir),
                $this->detectMigrationsPath($basePath)
            );
        }

        return $modules;
    }

    private function detectConfigPath(string $basePath): ?string
    {
        $paths = [
            $basePath . '/Config/config.php',
            $basePath . '/config.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function detectProviderClass(string $dir): ?string
    {
        $fqcn = "App\\Modules\\{$dir}\\Providers\\ModuleServiceProvider";

        return class_exists($fqcn) ? $fqcn : null;
    }

    private function detectMigrationsPath(string $basePath): ?string
    {
        $path = $basePath . '/Database/migrations';

        return is_dir($path) ? $path : null;
    }
}
