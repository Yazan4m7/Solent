<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class MobileLayoutRegressionTest extends TestCase
{
    public function test_invoice_list_has_one_plugin_owned_wrapper_and_readable_columns(): void
    {
        $user = new User(['name' => 'Layout preview', 'is_admin' => 1]);
        $user->id = 1;
        $this->actingAs($user);

        $html = view('generic.invoices-list', [
            'from' => '2026-08-01', 'to' => '2026-08-30',
            'clients' => collect(), 'invoices' => collect(),
            'currencyContext' => ['display' => 'JOD'],
        ])->render();

        $this->assertStringNotContainsString('id="datatable_wrapper"', $html);
        $this->assertSame(1, substr_count($html, 'id="datatable"'));
        $this->assertStringContainsString('invoice-filter-field--doctor', $html);
        $this->assertStringContainsString('repeat(2, minmax(0, 1fr))', $html);
        $this->assertStringContainsString('min-width: 64px', $html);
        $this->assertStringContainsString('"responsive": false', $html);
        $this->assertStringContainsString("<'solent-table-scroll't>", $html);
    }

    public function test_target_tables_scroll_without_moving_their_controls(): void
    {
        foreach ([
            'resources/views/generic/invoices-list.blade.php',
            'app/Modules/Cases/Resources/views/cases/index.blade.php',
            'app/Modules/Delivery/Resources/views/delivery/delivery-schedule.blade.php',
            'app/Modules/Cases/Resources/views/cases/admin-dashboardv2.blade.php',
        ] as $path) {
            $source = file_get_contents(base_path($path));
            $this->assertStringContainsString('solent-table-frame', $source, $path);
            $this->assertStringContainsString("<'solent-table-scroll't>", $source, $path);
            $this->assertStringContainsString('solent-table-name', $source, $path);
        }
        $css = file_get_contents(public_path('assets/css/solent-demo.css'));
        $this->assertStringContainsString('.solent-table-scroll {', $css);
        $this->assertStringContainsString('overscroll-behavior-x: contain;', $css);
        $this->assertStringContainsString('.solent-table-frame .dataTables_length select {', $css);
    }

    public function test_cases_use_scoped_actions_and_plugin_column_visibility(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/index.blade.php'));
        $this->assertStringContainsString('cases-filter-tools', $view);
        $this->assertStringContainsString('col-md-6 col-12 mb-3', $view);
        $this->assertStringNotContainsString('table-layout: fixed', $view);
        $this->assertStringContainsString("DataTable().column(colNumber - 1)", $view);
        $this->assertStringNotContainsString("$('td:nth-child(2),th:nth-child(2)').hide()", $view);
    }

    public function test_cases_toolbar_compacts_only_on_mobile_and_contains_its_column_options(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/index.blade.php'));
        $mobile = substr($view, strpos($view, '@media screen and (max-width: 768px)'));
        $mobile = substr($mobile, 0, strpos($mobile, '</style>'));

        $this->assertMatchesRegularExpression('/\.cases-results \.dataTables_length\s*\{\s*display: none;/', $mobile);
        $this->assertMatchesRegularExpression('/\.cases-results \.dataTables_filter input\s*\{\s*width: 160px;/', $mobile);
        $this->assertStringContainsString('"lengthChange": true', $view);
        $this->assertStringContainsString('data-boundary="viewport"', $view);
        $this->assertStringContainsString('dropdown-menu-right cases-columns-menu', $view);
        $this->assertMatchesRegularExpression('/@media screen and \(max-width: 768px\)[\s\S]*\.cases-filter-tools \.cases-columns-menu\s*\{[^}]*left:\s*0 !important;[^}]*right:\s*auto !important;/i', $view);
        $this->assertStringNotContainsString('style="padding-left:10px"', $view);
        $this->assertMatchesRegularExpression('/\.cases-columns-menu \.form-check-input\s*\{\s*position: static;/', $view);
        $this->assertMatchesRegularExpression('/\.cases-filter-tools \.btn\s*\{\s*display: inline-flex;\s*align-items: center;\s*justify-content: center;/', $view);
    }

    public function test_classic_operations_keeps_patient_columns_visible_and_wraps_stage_cards(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/admin-dashboardv2.blade.php'));
        $this->assertStringNotContainsString('.activeTable tr > *:nth-child(3)', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr));', $view);
        $this->assertStringContainsString('targets: [nameColumnOffset, nameColumnOffset + 1]', $view);
    }

    public function test_dashboard_map_has_a_mobile_projection_and_chart_labels_can_skip(): void
    {
        $map = file_get_contents(resource_path('views/partials/jordan-dashboard-map.blade.php'));
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));
        $this->assertStringContainsString('isMobilePreview ? 300 : 180', $map);
        $this->assertStringContainsString("isExpandedView || isMobilePreview ? 'xMidYMid meet'", $map);
        $this->assertStringContainsString("addEventListener('change'", $map);
        $this->assertStringContainsString('height: clamp(260px, 75vw, 340px);', $css);
        $this->assertStringContainsString('autoSkipPadding: 12, maxRotation: 0', $view);
    }
}
