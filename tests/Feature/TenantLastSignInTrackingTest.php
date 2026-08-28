<?php

namespace Tests\Feature;

use Tests\TestCase;

class TenantLastSignInTrackingTest extends TestCase
{
    public function test_tenant_last_sign_in_schema_is_registered_on_landlord_tenants(): void
    {
        $migration = file_get_contents(database_path('migrations/2026_08_18_000001_add_last_login_to_tenants_table.php'));

        $this->assertStringContainsString("'last_login_at'", $migration);
        $this->assertStringContainsString("'last_login_host'", $migration);
        $this->assertStringContainsString("config('tenancy.landlord_connection', 'landlord')", $migration);

        $usernameMigration = file_get_contents(database_path('migrations/2026_08_19_000001_add_last_login_username_to_tenants_table.php'));
        $this->assertStringContainsString("'last_login_username'", $usernameMigration);
    }

    public function test_successful_tenant_login_records_last_sign_in_with_username(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Auth/LoginController.php'));

        $this->assertStringContainsString('recordTenantLogin($request, $user)', $controller);
        $this->assertStringContainsString("'last_login_at' => now()", $controller);
        $this->assertStringContainsString("'last_login_host' => $" . "tenantContext->domain ?: $" . "request->getHost()", $controller);
        $this->assertStringContainsString("'last_login_username' => $" . "user->username", $controller);
    }

    public function test_platform_tenant_pages_show_last_sign_in_status(): void
    {
        $index = file_get_contents(resource_path('views/system/tenants/index.blade.php'));
        $show = file_get_contents(resource_path('views/system/tenants/show.blade.php'));

        $this->assertStringContainsString('Last sign-in', $index);
        $this->assertStringContainsString('$tenant->last_login_at', $index);
        $this->assertStringContainsString('$tenant->last_login_username', $index);
        $this->assertStringNotContainsString('Unknown user', $index);
        $this->assertStringNotContainsString('$tenant->last_login_host', $index);
        $this->assertStringContainsString('Last sign-in host', $show);
        $this->assertStringContainsString('Last sign-in user', $show);
        $this->assertStringContainsString('Never', $show);
    }
}
