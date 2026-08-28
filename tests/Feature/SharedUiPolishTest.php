<?php

namespace Tests\Feature;

use Tests\TestCase;

class SharedUiPolishTest extends TestCase
{
    public function test_shared_case_badges_use_one_compact_width_and_monitor_columns_reserve_space(): void
    {
        $css = file_get_contents(public_path('assets/css/site-typography.css'));

        $this->assertStringContainsString('inline-size: 104px !important;', $css);
        $this->assertStringContainsString('width: 104px !important;', $css);
        $this->assertStringContainsString('body th.solent-case-status-column', $css);

        foreach ([
            'screen.blade.php',
            'defaultScreen.blade.php',
            'screenThreeColumns.blade.php',
            'screenFourBoxes1Column.blade.php',
        ] as $view) {
            $markup = file_get_contents(resource_path("views/generic/{$view}"));

            $this->assertStringContainsString('<th class="solent-case-status-column">Status</th>', $markup, $view);
            $this->assertStringContainsString('<td class="solent-case-status-column">', $markup, $view);
            $this->assertStringContainsString('box-sizing: border-box;', $markup, $view);
            $this->assertStringContainsString('width: 100%;', $markup, $view);
        }
    }
}
