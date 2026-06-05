<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class CreateCasePageTest extends TestCase
{
    public function test_units_dialog_uses_centered_soft_white_shell_styles(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/create.blade.php'));

        $this->assertStringContainsString('min-height: calc(100% - 1rem);', $view);
        $this->assertStringContainsString('display: flex;', $view);
        $this->assertStringContainsString('align-items: center;', $view);
        $this->assertStringContainsString('linear-gradient(145deg, #ffffff 0%, #fbfcff 100%)', $view);
    }

    public function test_create_case_page_renders_for_admin_user(): void
    {
        $user = new User([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'username' => 'test-admin',
            'email' => 'test-admin@example.com',
            'password' => 'unused',
            'is_admin' => 1,
        ]);
        $user->id = 1;
        $user->exists = true;

        $response = $this->actingAs($user)->get('/new-case');

        $response->assertOk();
        $response->assertSee('Amount (JOD)');
    }
}
