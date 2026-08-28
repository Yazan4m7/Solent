<?php

namespace Tests\Feature;

use App\Console\Commands\ManageDemoAccounts;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use ReflectionMethod;
use Tests\TestCase;

class ManageDemoAccountsCommandTest extends TestCase
{
    public function test_demo_account_command_is_registered_with_database_and_safety_guards(): void
    {
        $this->assertArrayHasKey('demo:accounts', Artisan::all());

        $command = file_get_contents(app_path('Console/Commands/ManageDemoAccounts.php'));

        $this->assertStringContainsString("demo:accounts", $command);
        $this->assertStringContainsString("config('domain_context.demo.database'", $command);
        $this->assertStringContainsString("unset(\$template['url']);", $command);
        $this->assertStringContainsString('must be isolated from every production or landlord database', $command);
        $this->assertStringContainsString('assertRegisteredTenantIsDemo($database)', $command);
        $this->assertStringContainsString("->table('tenant_domains')", $command);
        $this->assertStringContainsString('registered to a non-demo or unverified tenant domain', $command);
        $this->assertStringContainsString('Unable to verify DEMO_DB_DATABASE against the landlord tenant registry', $command);
        $this->assertStringContainsString('$count < 1 || $count > 10', $command);
        $this->assertStringContainsString('Hash::make($password)', $command);
        $this->assertStringContainsString("'is_admin' => 1", $command);
        $this->assertStringContainsString("'status' => 1", $command);
        $this->assertStringContainsString("'active' => 1", $command);
        $this->assertStringContainsString('{--enable=*', $command);
        $this->assertStringNotContainsString('{--password=', $command);
        $this->assertStringContainsString('The primary demo owner account cannot be managed', $command);
        $this->assertStringContainsString("bin2hex(random_bytes(30))", $command);
        $this->assertStringContainsString("'disabled and credentials revoked'", $command);
        $this->assertStringContainsString("Cache::forget('user' . \$userId)", $command);
    }

    public function test_generated_demo_passwords_have_a_shareable_strong_format(): void
    {
        $method = new ReflectionMethod(ManageDemoAccounts::class, 'generatePassword');
        $method->setAccessible(true);
        $command = new ManageDemoAccounts();

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $password = $method->invoke($command);

            $this->assertSame(14, strlen($password));
            $this->assertMatchesRegularExpression('/[A-Z]/', $password);
            $this->assertMatchesRegularExpression('/[a-z]/', $password);
            $this->assertMatchesRegularExpression('/[0-9]/', $password);
            $this->assertMatchesRegularExpression('/[!@#$%]/', $password);
        }
    }

    public function test_it_refuses_to_run_without_an_explicit_demo_database(): void
    {
        config()->set('domain_context.demo.database', null);

        $this->artisan('demo:accounts')
            ->expectsOutput('DEMO_DB_DATABASE must be configured before managing demo accounts.')
            ->assertExitCode(1);
    }

    public function test_protected_database_inventory_includes_main_landlord_and_non_demo_hosts(): void
    {
        config()->set('domain_context.default.database', 'main_database');
        config()->set('domain_context.hosts', [
            'jo.example.test' => ['country_code' => 'JO', 'database' => 'jordan_database'],
            'demo.example.test' => ['country_code' => 'DEMO', 'database' => 'demo_database'],
        ]);
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.database', 'main_database');
        config()->set('tenancy.landlord_connection', 'landlord');
        config()->set('database.connections.landlord.database', 'landlord_database');

        $method = new ReflectionMethod(ManageDemoAccounts::class, 'protectedDatabaseNames');
        $method->setAccessible(true);
        $databases = $method->invoke(new ManageDemoAccounts(), 'mysql');

        $this->assertContains('main_database', $databases);
        $this->assertContains('landlord_database', $databases);
        $this->assertContains('jordan_database', $databases);
        $this->assertNotContains('demo_database', $databases);
    }

    public function test_primary_owner_protection_is_case_insensitive(): void
    {
        config()->set('domain_context.demo.user.username', 'demo');

        $method = new ReflectionMethod(ManageDemoAccounts::class, 'normalizeManagedUsernames');
        $method->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('primary demo owner account');

        $method->invoke(new ManageDemoAccounts(), ['DEMO']);
    }

    public function test_configured_demo_host_inventory_includes_the_solent_domain(): void
    {
        $method = new ReflectionMethod(ManageDemoAccounts::class, 'configuredDemoHosts');
        $method->setAccessible(true);
        $hosts = $method->invoke(new ManageDemoAccounts());

        $this->assertContains('demo.solentjo.com', $hosts);
    }

    public function test_registered_demo_database_is_allowed_when_every_domain_is_a_demo_host(): void
    {
        $this->invokeRegisteredHostGuard(['demo.solentjo.com', 'www.demo.ceralis.com']);

        $this->assertTrue(true);
    }

    public function test_registered_database_is_rejected_when_any_domain_is_not_a_demo_host(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('registered to a non-demo or unverified tenant domain');

        $this->invokeRegisteredHostGuard(['demo.solentjo.com', 'customer.example.com']);
    }

    public function test_registered_database_is_rejected_when_it_has_no_domains(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('registered to a non-demo or unverified tenant domain');

        $this->invokeRegisteredHostGuard([]);
    }

    private function invokeRegisteredHostGuard(array $hosts): void
    {
        $method = new ReflectionMethod(ManageDemoAccounts::class, 'assertRegisteredHostsAreDemo');
        $method->setAccessible(true);
        $method->invoke(new ManageDemoAccounts(), $hosts);
    }
}
