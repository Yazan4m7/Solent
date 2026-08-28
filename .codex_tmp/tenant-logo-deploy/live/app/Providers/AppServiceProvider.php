<?php

namespace App\Providers;

use App\Modules\Cases\Http\Controllers\OperationsUpgrade;
use App\job;
use App\Observers\JobObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Support\Branding\BrandingManager;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDataCache;
use App\Support\Tenancy\TenantStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TenantDataCache::class, function ($app): TenantDataCache {
            return new TenantDataCache(
                $app->make('cache')->store(),
                $app->bound('app.tenant_context') ? $app->make('app.tenant_context') : null
            );
        });

        $this->app->bind(TenantStorage::class, function ($app): TenantStorage {
            return new TenantStorage(
                $app->bound('app.tenant_context') ? $app->make('app.tenant_context') : null
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // REMOVED: Device feature
        // class_exists(\App\Device::class);
        Paginator::useBootstrap();
       View::share('dashboardName', 'Operations Dashboard');
       View::share('viewCase', 'Case Profile');
       View::share('editCase', 'Edit Case');
        View::share('clientTitle', 'Doctor');
        View::share('voucher', 'Voucher');
        View::share('user', 'User');
        View::share('device', 'Machine');
        View::share('failureCause', 'Fail Cause');
        View::share('reject', 'Reject');
        View::share('modify', 'Modify');
        View::share('repeat', 'Repeat');
        Job::observe(JobObserver::class);
        View::composer('*', function ($view) {
            $view->with('stageConfig', OperationsUpgrade::STAGE_CONFIG);
            $view->with($this->brandingContext());
        });
    }

    private function brandingContext(): array
    {
        static $data;

        if ($data !== null) {
            return $data;
        }

        $settings = app(BrandingManager::class)->current();

        $data = [
            'brandingSettings' => $settings,
            'brandingLogoPath' => $settings->logoPath ?? config('branding.defaults.logo_path'),
            'brandingMarkPath' => $settings->extra['mark_path'] ?? config('branding.defaults.mark_path'),
            'brandingSidebarMarkPath' => $settings->extra['sidebar_mark_path'] ?? config('branding.defaults.sidebar_mark_path'),
            'brandingFaviconPath' => $settings->faviconPath ?? config('branding.defaults.favicon_path'),
            'brandingName' => $settings->name ?? config('branding.defaults.name'),
        ];

        return $data;
    }
}
