<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function test_validation_exceptions_redirect_back_instead_of_rendering_generic_500(): void
    {
        Route::middleware('web')->post('/_test/validation-exception', function (): void {
            throw ValidationException::withMessages([
                'username' => ['These credentials do not match our records.'],
            ]);
        });

        $response = $this->from('/login')->post('/_test/validation-exception');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    public function test_debug_error_page_shows_a_short_developer_hint(): void
    {
        config()->set('app.debug', true);
        Route::get('/_test/runtime-exception', function (): void {
            throw new RuntimeException('Monitor layout is missing.');
        });

        $response = $this->get('/_test/runtime-exception');

        $response->assertStatus(500);
        $response->assertSee('Developer hint');
        $response->assertSee('Monitor layout is missing.');
    }

    public function test_production_error_page_hides_the_developer_hint(): void
    {
        config()->set('app.debug', false);
        Route::get('/_test/runtime-exception', function (): void {
            throw new RuntimeException('Monitor layout is missing.');
        });

        $response = $this->get('/_test/runtime-exception');

        $response->assertStatus(500);
        $response->assertDontSee('Developer hint');
        $response->assertDontSee('Monitor layout is missing.');
    }
}
