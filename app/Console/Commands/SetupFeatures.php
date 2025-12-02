<?php
// app/Console/Commands/SetupFeatures.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use JustSteveKing\Laravel\FeatureFlags\Models\Feature;
use JustSteveKing\Laravel\FeatureFlags\Models\FeatureGroup;

class SetupFeatures extends Command
{
    protected $signature = 'setup:features';
    protected $description = 'Set up initial feature flags';

    public function handle()
    {
        $group = FeatureGroup::create([
            'name' => 'everyone'
        ]);
        $feature = Feature::create([
            'name' => 'create cases'
        ]);
        $group->addFeature($feature);
        $feature = Feature::create([
            'name' => 'finish cases'
        ]);
        $group->addFeature($feature);
        $feature = Feature::create([
            'name' => 'receive payment'
        ]);
        $group->addFeature($feature);

        $this->info('Features have been set up.');
    }
}
