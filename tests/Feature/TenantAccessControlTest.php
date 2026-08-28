<?php

namespace Tests\Feature;

use App\Http\Middleware\ApplyDomainContext;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class TenantAccessControlTest extends TestCase
{
    public function test_platform_admin_registers_disable_and_enable_actions(): void
    {
        $this->assertSame(['POST'], Route::getRoutes()->getByName('system.tenants.disable')->methods());
        $this->assertSame(['POST'], Route::getRoutes()->getByName('system.tenants.enable')->methods());
    }

    public function test_disabled_tenant_receives_standalone_korvion_notice(): void
    {
        $context = TenantContext::fromArray([
            'tenant_id' => 10,
            'uuid' => 'disabled-tenant',
            'slug' => 'disabled-tenant',
            'name' => 'Disabled Tenant',
            'database' => 'disabled_tenant_database',
            'status' => 'disabled',
            'domain' => 'disabled.solentjo.com',
        ]);

        $resolver = Mockery::mock(TenantResolver::class);
        $resolver->shouldReceive('resolve')->once()->andReturn($context);
        $this->app->instance(TenantResolver::class, $resolver);

        $request = Request::create('https://disabled.solentjo.com/login', 'GET');
        $response = (new ApplyDomainContext())->handle($request, fn () => response('tenant app'));

        $this->assertSame(423, $response->getStatusCode());
        $this->assertStringContainsString('This workspace is disabled', $response->getContent());
        $this->assertStringContainsString('contact the Korvion team', $response->getContent());
        $this->assertStringNotContainsString('tenant app', $response->getContent());
    }

    public function test_tenant_index_hides_disabled_tenants_and_links_domains(): void
    {
        $controller = file_get_contents(app_path('Modules/System/Http/Controllers/TenantController.php'));
        $view = file_get_contents(resource_path('views/system/tenants/index.blade.php'));

        $this->assertStringContainsString("request()->query('status') === 'disabled'", $controller);
        $this->assertStringContainsString("where('status', '!=', 'disabled')", $controller);
        $this->assertStringContainsString("where('status', 'disabled')", $controller);
        $this->assertStringContainsString('View disabled', $view);
        $this->assertStringContainsString('https://{{ $tenant->primaryDomain->host }}', $view);
        $this->assertStringContainsString('target="_blank"', $view);
    }
}
