<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class CreateCasePageTest extends TestCase
{
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
