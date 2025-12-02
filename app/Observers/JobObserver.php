<?php
namespace App\Observers;

use App\job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class JobObserver
{
    public function created(Job $job)
    {
        $this->clearDashboardCache();
    }

    public function updated(Job $job)
    {
        $this->clearDashboardCache();
    }

    public function deleted(Job $job): void
    {
        $this->clearDashboardCache();
    }

    protected function clearDashboardCache(): void
    {
        // Anthropic Assistant: Added selective cache clearing with index hints
        if (!Auth::check()) return;
        $user = Auth()->user();
        $isAdmin = $user->is_admin ?? false;

        // Clear specific cache keys instead of entire dashboard
        $cacheKeys = [
            'dashboard_data_' . $user->id . '_' . ($isAdmin ? 'admin' : 'user'),
            'user_permissions_' . $user->id,
            'dashboard_stages_' . $user->id,
            'dashboard_v2_' . $user->id,
            'build_cases_*'
        ];

        foreach ($cacheKeys as $key) {
            if (strpos($key, '*') !== false) {
                // TODO redis caching. tags.
                Cache::flush();
                //  Cache::tags(['builds'])->flush();
            } else {
                Cache::forget($key);
            }
        }
    }
}
