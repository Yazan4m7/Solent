<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserManagementContactFieldsTest extends TestCase
{
    public function test_employee_user_forms_and_list_do_not_show_retired_contact_fields(): void
    {
        $create = file_get_contents(base_path('app/Modules/Users/Resources/views/users/create.blade.php'));
        $edit = file_get_contents(base_path('app/Modules/Users/Resources/views/users/edit.blade.php'));
        $index = file_get_contents(base_path('app/Modules/Users/Resources/views/users/index.blade.php'));

        foreach ([$create, $edit] as $form) {
            $this->assertStringNotContainsString('name="phone"', $form);
            $this->assertStringNotContainsString('name="email"', $form);
        }

        $this->assertStringNotContainsString('<th>Phone</th>', $index);
        $this->assertStringNotContainsString('$user->phone', $index);
        $this->assertStringContainsString("@include('alerts.errors')", $create);
        $this->assertStringContainsString('class="user-form-grid"', $create);
    }

    public function test_employee_user_controller_lazily_nulls_retired_contact_fields(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Users/Http/Controllers/UserController.php'));

        $this->assertStringNotContainsString("'email' => 'required", $controller);
        $this->assertStringNotContainsString("'phone' => 'required", $controller);
        $this->assertStringContainsString("'email' => null", $controller);
        $this->assertStringContainsString("'phone' => null", $controller);
        $this->assertStringContainsString('$user->email = null;', $controller);
        $this->assertStringContainsString('$user->phone = null;', $controller);
        $this->assertStringContainsString("redirect()->route('users-index')", $controller);
        $this->assertStringContainsString("with('success', 'The user has been successfully created')", $controller);
    }

    public function test_employee_phone_column_is_kept_but_made_nullable(): void
    {
        $files = glob(database_path('migrations/*_make_user_phone_nullable.php'));

        $this->assertNotEmpty($files, 'Expected a lazy employee phone cleanup migration.');

        $migration = file_get_contents($files[0]);

        $this->assertStringContainsString("Schema::hasColumn('users', 'phone')", $migration);
        $this->assertStringContainsString('ALTER TABLE `users` MODIFY `phone` VARCHAR(191) NULL DEFAULT NULL', $migration);
    }

    public function test_other_employee_creation_paths_do_not_restore_retired_contact_fields(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $demoMode = file_get_contents(app_path('Support/DemoMode.php'));
        $provisioning = file_get_contents(app_path('Support/Tenancy/TenantProvisioningService.php'));

        $this->assertStringContainsString("Auth::routes(['register' => false]);", $routes);
        $this->assertStringContainsString("'email' => null", $demoMode);
        $this->assertStringContainsString("'phone' => null", $demoMode);
        $this->assertStringContainsString("'email' => null", $provisioning);
        $this->assertStringContainsString("'phone' => null", $provisioning);
    }
}
