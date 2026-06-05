<?php

namespace Tests\Feature;

use Tests\TestCase;

class CasesMonitorViewTest extends TestCase
{
    public function test_cases_monitor_uses_the_available_blank_layout(): void
    {
        $views = [
            'screen.blade.php',
            'defaultScreen.blade.php',
            'screenFourBoxes1Column.blade.php',
            'screenThreeColumns.blade.php',
        ];

        foreach ($views as $file) {
            $view = file_get_contents(resource_path('views/generic/' . $file));

            $this->assertStringContainsString("@extends('layout.mainlayout_blank')", $view);
            $this->assertStringNotContainsString("@extends('oldLayout.mainlayout_blank')", $view);
        }
    }
}
