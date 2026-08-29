<?php

namespace Tests\Unit;

use App\Support\DemoMode;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    public function test_demo_hosts_are_normalized_deduplicated_and_empty_values_removed(): void
    {
        config()->set('domain_context.demo.hosts', [
            'WWW.DEMO.TEST',
            'demo.test',
            ' demo-two.test ',
            '',
            null,
        ]);

        $this->assertSame(['demo.test', 'demo-two.test'], DemoMode::demoHosts());
    }

    public function test_demo_request_detection_respects_enabled_flag_and_www_prefix(): void
    {
        config()->set('domain_context.demo.enabled', true);
        config()->set('domain_context.demo.hosts', ['demo.test']);

        $this->assertTrue(DemoMode::isDemoRequest(Request::create('https://www.demo.test/login')));

        config()->set('domain_context.demo.enabled', false);
        $this->assertFalse(DemoMode::isDemoRequest(Request::create('https://demo.test/login')));
    }

    public function test_demo_database_must_match_demo_config_and_not_match_any_protected_database(): void
    {
        config()->set('domain_context.demo.database', 'solent_demo');
        config()->set('domain_context.default.database', 'solent_default');
        config()->set('database.connections.landlord.database', 'solent_landlord');
        config()->set('database.connections.mysql.database', 'solent_primary');
        config()->set('domain_context.hosts', [
            'production.test' => ['country_code' => 'JO', 'database' => 'solent_prod'],
            'demo.test' => ['country_code' => 'DEMO', 'database' => 'solent_demo'],
        ]);

        $isolated = new TenantContext(null, 'demo', 'demo', 'Demo', 'solent_demo', 'active', 'demo.test');
        $wrongDatabase = new TenantContext(null, 'demo', 'demo', 'Demo', 'other_db', 'active', 'demo.test');

        $this->assertTrue(DemoMode::hasIsolatedDatabase($isolated));
        $this->assertFalse(DemoMode::hasIsolatedDatabase($wrongDatabase));

        config()->set('database.connections.mysql.database', 'solent_demo');
        $this->assertFalse(DemoMode::hasIsolatedDatabase($isolated));
    }
}
