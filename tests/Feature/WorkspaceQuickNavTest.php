<?php

namespace Tests\Feature;

use Tests\TestCase;

class WorkspaceQuickNavTest extends TestCase
{
    public function test_authenticated_layout_renders_one_desktop_and_one_mobile_quick_nav(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $partial = file_get_contents(resource_path('views/layouts/navbars/quicknav.blade.php'));

        $this->assertStringContainsString("['quickNavVariant' => 'desktop']", $layout);
        $this->assertStringContainsString("['quickNavVariant' => 'mobile']", $layout);
        $this->assertStringContainsString("route' => 'home'", $partial);
        $this->assertStringContainsString("route' => 'admin-dashboard-v2'", $partial);
        $this->assertStringContainsString("route' => 'cases-index'", $partial);
        $this->assertStringContainsString("route' => 'view-cases-monitor'", $partial);
        $this->assertStringContainsString("route' => 'delivery-schedule'", $partial);
        $this->assertStringContainsString('$canUseQuickNav', $partial);
    }

    public function test_mobile_nav_and_delivery_header_are_compact_and_responsive(): void
    {
        $css = file_get_contents(public_path('assets/css/solent-quick-nav.css'));

        $this->assertStringContainsString('.solent-quick-nav--mobile', $css);
        $this->assertStringContainsString('@media (max-width: 991.98px)', $css);
        $this->assertStringContainsString('min-height: 116px;', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
    }
}
