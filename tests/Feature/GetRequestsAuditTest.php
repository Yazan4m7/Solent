<?php

namespace Tests\Feature;

use App\Http\Middleware\UseTenantDemoDatabase;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDemoDatabase;
use App\Support\Tenancy\TenantResolver;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GetRequestsAuditTest extends TestCase
{
    private string $report = 'storage/framework/testing/get-requests-audit-test.json';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'audit_test',
            'database.connections.audit_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('username');
            $table->boolean('is_admin');
            $table->string('remember_token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('users')->insert(['username' => 'audit-admin', 'is_admin' => true]);

        $context = TenantContext::local('localhost', ':memory:');
        app()->instance('app.tenant_context', $context);
        $resolver = Mockery::mock(TenantResolver::class);
        $resolver->shouldReceive('resolve')->andReturn($context);
        app()->instance(TenantResolver::class, $resolver);
        app()->instance(TenantDemoDatabase::class, new class extends TenantDemoDatabase {
            public function activate(TenantContext $real): void
            {
                throw new RuntimeException('The sample database is unavailable.');
            }
        });

        $routes = new RouteCollection();
        $routes->add((new Route('GET', '/audit-probe', fn () => null))->middleware('web')->name('audit-probe'));
        $routes->add((new Route('GET', '/sys/config/update', fn () => null))->middleware('web')->name('update-sys-config'));
        $routes->add((new Route('POST', '/demo-data', fn () => null))->name('tenant-demo.update'));
        app('router')->setRoutes($routes);

        $kernel = Mockery::mock(Kernel::class);
        $kernel->shouldReceive('handle')->andReturnUsing(function ($request) {
            return (new UseTenantDemoDatabase())->handle($request, fn () => response('Page rendered'));
        });
        $kernel->shouldReceive('terminate');
        app()->instance(Kernel::class, $kernel);
    }

    protected function tearDown(): void
    {
        if (is_file(base_path($this->report))) {
            unlink(base_path($this->report));
        }
        DB::purge('audit_test');
        parent::tearDown();
    }

    public function test_default_audit_uses_real_data_without_activating_unavailable_sample_database(): void
    {
        $sessionDriver = config('session.driver');
        $this->artisan('routes:audit-get-requests', ['--json' => $this->report])->assertExitCode(0);

        $report = json_decode(file_get_contents(base_path($this->report)), true);
        $this->assertSame('real', $report['data_mode']);
        $this->assertSame(200, $report['results'][0]['status']);
        $this->assertSame('skipped', $report['results'][1]['state']);
        $this->assertSame($sessionDriver, config('session.driver'));
    }

    public function test_explicit_demo_audit_reports_the_unavailable_database_with_a_useful_error(): void
    {
        $this->artisan('routes:audit-get-requests', ['--data' => 'demo', '--json' => $this->report])->assertExitCode(1);

        $report = json_decode(file_get_contents(base_path($this->report)), true);
        $this->assertSame('demo', $report['data_mode']);
        $this->assertSame(503, $report['results'][0]['status']);
        $this->assertStringContainsString('Sample data is unavailable', $report['results'][0]['error']);
        $this->assertStringNotContainsString('<style', $report['results'][0]['error']);
    }

    public function test_case_stage_parameters_use_a_case_record_and_other_host_routes_are_skipped(): void
    {
        Schema::create('cases', function ($table) {
            $table->increments('id');
            $table->softDeletes();
        });
        DB::table('cases')->insert(['id' => 71]);
        Schema::create('jobs', function ($table) {
            $table->increments('id');
            $table->integer('case_id');
            $table->integer('stage');
            $table->softDeletes();
        });
        DB::table('jobs')->insert(['case_id' => 71, 'stage' => 2]);
        $routes = app('router')->getRoutes();
        $routes->add((new Route('GET', '/view-case/{id}/{stage?}', fn () => null))->middleware('web')->name('view-case'));
        $routes->add((new Route('GET', '/employee-production-board/cases/{caseId}/stages/{stage}/details', fn () => null))->middleware('web')->name('employee-production-board-v2.details'));
        $routes->add((new Route('GET', '/design/{id}', fn () => null))->middleware('web')->name('designer-cases-list'));
        $routes->add((new Route('GET', '/system/tenants', fn () => null))->domain('admin.example.test')->middleware('web')->name('system.tenants.index'));

        $kernel = Mockery::mock(Kernel::class);
        $kernel->shouldReceive('handle')->times(4)->andReturnUsing(function ($request) {
            $this->assertContains($request->getPathInfo(), ['/audit-probe', '/view-case/71/1', '/employee-production-board/cases/71/stages/2/details', '/design/1']);
            return (new UseTenantDemoDatabase())->handle($request, fn () => response('Page rendered'));
        });
        $kernel->shouldReceive('terminate')->times(4);
        app()->instance(Kernel::class, $kernel);

        $this->artisan('routes:audit-get-requests', ['--json' => $this->report])->assertExitCode(0);
        $report = json_decode(file_get_contents(base_path($this->report)), true);
        $this->assertSame('/view-case/71/1', $report['results'][2]['uri']);
        $this->assertSame('/employee-production-board/cases/71/stages/2/details', $report['results'][3]['uri']);
        $this->assertSame('/design/1', $report['results'][4]['uri']);
        $this->assertSame('skipped', $report['results'][5]['state']);
    }
}
