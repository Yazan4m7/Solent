<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppVersionFooterTest extends TestCase
{
    public function test_shared_app_footer_displays_the_release_and_update_month(): void
    {
        $footer = file_get_contents(resource_path('views/layouts/footer.blade.php'));

        $this->assertStringContainsString('class="solent-app-release"', $footer);
        $this->assertStringContainsString('Version 1.1 &middot; Updated Aug 2026', $footer);
    }
}
