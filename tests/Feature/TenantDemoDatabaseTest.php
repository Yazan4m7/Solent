<?php

namespace Tests\Feature;

use App\Http\Controllers\TenantDemoController;
use App\Http\Middleware\EnforceDemoReadOnly;
use App\Http\Middleware\UseTenantDemoDatabase;
use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDataCache;
use App\Support\Tenancy\TenantDemoDatabase;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantDemoDatabaseTest extends TestCase
{
    private TenantContext $context;
    private TenantDemoDatabase $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'demo_test_real',
            'database.connections.demo_test_real' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'database.connections.tenant_demo' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);
        foreach (['demo_test_real', 'tenant_demo'] as $name) {
            Schema::connection($name)->create('cases', function ($table) {
                $table->increments('id'); $table->string('patient_name');
            });
            DB::connection($name)->table('cases')->insert(['patient_name' => $name === 'tenant_demo' ? 'Sample patient' : 'Real patient']);
        }
        Schema::connection('tenant_demo')->create(TenantDemoDatabase::MARKER, function ($table) {
            $table->string('tenant_uuid'); $table->string('real_database');
        });
        $this->context = TenantContext::fromArray([
            'uuid' => 'tenant-alpha', 'domain' => 'alpha.example.test', 'database' => 'alpha_live', 'branding_key' => 'alpha',
        ]);
        app()->instance('app.tenant_context', $this->context);
        DB::connection('tenant_demo')->table(TenantDemoDatabase::MARKER)->insert([
            'tenant_uuid' => $this->context->uuid, 'real_database' => $this->context->database,
        ]);
        $this->service = new class extends TenantDemoDatabase {
            public function connection(TenantContext $context) { return DB::connection(self::CONNECTION); }
        };
        app()->instance(TenantDemoDatabase::class, $this->service);
        $this->admin = new User(['id' => 1, 'first_name' => 'Real', 'is_admin' => 1]);
        $this->admin->id = 1;
        $this->actingAs($this->admin);
    }

    protected function tearDown(): void
    {
        DB::purge('tenant_demo');
        DB::purge('demo_test_real');
        parent::tearDown();
    }

    public function test_each_website_has_its_own_deterministic_database_and_session_key(): void
    {
        $other = clone $this->context; $other->uuid = 'tenant-beta';
        $this->assertNotSame($this->service->databaseName($this->context), $this->service->databaseName($other));
        $this->assertNotSame($this->service->sessionKey($this->context, $this->admin), $this->service->sessionKey($other, $this->admin));
        $other->uuid = $this->context->uuid; $other->database = 'replaced_database';
        $this->assertNotSame($this->service->databaseName($this->context), $this->service->databaseName($other));
    }

    public function test_copied_connection_does_not_inherit_the_real_eloquent_connection_name(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'solent-demo-test-');
        $service = new class($file) extends TenantDemoDatabase {
            private string $file;
            public function __construct(string $file) { $this->file = $file; }
            public function databaseName(TenantContext $context): string { return $this->file; }
        };
        try {
            $connection = $service->connection($this->context);
            $connection->getSchemaBuilder()->create('cases', function ($table) {
                $table->increments('id'); $table->string('patient_name');
            });
            $connection->getSchemaBuilder()->create(TenantDemoDatabase::MARKER, function ($table) {
                $table->string('tenant_uuid'); $table->string('real_database');
            });
            $connection->table('cases')->insert(['patient_name' => 'Sample patient']);
            $connection->table(TenantDemoDatabase::MARKER)->insert([
                'tenant_uuid' => $this->context->uuid, 'real_database' => $this->context->database,
            ]);
            $service->activate($this->context);
            $model = new class extends \Illuminate\Database\Eloquent\Model {
                protected $table = 'cases';
                public function linkedRecord() { return $this->belongsTo(self::class, 'id'); }
            };
            $record = $model->first();
            $this->assertSame('tenant_demo', $record->getConnectionName());
            $this->assertSame('Sample patient', $record->linkedRecord->patient_name);
            $this->assertSame('Real patient', DB::connection('demo_test_real')->table('cases')->value('patient_name'));
        } finally {
            DB::purge('tenant_demo');
            unlink($file);
        }
    }

    public function test_active_mode_switches_page_queries_but_preserves_identity_branding_and_real_records(): void
    {
        $request = $this->request('GET', 'cases-index');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->session()->put($key . '.active', true);
        $realCache = (new TenantDataCache())->key('dashboard');
        $response = (new UseTenantDemoDatabase())->handle($request, function () use ($realCache) {
            $this->assertSame('Sample patient', DB::table('cases')->value('patient_name'));
            $this->assertSame('demo_test_real', auth()->user()->getConnectionName());
            $this->assertSame('Real', auth()->user()->first_name);
            $this->assertSame('alpha', app('app.tenant_context')->brandingKey);
            $this->assertNotSame($realCache, (new TenantDataCache())->key('dashboard'));
            return response('sample page');
        });
        $this->assertSame('sample page', $response->getContent());
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame('Real patient', DB::table('cases')->value('patient_name'));
        $this->assertSame($this->context, app('app.tenant_context'));
    }

    public function test_admin_defaults_to_sample_data_without_changing_real_records(): void
    {
        $request = $this->request('GET', 'cases-index');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $this->assertFalse($request->session()->has($key . '.active'));
        $response = (new UseTenantDemoDatabase())->handle($request, function () {
            $this->assertSame('Sample patient', DB::table('cases')->value('patient_name'));
            $this->assertTrue(app('app.tenant_demo_active'));
            return response('default demo');
        });
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame('Real patient', DB::table('cases')->value('patient_name'));
    }

    public function test_explicit_off_and_employee_sessions_stay_on_real_data(): void
    {
        $request = $this->request('GET', 'cases-index');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->session()->put($key, [
            'active' => false,
            'preference_version' => TenantDemoDatabase::PREFERENCE_VERSION,
        ]);
        $assertReal = function () {
            $this->assertSame('Real patient', DB::table('cases')->value('patient_name'));
            $this->assertFalse(app('app.tenant_demo_active'));
            return response('real');
        };
        (new UseTenantDemoDatabase())->handle($request, $assertReal);
        $request->session()->forget($key);
        $this->admin->is_admin = 0;
        (new UseTenantDemoDatabase())->handle($request, $assertReal);
    }

    public function test_old_off_preference_receives_the_new_default_once(): void
    {
        $request = $this->request('GET', 'cases-index');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->session()->put($key, ['active' => false, 'switched' => true, 'token' => 'old']);
        (new UseTenantDemoDatabase())->handle($request, function () {
            $this->assertTrue(app('app.tenant_demo_active'));
            $this->assertSame('Sample patient', DB::table('cases')->value('patient_name'));
            return response('sample');
        });
    }

    public function test_default_demo_blocks_writes_but_switch_and_logout_remain_available(): void
    {
        $response = (new UseTenantDemoDatabase())->handle($this->request('POST', 'cases-create'), function ($request) {
            return (new EnforceDemoReadOnly())->handle($request, function () {
                $this->fail('Default sample mode must not allow business changes.');
            });
        });
        $this->assertSame(423, $response->getStatusCode());
        foreach (['tenant-demo.update', 'logout'] as $route) {
            $response = (new UseTenantDemoDatabase())->handle($this->request('POST', $route), function () {
                $this->assertSame('demo_test_real', DB::getDefaultConnection());
                return response('control');
            });
            $this->assertSame(200, $response->getStatusCode());
        }
    }

    public function test_switch_on_and_off_is_session_only(): void
    {
        app()->instance('app.tenant_real_context', $this->context);
        $request = $this->request('POST', 'tenant-demo.update');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->merge(['enabled' => '1']);
        (new TenantDemoController())->update($request, $this->service);
        $this->assertTrue($request->session()->get($key . '.active'));
        $firstToken = $request->session()->get($key . '.token');
        $request->merge(['enabled' => '0']);
        (new TenantDemoController())->update($request, $this->service);
        $this->assertFalse($request->session()->get($key . '.active'));
        $this->assertNotSame($firstToken, $request->session()->get($key . '.token'));
        $this->assertSame('Real patient', DB::table('cases')->value('patient_name'));
    }

    public function test_missing_or_wrong_owner_sample_database_never_falls_back_to_real_data(): void
    {
        DB::connection('tenant_demo')->table(TenantDemoDatabase::MARKER)->update(['tenant_uuid' => 'different-tenant']);
        $request = $this->request('GET', 'cases-index');
        $request->session()->put($this->service->sessionKey($this->context, $this->admin) . '.active', true);
        $response = (new UseTenantDemoDatabase())->handle($request, function () {
            $this->fail('The controller must not run against the real database.');
        });
        $this->assertSame(503, $response->getStatusCode());
        $this->assertStringContainsString('Return to real data', $response->getContent());
        $this->assertSame('Real patient', DB::table('cases')->value('patient_name'));
    }

    public function test_demo_connection_itself_rejects_writes(): void
    {
        $this->service->activate($this->context);
        $this->expectException(QueryException::class);
        DB::table('cases')->update(['patient_name' => 'Changed']);
    }

    public function test_legacy_get_and_post_changes_are_blocked_in_sample_mode(): void
    {
        app()->instance('app.tenant_demo_active', true);
        foreach ([['GET', 'complete-by-admin'], ['POST', 'cases-create']] as [$method, $route]) {
            $response = (new EnforceDemoReadOnly())->handle($this->request($method, $route), fn () => response('unexpected'));
            $this->assertSame(423, $response->getStatusCode());
        }
    }

    public function test_old_tab_cannot_mutate_real_data_after_switching_off(): void
    {
        $request = $this->request('GET', 'complete-by-admin');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->attributes->set('tenant_demo_session_key', $key);
        $request->session()->put($key, ['switched' => true, 'token' => 'new-mode']);
        $request->merge(['_data_context' => 'old-mode']);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('The data mode changed');
        (new EnforceDemoReadOnly())->handle($request, fn () => response('unexpected'));
    }

    public function test_fresh_real_page_actions_and_demo_exit_remain_allowed(): void
    {
        $request = $this->request('POST', 'cases-create');
        $key = $this->service->sessionKey($this->context, $this->admin);
        $request->attributes->set('tenant_demo_session_key', $key);
        $request->session()->put($key, ['switched' => true, 'token' => 'new-mode']);
        $request->headers->set('X-Data-Context', 'new-mode');
        $request->merge(['_data_context' => 'new-mode']);
        $this->assertSame(200, (new EnforceDemoReadOnly())->handle($request, fn () => response('allowed'))->getStatusCode());
        $this->assertFalse($request->has('_data_context'));
        app()->instance('app.tenant_demo_active', true);
        $this->assertSame(200, (new EnforceDemoReadOnly())->handle($this->request('POST', 'tenant-demo.update'), fn () => response('allowed'))->getStatusCode());
    }

    public function test_non_admin_cannot_switch_to_sample_database(): void
    {
        $this->admin->is_admin = 0;
        $this->expectException(HttpException::class);
        (new TenantDemoController())->update($this->request('POST', 'tenant-demo.update'), $this->service);
    }

    public function test_middleware_order_resolves_real_auth_and_branding_before_sample_bindings(): void
    {
        app(\Illuminate\Contracts\Http\Kernel::class);
        $route = app('router')->getRoutes()->getByName('tenant-demo.update');
        $stack = app('router')->gatherRouteMiddleware($route);
        $switch = array_search(UseTenantDemoDatabase::class, $stack);
        $this->assertLessThan($switch, array_search(\Illuminate\Session\Middleware\AuthenticateSession::class, $stack));
        $this->assertLessThan($switch, array_search(\App\Http\Middleware\ApplyBranding::class, $stack));
        $this->assertGreaterThan($switch, array_search(\Illuminate\Routing\Middleware\SubstituteBindings::class, $stack));
    }

    private function request(string $method, string $route): Request
    {
        $request = Request::create('https://alpha.example.test/check', $method);
        $request->headers->set('Accept', 'application/json');
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => $this->admin);
        $namedRoute = (new Route([$method], '/check', fn () => null))->name($route);
        $request->setRouteResolver(fn () => $namedRoute);
        return $request;
    }
}
