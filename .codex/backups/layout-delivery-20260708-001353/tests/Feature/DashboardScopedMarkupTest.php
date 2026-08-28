<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class DashboardScopedMarkupTest extends TestCase
{
    public function test_dashboard_uses_the_new_scoped_dashboard_markup_and_css(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));

        $this->assertStringContainsString('class="solent-dash"', $view);
        $this->assertStringContainsString('solentDashProductionLoad', $view);
        $this->assertStringContainsString('Revenue by Service Type', $view);
        $this->assertStringContainsString('Case Activity by Governorate', $view);
        $this->assertStringContainsString('.solent-dash', $css);

        $this->assertStringNotContainsString('class="ed-dashboard', $view);
        $this->assertStringNotContainsString('.ed-dashboard', $css);
        $this->assertStringNotContainsString('stat-card', $view);
    }

    public function test_dashboard_uses_sample_values_by_default_without_visible_mode_controls(): void
    {
        config()->set('features.dashboard.sample_data', true);

        $html = $this->renderDashboard();

        $this->assertStringContainsString('data-dashboard-sample-mode="on"', $html);
        $this->assertStringContainsString('428,540', $html);
        $this->assertStringContainsString('3,721', $html);
        $this->assertStringContainsString('2,145', $html);
        $this->assertStringNotContainsString('Real data is on', $html);
        $this->assertStringNotContainsString('Show sample data', $html);
        $this->assertStringNotContainsString('sample_data=', $html);
    }

    public function test_dashboard_can_render_real_zero_values_when_sample_mode_is_disabled_by_config(): void
    {
        config()->set('features.dashboard.sample_data', false);

        $html = $this->renderDashboard();

        $this->assertStringContainsString('data-dashboard-sample-mode="off"', $html);
        $this->assertStringContainsString('JOD 0', $html);
        $this->assertStringNotContainsString('428,540', $html);
        $this->assertStringNotContainsString('3,721', $html);
        $this->assertStringNotContainsString('2,145', $html);
    }

    public function test_stage_daily_utilization_uses_vertical_active_waiting_case_chart(): void
    {
        $html = $this->renderDashboard([
            'productionLoadRows' => [
                ['label' => 'Design', 'jobs' => 7, 'active' => 4, 'waiting' => 3, 'utilization' => 57, 'jobsScaled' => 100],
                ['label' => 'Milling', 'jobs' => 2, 'active' => 1, 'waiting' => 1, 'utilization' => 50, 'jobsScaled' => 29],
            ],
        ]);
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));

        $this->assertStringContainsString("type: 'bar'", $html);
        $this->assertStringNotContainsString("type: 'horizontalBar'", $html);
        $this->assertStringContainsString('Active cases', $html);
        $this->assertStringContainsString('Waiting cases', $html);
        $this->assertStringContainsString('4 active cases', $html);
        $this->assertStringContainsString('3 waiting cases', $html);
        $this->assertStringContainsString('7 total cases', $html);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: minmax(300px, 0.72fr) minmax(520px, 1.43fr) minmax(380px, 1.05fr);', $css);
        $this->assertStringContainsString('align-items: stretch;', $css);
        $this->assertLessThan(
            strpos($html, 'class="solent-dash-load-list"'),
            strpos($html, 'id="solentDashProductionLoad"')
        );
    }

    public function test_top_dashboard_cards_render_values_without_mini_charts(): void
    {
        $html = $this->renderDashboard();
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));

        $this->assertStringContainsString('class="solent-dash-value-number"', $html);
        $this->assertStringNotContainsString('solent-dash-mini-chart', $html);
        $this->assertStringNotContainsString('solentDashSpark', $html);
        $this->assertStringNotContainsString('solentDashMiniBars', $html);
        $this->assertStringNotContainsString('solent-dash-delta', $html);
        $this->assertStringNotContainsString('solent-dash-note', $html);
        $this->assertStringNotContainsString('.solent-dash .solent-dash-mini-chart', $css);
    }

    public function test_dashboard_polish_keeps_cards_compact_and_mobile_safe(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));

        $this->assertStringContainsString('min-height: 86px;', $css);
        $this->assertStringContainsString('border-radius: 8px', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: 36px minmax(0, 1fr);', $css);
        $this->assertStringContainsString('grid-column: 2;', $css);
        $this->assertStringContainsString('position: fixed;', $view);
        $this->assertStringContainsString('bottom: 18px;', $view);
        $this->assertStringContainsString("asset('assets/css/elegant-dashboard.css')", $view);
        $this->assertStringNotContainsString('elegant-dashboard.css?v=', $view);
    }

    public function test_payments_index_renders_formatted_total_without_raw_blade_expression(): void
    {
        $view = file_get_contents(resource_path('views/generic/payments-list.blade.php'));
        $controller = file_get_contents(app_path('Modules/Clients/Http/Controllers/ClientsController.php'));
        $compiled = app('blade.compiler')->compileString($view);

        $this->assertStringContainsString('$paymentsTotal = $payments->sum(\'amount\');', $controller);
        $this->assertStringContainsString("compact('payments','paymentsTotal'", $controller);
        $this->assertStringContainsString('number_format($paymentsTotal, 2)', $view);
        $this->assertStringNotContainsString('{{number_format($payments->sum', $view);
        $this->assertStringNotContainsString('@if(isset($tag))', $compiled);
        $this->assertStringNotContainsString('{{ number_format($paymentsTotal, 2) }}', $compiled);
    }

    public function test_authenticated_nav_uses_the_profile_chip_instead_of_stock_avatar_image(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/navs/auth.blade.php'));

        $this->assertStringContainsString('solent-layout-profile', $view);
        $this->assertStringContainsString('solent-layout-profile-avatar', $view);
        $this->assertStringContainsString('solent-layout-profile-role', $view);
        $this->assertStringContainsString('solent-layout-profile-shell', $view);
        $this->assertStringContainsString('headerActionsCol', $view);
        $this->assertStringContainsString('Administrator', $view);
        $this->assertStringNotContainsString('collapse navbar-collapse', $view);
        $this->assertStringNotContainsString('display: none !important;', $this->profileCssBlock($view));
        $this->assertStringContainsString('position: static;', $this->profileNavCssBlock($view));
        $this->assertStringContainsString('top: auto;', $this->profileNavCssBlock($view));
        $this->assertStringContainsString('nav.stickMe .headerRow {', $view);
        $this->assertStringContainsString('nav.stickMe .headerRow > .headerTitleCol {', $view);
        $this->assertStringContainsString('color-mix(in srgb, var(--color-topbar, var(--text-1)) 50%, transparent)', $view);
        $this->assertStringNotContainsString('anime3.png', $view);
        $this->assertLessThan(
            strpos($view, 'solent-layout-profile-shell'),
            strpos($view, 'headerSearchCol')
        );
    }

    public function test_authenticated_nav_uses_phone_header_proportions_and_hides_the_brand_logo(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/navs/auth.blade.php'));
        $phoneCss = $this->phoneNavCssBlock($view);

        $this->assertStringContainsString('display: none !important;', $phoneCss);
        $this->assertStringContainsString('flex-direction: column !important;', $phoneCss);
        $this->assertStringContainsString('flex: 0 0 20% !important;', $phoneCss);
        $this->assertStringContainsString('flex: 0 0 80% !important;', $phoneCss);
        $this->assertStringContainsString('flex: 0 0 75% !important;', $phoneCss);
        $this->assertStringContainsString('flex: 0 0 25% !important;', $phoneCss);
    }

    public function test_footer_mobile_nav_toggle_is_null_safe_and_animates_the_bars(): void
    {
        $view = file_get_contents(resource_path('views/layouts/footer.blade.php'));

        $this->assertStringContainsString('if (navbarToggle) {', $view);
        $this->assertStringContainsString("navbarToggle.classList.toggle('toggled');", $view);
        $this->assertStringContainsString('if (overlay) {', $view);
    }

    public function test_sidebar_brand_logo_is_centered_in_the_menu_header(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('justify-content: center;', $view);
        $this->assertStringContainsString('margin: 0 auto;', $view);
        $this->assertStringContainsString('korvion-sidebar-logo-full', $view);
    }

    public function test_case_activity_card_keeps_map_without_repeating_customer_total(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));
        $map = file_get_contents(resource_path('views/partials/jordan-dashboard-map.blade.php'));

        $this->assertStringContainsString('Case Activity by Governorate', $view);
        $this->assertStringContainsString('solent-dash-map-card-summary', $view);
        $this->assertStringContainsString('solent-dash-map-modal-eyebrow', $view);
        $this->assertStringContainsString('jordan-dashboard-map', $view);
        $this->assertStringContainsString('solent-dash-map-button', $view);
        $this->assertStringContainsString('solentJordanMapModal', $view);
        $this->assertStringContainsString('solentJordanGovernorateModal', $view);
        $this->assertStringContainsString('dashboardMapAreas', $view);
        $this->assertStringContainsString('solentDashInitJordanMapDrilldown', $view);
        $this->assertStringContainsString('solent-dash-map-detail-backdrop', $view);
        $this->assertStringContainsString('solent-dash-map-detail-open', $view);
        $this->assertStringContainsString('if (!isFocusedView)', $map);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $css);
        $this->assertStringContainsString('.solent-dash .solent-dash-map-button', $css);
        $this->assertStringNotContainsString('Customer Overview', $view);
        $this->assertStringNotContainsString('solent-dash-country-list', $view);
        $this->assertStringNotContainsString('solent-dash-track', $view);
        $this->assertStringNotContainsString('.solent-dash .solent-dash-country-list', $css);
    }

    public function test_dashboard_sample_activity_is_dental_lab_specific(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertStringContainsString('New dental case #CASE-1256', $view);
        $this->assertStringContainsString('Clinic payment received', $view);
        $this->assertStringNotContainsString('Order #ORD-1255 shipped', $view);
        $this->assertStringNotContainsString('Low stock alert', $view);
        $this->assertStringNotContainsString('New customer registered', $view);
    }

    public function test_operations_dashboard_case_rows_open_the_rendered_case_actions_modal(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/admin-dashboardv2.blade.php'));

        $this->assertStringContainsString('<x-partiels.operationsCaseSlidePanel', $view);
        $this->assertStringContainsString('function YSH_findCaseActionsModal(', $view);
        $this->assertStringContainsString("window.jQuery(caseActionsModal).modal('show');", $view);
    }

    public function test_case_actions_modal_keeps_each_job_compact_and_hover_actions_readable(): void
    {
        $view = file_get_contents(resource_path('views/components/partiels/caseActionsModal.blade.php'));
        $casesView = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/index.blade.php'));
        $css = file_get_contents(public_path('assets/css/solent-demo.css'));

        $this->assertStringContainsString('solent-case-actions-modal__job-details', $view);
        $this->assertStringContainsString('grid-template-columns: minmax(88px, 0.32fr) minmax(0, 1fr);', $css);
        $this->assertStringContainsString('.solent-case-actions-modal__actions .btn-outline-secondary:hover', $casesView);
        $this->assertStringContainsString('background: #4f46e5 !important;', $casesView);
        $this->assertStringContainsString('background: #dc2626 !important;', $casesView);
        $this->assertStringContainsString('background: #334155 !important;', $casesView);
        $this->assertStringContainsString('color: #ffffff !important;', $casesView);
    }

    public function test_sidebar_uses_inline_svg_icons_without_material_symbol_font(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));
        $icon = file_get_contents(resource_path('views/layouts/navbars/partials/sidebar-icon.blade.php'));
        $liveView = preg_replace('/\{\{--.*?--\}\}/s', '', $view);

        $this->assertStringContainsString("@include('layouts.navbars.partials.sidebar-icon'", $view);
        $this->assertStringContainsString('solent-sidebar-icon', $icon);
        $this->assertStringNotContainsString('Material+Symbols', $view);
        $this->assertStringNotContainsString('material-symbols-outlined', $view);
        $this->assertStringNotContainsString('<i class="fa', $liveView);
    }

    public function test_summary_reports_share_the_datatable_initializer(): void
    {
        $partial = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/partials/report-ui.blade.php'));
        $reportViews = [
            'numOfUnits.blade.php',
            'jobTypes.blade.php',
            'QC.blade.php',
            'repeats.blade.php',
            'case-materials-report.blade.php',
            'implants.blade.php',
        ];
        $jobTypes = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/jobTypes.blade.php'));
        $repeats = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/repeats.blade.php'));
        $implants = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/implants.blade.php'));

        $this->assertStringContainsString("@push('js')", $partial);
        $this->assertStringNotContainsString('@once', $partial);
        $this->assertStringContainsString("$('table.report-table').not('#datatable').each", $partial);
        $this->assertStringContainsString('$(this).DataTable({', $partial);
        $this->assertStringContainsString("pagingType: 'full_numbers'", $partial);
        $this->assertStringContainsString('.report-switch__track', $partial);
        $this->assertStringContainsString('border-radius: 14px !important;', $partial);
        $this->assertStringContainsString('.dt-buttons .dt-button', $partial);
        $this->assertStringContainsString('body.white-content .report-filter .filter-option-inner {', $partial);
        $this->assertStringContainsString('align-items: center;', $partial);
        $this->assertStringContainsString('overflow-x: auto;', $partial);

        $this->assertStringContainsString('class="report-switch"', $jobTypes);
        $this->assertStringContainsString('class="report-switch"', $repeats);
        $this->assertStringContainsString('class="report-switch"', $implants);
        $this->assertStringNotContainsString('gitcdn.github.io/bootstrap-toggle', $jobTypes);
        $this->assertStringNotContainsString('gitcdn.github.io/bootstrap-toggle', $repeats);
        $this->assertStringNotContainsString('gitcdn.github.io/bootstrap-toggle', $implants);
        $this->assertStringNotContainsString('data-toggle="toggle"', $jobTypes);
        $this->assertStringNotContainsString('data-toggle="toggle"', $repeats);
        $this->assertStringNotContainsString('data-toggle="toggle"', $implants);

        foreach ($reportViews as $reportView) {
            $view = file_get_contents(base_path("app/Modules/Reports/Resources/views/reports/{$reportView}"));

            $this->assertStringContainsString("@include('reports.partials.report-ui')", $view);
            $this->assertStringContainsString('report-table', $view);
        }
    }

    private function renderDashboard(array $overrides = []): string
    {
        $this->actingAsDashboardUser();

        return view('dashboard', array_merge([
            'currencyContext' => ['display' => 'JOD'],
            'paymentsReceivedToday' => [],
            'DeliveriesToday' => [],
            'compUnitsCount7Days' => [],
            'compCasesCount7Days' => [],
            'collectionsInLast30Days' => [],
            'compCasesCount30Days' => [],
            'compUnitsCount30Days' => [],
            'sales30Days' => [],
            'last7DaysLabels' => [],
            'last30DaysLabels' => [],
            'CompletedJobsToday' => 0,
            'ActiveJobsToday' => 0,
            'waitingJobsToday' => 0,
        ], $overrides))->render();
    }

    private function actingAsDashboardUser(): void
    {
        $user = new User([
            'first_name' => 'Yazan',
            'name' => 'Yazan N.',
            'email' => 'yazan@example.test',
            'password' => 'unused',
            'is_admin' => 1,
        ]);
        $user->id = 1;
        $user->exists = true;

        $this->actingAs($user);
    }

    private function profileCssBlock(string $view): string
    {
        preg_match('/\.dotsDiv\s*\{.*?\}/s', $view, $matches);

        return $matches[0] ?? '';
    }

    private function profileNavCssBlock(string $view): string
    {
        preg_match('/\.solent-layout-profile-shell \.navbar-nav\s*\{.*?\}/s', $view, $matches);

        return $matches[0] ?? '';
    }

    private function phoneNavCssBlock(string $view): string
    {
        preg_match('/@media screen and \(max-width: 575\.98px\) \{.*?\n    \}/s', $view, $matches);

        return $matches[0] ?? '';
    }
}
