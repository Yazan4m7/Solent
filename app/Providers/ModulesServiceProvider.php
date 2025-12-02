<?php

namespace App\Providers;

use App\Modules\Contracts\Modules\ModuleRegistryInterface;
use App\Modules\Support\FilesystemModuleRegistry;
use App\Modules\Support\ModuleMetadata;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistryInterface::class, function (): ModuleRegistryInterface {
            return new FilesystemModuleRegistry(app_path('Modules'));
        });
    }

    public function boot(): void
    {
        foreach ($this->app->make(ModuleRegistryInterface::class)->all() as $module) {
            $this->registerConfig($module);
            $this->registerRoutes($module);
            $this->registerViews($module);
            $this->aliasControllers($module);
            $this->registerMigrations($module);
            $this->registerModuleProvider($module);
        }
    }

    protected function registerRoutes(ModuleMetadata $module): void
    {
        if (is_file($module->routesPath)) {
            Route::middleware('web')
                ->namespace($module->namespace . '\\Http\\Controllers')
                ->group($module->routesPath);
        }
    }

    protected function registerViews(ModuleMetadata $module): void
    {
        if (is_dir($module->viewsPath)) {
            $namespace = Str::of($module->name)->lower()->replace('\\', '');
            $this->loadViewsFrom($module->viewsPath, (string) $namespace);
            View::addLocation($module->viewsPath);
        }
    }

    protected function aliasControllers(ModuleMetadata $module): void
    {
        if (! is_dir($module->controllersPath)) {
            return;
        }

        foreach (glob($module->controllersPath . '/*.php') as $controllerFile) {
            $classBase = pathinfo($controllerFile, PATHINFO_FILENAME);
            if (strtolower($classBase) === 'controller') {
                continue;
            }

            $fqcn = $module->namespace . '\\Http\\Controllers\\' . $classBase;
            $legacyAlias = 'App\\Http\\Controllers\\' . $classBase;

            if (! class_exists($legacyAlias) && class_exists($fqcn)) {
                class_alias($fqcn, $legacyAlias);
            }
        }
    }

    protected function registerConfig(ModuleMetadata $module): void
    {
        if (! $module->configPath) {
            return;
        }

        $key = 'modules.' . Str::of($module->name)->kebab();
        $this->mergeConfigFrom($module->configPath, (string) $key);
    }

    protected function registerMigrations(ModuleMetadata $module): void
    {
        if ($module->migrationsPath) {
            $this->loadMigrationsFrom($module->migrationsPath);
        }
    }

    protected function registerModuleProvider(ModuleMetadata $module): void
    {
        if ($module->providerClass) {
            $this->app->register($module->providerClass);
        }
    }
}
