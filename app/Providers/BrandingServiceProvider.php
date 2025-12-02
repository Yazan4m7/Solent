<?php

namespace App\Providers;

use App\Modules\Contracts\Branding\BrandingRepositoryInterface;
use App\Modules\Contracts\Branding\BrandingResolverInterface;
use App\Support\Branding\BrandingManager;
use App\Support\Branding\BrandingTheme;
use App\Support\Branding\Repositories\DatabaseBrandingRepository;
use App\Support\Branding\RequestBrandingResolver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BrandingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BrandingRepositoryInterface::class, DatabaseBrandingRepository::class);
        $this->app->singleton(BrandingResolverInterface::class, RequestBrandingResolver::class);

        $this->app->singleton(BrandingManager::class, function ($app): BrandingManager {
            return new BrandingManager(
                $app->make(BrandingRepositoryInterface::class),
                $app->make(BrandingResolverInterface::class),
                $app->make('cache')->store()
            );
        });

        $this->app->singleton(BrandingTheme::class);
    }

    public function boot(): void
    {
        Blade::directive('brandStyles', function (): string {
            return "<?php echo app('App\\\\Support\\\\Branding\\\\BrandingTheme')->renderStyleTag(app('App\\\\Support\\\\Branding\\\\BrandingManager')->current()); ?>";
        });
    }
}
