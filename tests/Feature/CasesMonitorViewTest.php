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

    public function test_cases_monitor_headers_are_rtl_safe_and_localized(): void
    {
        $view = file_get_contents(resource_path('views/generic/screen.blade.php'));

        $this->assertStringContainsString('class="case-monitor-header"', $view);
        $this->assertStringContainsString('case-monitor-title" dir="auto"', $view);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $view);
        $this->assertStringContainsString('.case-monitor-table.dataTable thead th {', $view);
        $this->assertStringContainsString('padding: 8px 5px !important;', $view);
        $this->assertStringContainsString('white-space: normal !important;', $view);
        $this->assertStringContainsString('overflow: hidden;', $view);
        $this->assertStringContainsString('.dataTables_scrollBody .case-monitor-table.dataTable thead th {', $view);
        $this->assertStringContainsString('height: 0 !important;', $view);
        $this->assertStringContainsString('.case-monitor-date-column { width: 17% !important; }', $view);
        $this->assertStringNotContainsString('style="width:6%;text-align:center"', $view);
        $this->assertStringContainsString("$" . "ui['Doctor'] ?? 'Doctor'", $view);
        $this->assertStringContainsString("$" . "ui['Delivery date'] ?? 'Delivery date'", $view);
        $this->assertStringContainsString("$" . "ui['Status'] ?? 'Status'", $view);
    }

    public function test_cases_monitor_uses_the_application_palette(): void
    {
        $view = file_get_contents(resource_path('views/generic/screen.blade.php'));

        $this->assertStringContainsString('var(--color-main-bg, #f8fafc)', $view);
        $this->assertStringContainsString('var(--color-primary-teal, #6366f1)', $view);
        $this->assertStringContainsString('var(--color-surface-soft, #eef2ff)', $view);
        $this->assertStringContainsString('case-monitor-stage-heading', $view);
        $this->assertStringNotContainsString('portlet-heading bg-info', $view);
    }

    public function test_cases_monitor_badges_match_the_cases_list_status_classes(): void
    {
        $view = file_get_contents(resource_path('views/generic/screen.blade.php'));

        $this->assertStringContainsString('badge badge-success solent-case-status-badge', $view);
        $this->assertStringContainsString('badge badge-primary solent-case-status-badge', $view);
        $this->assertStringContainsString('badge badge-danger solent-case-status-badge', $view);
        $this->assertStringContainsString('badge badge-warning solent-case-status-badge', $view);
        $this->assertStringContainsString('body .case-monitor-card .badge-success', $view);
        $this->assertStringContainsString('background: #10b981 !important;', $view);
        $this->assertStringContainsString('background: #ef4444 !important;', $view);
    }
}
