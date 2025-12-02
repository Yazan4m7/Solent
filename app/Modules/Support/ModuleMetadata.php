<?php

namespace App\Modules\Support;

class ModuleMetadata
{
    public string $name;
    public string $basePath;
    public string $namespace;
    public string $routesPath;
    public string $viewsPath;
    public string $controllersPath;
    public ?string $configPath;
    public ?string $providerClass;
    public ?string $migrationsPath;

    public function __construct(
        string $name,
        string $basePath,
        string $namespace,
        string $routesPath,
        string $viewsPath,
        string $controllersPath,
        ?string $configPath = null,
        ?string $providerClass = null,
        ?string $migrationsPath = null
    ) {
        $this->name = $name;
        $this->basePath = $basePath;
        $this->namespace = $namespace;
        $this->routesPath = $routesPath;
        $this->viewsPath = $viewsPath;
        $this->controllersPath = $controllersPath;
        $this->configPath = $configPath;
        $this->providerClass = $providerClass;
        $this->migrationsPath = $migrationsPath;
    }
}
