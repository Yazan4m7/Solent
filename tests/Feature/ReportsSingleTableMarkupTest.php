<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReportsSingleTableMarkupTest extends TestCase
{
    public function test_non_material_reports_render_one_consolidated_table_with_the_applied_time_range(): void
    {
        $reportViews = [
            'numOfUnits.blade.php',
            'jobTypes.blade.php',
            'QC.blade.php',
            'repeats.blade.php',
            'implants.blade.php',
        ];

        foreach ($reportViews as $reportView) {
            $view = file_get_contents(base_path("app/Modules/Reports/Resources/views/reports/{$reportView}"));

            $this->assertSame(1, substr_count($view, '<table'), $reportView);
            $this->assertStringContainsString('data-report-consolidated="true"', $view, $reportView);
            $this->assertStringContainsString("@include('reports.partials.report-range')", $view, $reportView);
            $this->assertStringContainsString('class="report-filter-label"', $view, $reportView);
            $this->assertStringContainsString('far fa-calendar', $view, $reportView);
        }

        $rangePartial = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/partials/report-range.blade.php'));
        $this->assertStringContainsString("format('Y-m-d H:i')", $rangePartial);
        $this->assertStringContainsString('class="report-applied-range"', $rangePartial);
    }

    public function test_five_sidebar_reports_use_the_shared_solent_report_shell(): void
    {
        $reportViews = [
            'numOfUnits.blade.php',
            'jobTypes.blade.php',
            'QC.blade.php',
            'repeats.blade.php',
            'case-materials-report.blade.php',
        ];

        foreach ($reportViews as $reportView) {
            $view = file_get_contents(base_path("app/Modules/Reports/Resources/views/reports/{$reportView}"));

            $this->assertStringContainsString('class="solent-report-page"', $view, $reportView);
            $this->assertStringContainsString("@include('reports.partials.report-header'", $view, $reportView);
            $this->assertStringContainsString("@include('reports.partials.report-section-heading'", $view, $reportView);
            $this->assertStringContainsString('Apply Filters', $view, $reportView);
        }

        $header = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/partials/report-header.blade.php'));
        $this->assertSame(5, substr_count($header, "'route' =>"));
        $this->assertStringContainsString('aria-current="page"', $header);
    }

    public function test_materials_usage_keeps_its_detail_table_and_matches_the_shared_theme(): void
    {
        $view = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/case-materials-report.blade.php'));
        $reportUi = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/partials/report-ui.blade.php'));

        $this->assertSame(1, substr_count($view, '<table'));
        $this->assertStringContainsString('materials-usage-table', $view);
        $this->assertStringContainsString("@include('reports.partials.report-range'", $view);
        $this->assertStringContainsString('materialsPrintBtn', $view);
        $this->assertStringContainsString('.solent-report-page table.report-table.materials-usage-table thead th', $reportUi);
        $this->assertStringContainsString('background: var(--surface-raised) !important;', $reportUi);
    }

    public function test_qc_metrics_use_the_shared_report_summary_cards(): void
    {
        $view = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/QC.blade.php'));

        $this->assertStringContainsString('class="report-summary-grid"', $view);
        $this->assertSame(3, substr_count($view, 'class="report-summary-card"'));
        $this->assertStringNotContainsString('style="font-weight:bold;font-size:19px', $view);
    }

    public function test_qc_report_uses_chronological_range_bounds_and_filters_doctors_in_the_query(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Reports/Http/Controllers/ReportsController.php'));

        $this->assertStringContainsString("collect(\$selectedMonths)->sort()->values()", $controller);
        $this->assertStringContainsString("->whereBetween('created_at', [\$rangeStart, \$rangeEnd])", $controller);
        $this->assertStringContainsString("->whereIn('doctor_id', \$selectedClients)", $controller);
    }

    public function test_repeats_report_provides_chronological_range_bounds_to_the_view(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Reports/Http/Controllers/ReportsController.php'));
        $view = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/repeats.blade.php'));

        preg_match('/public function repeatsReport\(Request \$request\)(.*?)public function homeScreen/s', $controller, $matches);
        $repeatsMethod = $matches[1] ?? '';

        $this->assertStringContainsString("\$rangeStart = \\Carbon\\Carbon::parse", $repeatsMethod);
        $this->assertStringContainsString("'rangeStart','rangeEnd'", $repeatsMethod);
        $this->assertStringContainsString("\$rangeStart->format('Y-m-d H:i:s')", $view);
        $this->assertStringContainsString("\$rangeEnd->format('Y-m-d H:i:s')", $view);
    }
}
