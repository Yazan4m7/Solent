<?php

namespace Tests\Feature;

use App\Listeners\RecordUserLoginAudit;
use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class UserLoginAuditTest extends TestCase
{
    public function test_enabled_web_user_login_is_recorded_with_identity_and_timestamp(): void
    {
        $inserted = null;
        $query = Mockery::mock();
        $query->shouldReceive('insert')
            ->once()
            ->with(Mockery::on(function (array $values) use (&$inserted): bool {
                $inserted = $values;

                return true;
            }))
            ->andReturnTrue();

        DB::shouldReceive('table')
            ->once()
            ->with('user_login_audits')
            ->andReturn($query);

        $request = Request::create('https://solent.test/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '203.0.113.25',
            'HTTP_USER_AGENT' => 'Solent Login Audit Test',
        ]);
        $user = (new User())->forceFill([
            'id' => 42,
            'username' => 'enabled.operator',
            'status' => 1,
        ]);

        (new RecordUserLoginAudit($request))->handle(new Login('web', $user, false));

        $this->assertSame(42, $inserted['user_id']);
        $this->assertSame('enabled.operator', $inserted['username']);
        $this->assertSame('203.0.113.25', $inserted['ip_address']);
        $this->assertSame('Solent Login Audit Test', $inserted['user_agent']);
        $this->assertNotNull($inserted['logged_in_at']);
    }

    public function test_disabled_users_and_non_web_guards_are_not_recorded(): void
    {
        DB::shouldReceive('table')->never();

        $request = Request::create('https://solent.test/login', 'POST');
        $disabledUser = (new User())->forceFill([
            'id' => 43,
            'username' => 'disabled.operator',
            'status' => 0,
        ]);
        $enabledUser = (new User())->forceFill([
            'id' => 44,
            'username' => 'portal.user',
            'status' => 1,
        ]);
        $listener = new RecordUserLoginAudit($request);

        $listener->handle(new Login('web', $disabledUser, false));
        $listener->handle(new Login('clients', $enabledUser, false));

        $this->assertTrue(true);
    }

    public function test_audit_storage_failure_does_not_block_login(): void
    {
        $query = Mockery::mock();
        $query->shouldReceive('insert')
            ->once()
            ->andThrow(new RuntimeException('audit table unavailable'));

        DB::shouldReceive('table')
            ->once()
            ->with('user_login_audits')
            ->andReturn($query);
        Log::shouldReceive('warning')
            ->once()
            ->with('Unable to record user login audit.', Mockery::on(function (array $context): bool {
                return $context['user_id'] === 45
                    && $context['username'] === 'enabled.operator'
                    && $context['error'] === 'audit table unavailable';
            }));

        $request = Request::create('https://solent.test/login', 'POST');
        $user = (new User())->forceFill([
            'id' => 45,
            'username' => 'enabled.operator',
            'status' => 1,
        ]);

        (new RecordUserLoginAudit($request))->handle(new Login('web', $user, false));

        $this->assertTrue(true);
    }

    public function test_live_schema_is_provided_as_sql_instead_of_a_migration(): void
    {
        $sql = file_get_contents(database_path('sql/create_user_login_audits.sql'));
        $migrations = glob(database_path('migrations/*login*audit*.php')) ?: [];

        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `user_login_audits`', $sql);
        $this->assertStringContainsString('`username` VARCHAR(191) NOT NULL', $sql);
        $this->assertStringContainsString('`ip_address` VARCHAR(45) NULL', $sql);
        $this->assertStringContainsString('`logged_in_at` TIMESTAMP NOT NULL', $sql);
        $this->assertSame([], $migrations);
    }
}
