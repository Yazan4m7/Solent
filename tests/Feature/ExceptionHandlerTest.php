<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
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
}
