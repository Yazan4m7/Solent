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

        $this->assertStringContainsString('$paymentsTotal = $payments->sum(\'amount\');', $controller);
        $this->assertStringContainsString("compact('payments','paymentsTotal'", $controller);
        $this->assertStringContainsString('number_format($paymentsTotal, 2)', $view);
        $this->assertStringNotContainsString('{{number_format($payments->sum', $view);
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
        $this->assertStringNotContainsString('anime3.png', $view);
        $this->assertLessThan(
            strpos($view, 'solent-layout-profile-shell'),
            strpos($view, 'headerSearchCol')
        );
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
}
