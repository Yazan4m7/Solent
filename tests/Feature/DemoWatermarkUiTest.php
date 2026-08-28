<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoWatermarkUiTest extends TestCase
{
    public function test_demo_watermark_uses_the_doubled_responsive_size(): void
    {
        $css = file_get_contents(public_path('assets/css/solent-demo.css'));

        $this->assertStringContainsString('content: "DEMO";', $css);
        $this->assertStringContainsString('font-size: clamp(32px, 2.4vw, 54px);', $css);
        $this->assertStringNotContainsString('font-size: clamp(16px, 1.2vw, 27px);', $css);
    }
}
