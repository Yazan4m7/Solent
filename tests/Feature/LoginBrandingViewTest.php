<?php

namespace Tests\Feature;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class LoginBrandingViewTest extends TestCase
{
    public function test_login_view_initializes_and_renders_branding_variables(): void
    {
        $html = view('auth.login', [
            'errors' => new ViewErrorBag(),
        ])->render();

        $this->assertStringContainsString('<title>Solent |', $html);
        $this->assertStringContainsString('class="login-card"', $html);
        $this->assertStringContainsString('Solent branding', $html);
        $this->assertStringContainsString('class="brand-logo"', $html);
        $this->assertStringContainsString('images/brands/solent/solent_ui_icon.png', $html);
        $this->assertStringContainsString('filter: brightness(4);', $html);
        $this->assertStringNotContainsString('@php', $html);
    }
}
