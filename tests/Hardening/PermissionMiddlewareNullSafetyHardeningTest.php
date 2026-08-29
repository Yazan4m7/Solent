<?php

namespace Tests\Hardening;

use App\Http\Middleware\ViewPaymentsMiddleware;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Throwable;

class PermissionMiddlewareNullSafetyHardeningTest extends TestCase
{
    public function test_view_payments_middleware_denies_cleanly_when_permission_cache_is_missing(): void
    {
        Cache::flush();

        $user = new User([
            'first_name' => 'No',
            'last_name' => 'Permissions',
            'username' => 'no-permissions',
            'password' => 'unused',
            'is_admin' => 0,
        ]);
        $user->id = 999999;
        $user->exists = true;
        $this->actingAs($user);

        try {
            $response = (new ViewPaymentsMiddleware())->handle(
                Request::create('/payments/index', 'GET'),
                fn () => response('should-not-run', 200)
            );
        } catch (Throwable $exception) {
            $this->fail('Permission middleware crashed when cache was empty: ' . $exception->getMessage());
        }

        $this->assertSame(403, $response->getStatusCode());
    }
}
