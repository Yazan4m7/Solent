<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardMapEmptyStateTest extends TestCase
{
    public function test_empty_map_has_a_readable_status_without_replacing_the_map(): void
    {
        $html = view('partials.jordan-dashboard-map', ['dashboardMetrics' => []])->render();
        $this->assertStringContainsString('data-map-empty role="status" hidden', $html);
        $this->assertStringContainsString('<span class="solent-dash-jordan-empty-message">No activity data for this period</span>', $html);
        $this->assertStringContainsString('data-map-canvas', $html);
        $this->assertMatchesRegularExpression('/\.solent-dash-jordan-empty-message\s*\{[^}]*background: #ffffff;[^}]*color: #1e293b;[^}]*font-size: 14px;/s', $html);
    }

    public function test_map_only_drills_into_governorates_with_real_activity(): void
    {
        $html = view('partials.jordan-dashboard-map', [
            'dashboardMetrics' => [],
            'drilldown' => true,
        ])->render();

        $this->assertStringContainsString('const canDrilldown = isDrilldownEnabled(element) && Number(value) > 0;', $html);
        $this->assertStringContainsString('if (Number(cases) <= 0) return;', $html);
        $this->assertStringContainsString("const hasActivity = cases.some(function (value) { return value > 0; });", $html);
        $this->assertStringContainsString('empty.hidden = hasVisibleActivity;', $html);
    }

    public function test_map_does_not_fabricate_district_activity(): void
    {
        $html = view('partials.jordan-dashboard-map', ['dashboardMetrics' => []])->render();

        $this->assertStringNotContainsString('staticMetric', $html);
        $this->assertStringNotContainsString('areaMetrics', $html);
        $this->assertStringNotContainsString('areaTooltipHandlers', $html);
        $this->assertStringContainsString("fill: neutralFill,", $html);
        $this->assertStringContainsString("'aria-label': area.name", $html);
    }

    public function test_district_labels_are_constrained_to_the_map(): void
    {
        $html = view('partials.jordan-dashboard-map', ['dashboardMetrics' => []])->render();

        $this->assertMatchesRegularExpression('/\.solent-dash-jordan-canvas svg\s*\{[^}]*overflow: hidden;/s', $html);
        $this->assertStringContainsString('function placeLabels(labels, width, height, characterWidth)', $html);
        $this->assertStringContainsString('label.x = Math.max(estimatedHalfWidth + 8, Math.min(width - estimatedHalfWidth - 8, label.x));', $html);
        $this->assertStringContainsString('adm2Layer.appendChild(label);', $html);
        $this->assertStringContainsString('return pointInFeature(representativePoint(feature), governorateFeature);', $html);
    }
}
