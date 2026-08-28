<?php

namespace Tests\Feature;

use App\client;
use App\Http\Middleware\AuthenticateClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GuardAwareAuthenticateMiddlewareTest extends TestCase
{
    public function test_client_guard_can_pass_authentication_middleware(): void
    {
        $client = new client();
        $client->setRawAttributes(['id' => 1, 'active' => 1]);
        Auth::guard('clients')->setUser($client);

        $response = app(AuthenticateClient::class)->handle(
            Request::create('/portal/dashboard'),
            fn () => response('client dashboard')
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('client dashboard', $response->getContent());
    }

    public function test_disabled_client_is_returned_to_client_login(): void
    {
        $client = new client();
        $client->setRawAttributes(['id' => 1, 'active' => 0]);
        Auth::guard('clients')->setUser($client);

        $response = app(AuthenticateClient::class)->handle(
            Request::create('/portal/dashboard'),
            fn () => response('client dashboard')
        );

        $this->assertSame(url('/portal/login'), $response->headers->get('Location'));
    }
}
