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
        $this->assertStringContainsString('Sales by Channel', $view);
        $this->assertStringContainsString('Customer Overview', $view);
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

    public function test_dashboard_has_an_expandable_jordan_governorate_heatmap(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $map = file_get_contents(resource_path('views/partials/jordan-dashboard-map.blade.php'));

        $this->assertStringContainsString('data-target="#solentJordanMapModal"', $view);
        $this->assertStringContainsString('id="solentJordanMapModal"', $view);
        $this->assertStringContainsString('https://www.geoboundaries.org/api/current/gbOpen/JOR/ADM1/', $map);
        $this->assertStringContainsString('data-map-loading', $map);
        $this->assertStringContainsString('data-map-error', $map);
        $this->assertStringContainsString('data-map-retry', $map);
        $this->assertStringContainsString('data-map-tooltip', $map);
        $this->assertStringContainsString('data-map-legend', $map);
        $this->assertStringContainsString("const neutralFill = '#cbd5e1';", $map);
        $this->assertStringContainsString("const blueRamp = ['#f4f8fc', '#d8e8f3', '#a8cce1', '#609dc3', '#1a6fa8'];", $map);
        $this->assertStringContainsString('filter: brightness(0.8);', $map);
        $this->assertStringContainsString('function fitExtentProjector', $map);
        $this->assertStringContainsString('function staticMetric', $map);
        $this->assertStringContainsString('Currently waiting', $map);
        $this->assertStringContainsString('This month', $map);
        $this->assertStringNotContainsString('solent-dash-jordan-label-count', $map);
        $this->assertStringContainsString('window.solentJordanGeoJsonPromise', $map);
        $this->assertStringNotContainsString('<circle', $map);
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
