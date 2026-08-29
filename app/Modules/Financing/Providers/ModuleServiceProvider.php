<?php

namespace App\Modules\Financing\Providers;

use App\Modules\Financing\Console\Commands\GenerateRecurringExpenses;
use App\Modules\Financing\Http\Middleware\FinancingAccess;
use App\Modules\Financing\Http\Middleware\ModuleEnabled;
use App\Modules\Financing\Services\FinancingCollectionService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Solent discovers module providers while ModulesServiceProvider itself is
     * booting. Registration work therefore lives here rather than relying on
     * this provider's boot() method being revisited later in the same cycle.
     */
    public function register(): void
    {
        require_once dirname(__DIR__) . '/Helpers/Setting.php';

        $router = $this->app['router'];
        $router->aliasMiddleware('module', ModuleEnabled::class);
        $router->aliasMiddleware('financing.access', FinancingAccess::class);

        $this->loadTranslationsFrom(dirname(__DIR__) . '/Resources/lang', 'financing');

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateRecurringExpenses::class]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
                $schedule->command('financing:generate-recurring')->monthlyOn(1, '00:10');
            });
        }

        $jobModel = config('modules.financing.models.job');

        if ($jobModel && class_exists($jobModel)) {
            Event::listen('eloquent.updated: ' . $jobModel, function ($job): void {
                app(FinancingCollectionService::class)->captureFromCompletedJob($job);
            });
        }
    }
}
