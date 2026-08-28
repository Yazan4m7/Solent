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

    public function test_dashboard_exposes_a_kpi_sized_sample_data_toggle(): void
    {
        config()->set('features.dashboard.sample_data', true);

        $html = $this->renderDashboard();

        $this->assertStringContainsString('data-dashboard-sample-mode="on"', $html);
        $this->assertStringContainsString('428,540', $html);
        $this->assertStringContainsString('3,721', $html);
        $this->assertStringContainsString('2,145', $html);
        $this->assertStringContainsString('solent-dash-kpi-card solent-dash-sample-card card-1', $html);
        $this->assertStringContainsString('name="sample_data"', $html);
        $this->assertStringContainsString('solent-dash-switch__track', $html);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(150px, 1fr));', file_get_contents(public_path('assets/css/elegant-dashboard.css')));
        $this->assertStringNotContainsString("['label' => 'Workload Mix'", file_get_contents(resource_path('views/dashboard.blade.php')));
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
        $this->assertStringContainsString('>Off<', preg_replace('/\s+/', '', $html));
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
        $this->assertStringContainsString('grid-template-columns: minmax(280px, 0.94fr) minmax(320px, 1.08fr) minmax(270px, 0.98fr);', $css);
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

        $this->assertStringContainsString('min-height: 72px;', $css);
        $this->assertStringContainsString('border-radius: 8px', $css);
        $this->assertStringContainsString('html[dir="ltr"] .main-panel > .content.solent-dashboard-content', $css);
        $this->assertStringContainsString('padding-top: 72px !important;', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: 36px minmax(0, 1fr);', $css);
        $this->assertStringContainsString('grid-column: 2;', $css);
        $this->assertStringNotContainsString('solent-dash-floating-search', $view);
        $this->assertStringNotContainsString('solentDashInitFloatingSearch', $view);
        $this->assertStringNotContainsString('solent-dash-metrics-guide-toggle', $view);
        $this->assertStringContainsString('height: calc(100dvh - 92px);', $css);
        $this->assertStringContainsString('grid-template-rows: 72px minmax(0, 1fr);', $css);
        $this->assertStringContainsString("asset('assets/css/elegant-dashboard.css')", $view);
        $this->assertStringNotContainsString('elegant-dashboard.css?v=', $view);
    }

    public function test_dashboard_prioritizes_map_revenue_and_modern_order_trend(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));
        $quickNavCss = file_get_contents(public_path('assets/css/solent-quick-nav.css'));
        $sidebar = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('solent-dash-hidden-section" hidden', $view);
        $this->assertStringContainsString('solent-dash-load-panel solent-dash-hidden-card', $view);
        $this->assertStringContainsString('solent-dash-activity-panel solent-dash-hidden-card', $view);
        $this->assertStringContainsString('.solent-dash .solent-dash-hidden-section,', $css);
        $this->assertStringContainsString('display: none !important;', $css);

        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('"map revenue revenue revenue"', $css);
        $this->assertStringContainsString('"delivery service orders orders"', $css);
        $this->assertStringContainsString('.solent-dash .solent-dash-panel-large {', $css);
        $this->assertStringContainsString('grid-area: revenue;', $css);
        $this->assertStringContainsString('grid-area: map;', $css);
        $this->assertStringContainsString('grid-area: delivery;', $css);
        $this->assertStringContainsString('grid-area: service;', $css);
        $this->assertStringContainsString('grid-area: orders;', $css);
        $this->assertStringContainsString('.solent-dash .solent-dash-customer-panel .solent-dash-map-stage {', $css);
        $this->assertStringContainsString('data-dashboard-delivery-card', $view);
        $this->assertStringContainsString('data-dashboard-delivery-days', $view);
        $this->assertStringNotContainsString('<button class="solent-dash-map-button"', $view);

        $this->assertStringContainsString('data-target="#solentJordanMapModal"', $view);
        $this->assertStringNotContainsString('type="button">Daily', $view);
        $this->assertStringContainsString("orderGradient.addColorStop(0, 'rgba(99, 102, 241, 0.92)')", $view);
        $this->assertStringContainsString("type: 'line'", $view);
        $this->assertStringContainsString('orderTrend: true', $view);
        $this->assertStringContainsString("hoverBackgroundColor: '#4f46e5'", $view);

        $this->assertStringContainsString('.solent-floating-topbar {', $sidebar);
        $this->assertStringContainsString('position: absolute;', $sidebar);
        $this->assertStringContainsString('top: 18px;', $sidebar);
        $this->assertMatchesRegularExpression(
            '/\.solent-quick-nav--mobile\s*\{[^}]*position:\s*relative;[^}]*z-index:\s*990;/s',
            $quickNavCss
        );
        $this->assertStringNotContainsString('position: sticky;', $quickNavCss);
    }

    public function test_case_delivery_card_flattens_today_and_tomorrow_into_case_rows(): void
    {
        $deliveryDays = collect([
            ['key' => 'today', 'label' => 'Today', 'date' => '2026-07-20', 'time' => '09:15:00', 'patient' => 'سارة أحمد'],
            ['key' => 'tomorrow', 'label' => 'Tomorrow', 'date' => '2026-07-21', 'time' => '14:30:00', 'patient' => 'ليان خالد'],
            ['key' => 'following', 'label' => 'Day after tomorrow', 'date' => '2026-07-22', 'time' => '17:45:00', 'patient' => 'عمر محمود'],
        ])->values()->map(function (array $day, int $index): array {
            $clinic = (new \App\client())->forceFill(['name' => 'طبيب ' . ($index + 1)]);
            $clinic->id = 40 + $index;
            $clinic->exists = true;

            $case = (new \App\sCase())->forceFill([
                'case_id' => 'DEMO-' . (701 + $index),
                'doctor_id' => $clinic->id,
                'patient_name' => $day['patient'],
                'initial_delivery_date' => $day['date'] . ' ' . $day['time'],
            ]);
            $case->id = 701 + $index;
            $case->exists = true;
            $case->setRelation('client', $clinic);

            return [
                'key' => $day['key'],
                'label' => $day['label'],
                'date' => $day['date'],
                'cases' => collect([$case]),
            ];
        });

        $html = $this->renderDashboard(['deliveryScheduleDays' => $deliveryDays]);
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));
        $arabic = require resource_path('lang/ar/ui.php');

        $this->assertSame(2, substr_count($html, 'class="solent-dash-delivery-case"'));
        $this->assertStringContainsString('Case delivery', $html);
        $this->assertStringContainsString('data-dashboard-delivery-cases', $html);
        $this->assertStringNotContainsString('solent-dash-delivery-day--', $html);
        $this->assertStringContainsString('طبيب 1', $html);
        $this->assertStringContainsString('طبيب 2', $html);
        $this->assertStringContainsString('سارة أحمد', $html);
        $this->assertStringContainsString('ليان خالد', $html);
        $this->assertStringNotContainsString('عمر محمود', $html);
        $this->assertStringContainsString('/view-case/701/-2', $html);
        $this->assertMatchesRegularExpression('/<time datetime="2026-07-20T09:15:00[^"]*" dir="ltr">/', $html);
        $this->assertMatchesRegularExpression('/<strong>20 [^<]+ · 09:15<\/strong>/', $html);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('overflow-y: auto;', $css);
        $this->assertStringContainsString('min-height: 44px;', $css);
        $this->assertStringContainsString('html[dir="rtl"] .solent-dash .card-1::before', $css);
        $this->assertSame('تسليم الحالات', $arabic['dom']['Case delivery']);
        $this->assertSame('وقت التسليم', $arabic['dom']['Delivery time']);
    }

    public function test_dashboard_loads_one_pending_delivery_window_for_the_next_three_days(): void
    {
        $controller = file_get_contents(app_path('Modules/Reports/Http/Controllers/ReportsController.php'));

        $this->assertStringContainsString("sCase::with(['client:id,name'])", $controller);
        $this->assertStringContainsString("->whereBetween('initial_delivery_date'", $controller);
        $this->assertStringContainsString('->addDays(2)->endOfDay()', $controller);
        $this->assertStringContainsString("->where('delivered_to_client', 0)", $controller);
        $this->assertStringContainsString("['Today', 'Tomorrow', 'Day after tomorrow']", $controller);
        $this->assertStringContainsString("'deliveryScheduleDays'", $controller);
        $this->assertSame(0, substr_count($controller, '$this->getDashboardProductionLoadRows()'));
    }

    public function test_delivery_voucher_uses_the_solent_logo(): void
    {
        $view = file_get_contents(base_path('app/Modules/Delivery/Resources/views/delivery/view-voucher.blade.php'));

        $this->assertStringContainsString("asset('images/brands/solent/solent_h.svg')", $view);
        $this->assertStringContainsString('alt="Solent Dental Laboratory Logo"', $view);
        $this->assertStringNotContainsString('assets/images/hikaro-logo.png', $view);
        $this->assertStringContainsString('max-width: 120px;', $view);
        $this->assertStringContainsString('max-width: 50px;', $view);
    }

    public function test_home_uses_top_floating_search_without_bottom_search(): void
    {
        $app = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));

        $this->assertStringNotContainsString("(\$pageSlug ?? null) !== 'Dashboard'", $app);
        $this->assertStringContainsString('solent-dashboard-content', $app);
        $this->assertStringContainsString('solent-dashboard-page', $app);
        $this->assertStringContainsString('class="solent-floating-search"', $app);
        $this->assertStringContainsString('action="{{ route(\'global-search\') }}"', $app);
        $this->assertStringNotContainsString('id="solentDashSearchInput"', $view);
        $this->assertStringNotContainsString('.solent-dash .solent-dash-floating-search:hover', $css);
        $this->assertStringNotContainsString('.solent-dash .solent-dash-floating-search:focus-within', $css);
        $this->assertStringNotContainsString('width: min(300px, calc(100vw - 36px));', $css);
        $this->assertStringContainsString('.solent-dashboard-page .footer', $css);
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

    public function test_authenticated_layout_uses_floating_search_and_sidebar_account(): void
    {
        $app = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertDoesNotMatchRegularExpression('/<div class="main-panel">\s*@include\\(\'layouts\\.navbars\\.navbar\'\\)/', $app);
        $this->assertStringContainsString('solent-floating-topbar', $app);
        $this->assertStringContainsString('solent-floating-search', $app);
        $this->assertStringContainsString('solent-mobile-sidebar-toggle', $app);
        $this->assertStringContainsString('id="overlay"', $app);
        $this->assertStringContainsString('solent-sidebar-account', $sidebar);
        $this->assertStringContainsString('solent-sidebar-profile__avatar', $sidebar);
        $this->assertStringContainsString('margin-top: auto;', $sidebar);
        $this->assertStringContainsString('Administrator', $sidebar);
        $this->assertGreaterThan(
            strpos($sidebar, '<div class="sidebar-wrapper">'),
            strpos($sidebar, '<div class="solent-sidebar-account">')
        );
    }

    public function test_floating_search_and_sidebar_are_mobile_safe(): void
    {
        $sidebar = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('.solent-mobile-sidebar-toggle {', $sidebar);
        $this->assertStringContainsString('display: none;', $sidebar);
        $this->assertStringContainsString('display: inline-flex;', $sidebar);
        $this->assertStringContainsString('width: calc(100vw - 68px);', $sidebar);
        $this->assertStringContainsString('.main-panel > .content', $sidebar);
        $this->assertStringContainsString('padding: 82px 14px 18px !important;', $sidebar);
    }

    public function test_footer_mobile_nav_toggle_is_null_safe_and_animates_the_bars(): void
    {
        $view = file_get_contents(resource_path('views/layouts/footer.blade.php'));

        $this->assertStringContainsString('if (navbarToggle) {', $view);
        $this->assertStringContainsString("navbarToggle.classList.toggle('toggled', isOpen);", $view);
        $this->assertStringContainsString("document.body.classList.toggle('no-scroll', isOpen);", $view);
        $this->assertStringContainsString("navbarToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');", $view);
        $this->assertStringContainsString('if (overlay) {', $view);
        $this->assertStringContainsString("overlay.addEventListener('click'", $view);
        $this->assertStringContainsString("sidebarClose.addEventListener('click'", $view);
        $this->assertStringContainsString("event.key === 'Escape'", $view);
    }

    public function test_sidebar_brand_header_supports_the_mobile_close_control(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('justify-content: space-between;', $view);
        $this->assertStringContainsString('margin: 0 auto;', $view);
        $this->assertStringContainsString('korvion-sidebar-logo-full', $view);
        $this->assertStringContainsString('class="solent-sidebar-close"', $view);
    }

    public function test_main_sidebar_uses_reconstructed_navigation_hierarchy(): void
    {
        $view = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('solent-sidebar-section-label', $view);
        $this->assertStringContainsString("\$ui['Overview'] ?? 'Overview'", $view);
        $this->assertStringContainsString("\$ui['Workspace'] ?? 'Workspace'", $view);
        $this->assertStringContainsString("\$ui['Management'] ?? 'Management'", $view);
        $this->assertStringContainsString('solent-sidebar-quick-action', $view);
        $this->assertStringContainsString('solent-sidebar-icon-shell', $view);
        $this->assertStringContainsString('solent-sidebar-link-copy', $view);
        $this->assertStringContainsString('solent-sidebar-close', $view);
        $this->assertStringContainsString('grid-template-columns: 34px minmax(0, 1fr) 14px;', $view);
        $this->assertStringContainsString('html[dir="rtl"] .solent-sidebar-account .dropdown-menu', $view);
        $this->assertStringContainsString('html[dir="ltr"] .solent-sidebar-account .dropdown-menu', $view);
        $this->assertStringContainsString('(min-width: 992px) and (max-height: 850px)', $view);
        $this->assertStringContainsString('(min-width: 992px) and (max-height: 700px)', $view);
        $this->assertStringContainsString('min-height: 42px;', $view);
        $this->assertStringNotContainsString('solent-sidebar-context', $view);
        $this->assertStringNotContainsString('solent-sidebar-quick-action__arrow', $view);
        $this->assertStringNotContainsString('solent-sidebar-count', $view);
        $this->assertStringContainsString('id="sidebarReports"', $view);
        $this->assertStringContainsString('id="sidebarBilling"', $view);
        $this->assertStringContainsString('id="sidebarSettings"', $view);
        $this->assertStringContainsString('<a href="{{ route(\'users-index\') }}">', $view);
        $this->assertStringContainsString("{{ $" . "ui['Users'] ?? 'Users' }}", $view);
        $this->assertStringContainsString('.overlay.active {', $view);
        $this->assertStringContainsString('width: calc(100% - 280px);', $view);
        $this->assertStringContainsString('z-index: 1030;', $view);
        $this->assertStringContainsString("$" . "reportsExpanded = in_array($" . "currentRoute, $" . "reportsRoutes, true);", $view);
        $this->assertStringNotContainsString('id="laravel-examples"', $view);
        $this->assertStringNotContainsString('id="accountancyList"', $view);
        $this->assertStringNotContainsString('id="configList"', $view);
        $this->assertStringNotContainsString('<hr', $view);
        $this->assertSame(substr_count($view, '<li'), substr_count($view, '</li>'));
        $this->assertSame(substr_count($view, '<ul'), substr_count($view, '</ul>'));

        foreach ([
            'home', 'admin-dashboard-v2', 'new-case-view', 'cases-index',
            'view-cases-monitor', 'delivery-schedule', 'clients-index',
            'num-of-units-report', 'job-types-report', 'QC-report',
            'repeats-report', 'materials-report', 'invoices-index',
            'payments-index', 'material-index', 'job-type-index',
            'users-index', 'implants-index', 'tags-index', 'f-causes-index',
        ] as $routeName) {
            $this->assertTrue(\Illuminate\Support\Facades\Route::has($routeName), "Missing sidebar route: {$routeName}");
        }
    }

    public function test_delivery_schedule_uses_reconstructed_delivery_workspace(): void
    {
        $view = file_get_contents(base_path('app/Modules/Delivery/Resources/views/delivery/delivery-schedule.blade.php'));
        $css = file_get_contents(public_path('assets/css/solent-demo.css'));

        $this->assertStringContainsString('class="delivery-page"', $view);
        $this->assertStringContainsString('class="delivery-hero"', $view);
        $this->assertStringContainsString('delivery-filter-card', $view);
        $this->assertStringContainsString('delivery-results-shell__header', $view);
        $this->assertStringContainsString('delivery-table-wrap', $view);
        $this->assertStringContainsString('delivery-row clickable', $view);
        $this->assertStringContainsString(':modalId="\'caseActionsModal\' . $case->id"', $view);
        $this->assertStringContainsString('"dom": "<\'solent-datatable-toolbar\'Bfl>rt<\'solent-datatable-foot\'ip>"', $view);
        $this->assertStringContainsString('window.solentDataTableButtons', $view);
        $this->assertStringContainsString('"paging": true', $view);
        $this->assertStringContainsString("deliveryTable.button('.buttons-print')", $view);
        $this->assertStringContainsString('class="delivery-time" dir="ltr"', $view);
        $this->assertMatchesRegularExpression(
            '/<span class="delivery-time" dir="ltr">\s*<span>\{\{ \$deliveryTime \}\}<\/span>\s*@if \(\$deliveryPeriod\)\s*<span class="delivery-time__period">\{\{ \$deliveryPeriod \}\}<\/span>/s',
            $view
        );
        $this->assertStringNotContainsString('id="actionsDialog', $view);
        $this->assertStringNotContainsString('printWindow.document.write', $view);
        $this->assertStringNotContainsString('Track due, overdue, and upcoming cases', $view);
        $this->assertStringNotContainsString('Cases ready for delivery planning', $view);
        $this->assertStringNotContainsString('Schedule list', $view);
        $this->assertStringNotContainsString('delivery-summary-card__meta', $view);
        $this->assertStringContainsString('body.white-content .delivery-hero', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(6, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('body.white-content .delivery-table', $css);
    }

    public function test_shared_footer_provides_delivery_exports_without_the_live_clock(): void
    {
        $footer = file_get_contents(resource_path('views/layouts/footer.blade.php'));

        $this->assertStringContainsString("extend: 'excelHtml5'", $footer);
        $this->assertStringContainsString("extend: 'print'", $footer);
        $this->assertStringContainsString('dataTables.buttons.min.js', $footer);
        $this->assertStringContainsString('buttons.html5.min.js', $footer);
        $this->assertStringContainsString('buttons.print.min.js', $footer);
        $this->assertStringNotContainsString('id="live-time"', $footer);
        $this->assertStringNotContainsString('toLocaleTimeString', $footer);
        $this->assertStringNotContainsString('setInterval(updateTime', $footer);
    }

    public function test_case_activity_card_enables_the_expandable_jordan_map(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));
        $css = file_get_contents(public_path('assets/css/elegant-dashboard.css'));
        $map = file_get_contents(resource_path('views/partials/jordan-dashboard-map.blade.php'));

        $this->assertStringContainsString('Case Activity by Governorate', $view);
        $this->assertStringContainsString('solent-dash-map-card-summary', $view);
        $this->assertStringContainsString("@include('partials.jordan-dashboard-map", $view);
        $this->assertStringContainsString('data-target="#solentJordanMapModal"', $view);
        $this->assertStringContainsString('solentJordanMapModal', $view);
        $this->assertStringContainsString('solentJordanGovernorateModal', $view);
        $this->assertStringContainsString('solentDashInitJordanMapDrilldown', $view);
        $this->assertStringContainsString('/gbOpen/JOR/ADM1/', $map);
        $this->assertStringContainsString('/gbOpen/JOR/ADM2/', $map);
        $this->assertStringContainsString('fill: color(area.value, focusedAreaMaximum)', $map);
        $this->assertStringContainsString('areaTooltipHandlers(element, path, area.name, area.metrics)', $map);
        $this->assertStringContainsString('solent-dash-jordan-tooltip-stats', $map);
        $this->assertStringContainsString('.solent-dash-jordan-adm2-region:hover', $map);
        $this->assertStringNotContainsString("svgElement('circle'", $map);
        $this->assertStringNotContainsString("svgElement('radialGradient'", $map);
        $this->assertStringNotContainsString('solent-dash-jordan-area-heat', $map);
        $this->assertStringNotContainsString('solent-dash-jordan-area-dot', $map);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $css);
        $this->assertStringContainsString('.solent-dash .solent-dash-customer-panel .solent-dash-map-stage', $css);
        $this->assertStringNotContainsString('.solent-dash-map-stage--disabled', $css);
        $this->assertStringNotContainsString('.solent-dash-map-coming-soon', $css);
        $this->assertStringNotContainsString('.solent-dash .solent-dash-map-button', $css);
        $this->assertStringNotContainsString('body.solent-dash-map-detail-open', $css);
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
        $operationsModal = file_get_contents(resource_path('views/components/partiels/operationsCaseSlidePanel.blade.php'));
        $caseModal = file_get_contents(resource_path('views/components/partiels/caseActionsModal.blade.php'));

        $this->assertStringContainsString('<x-partiels.operationsCaseSlidePanel', $view);
        $this->assertStringContainsString('function YSH_findCaseActionsModal(', $view);
        $this->assertStringContainsString("window.jQuery(caseActionsModal).modal('show');", $view);
        $this->assertStringContainsString('YSH_openSlidePanel({{ $case->id }}, \'{{ strtolower($key) }}\', \'dashboard\')', $view);
        $this->assertStringNotContainsString('Case Completion', $view);
        $this->assertStringNotContainsString('waitingDialog{{', $view);
        $this->assertStringNotContainsString('confirmCompletion{{', $view);
        $this->assertStringContainsString('<x-slot name="operationsActions">', $operationsModal);
        $this->assertStringContainsString("route('assign-to-me'", $operationsModal);
        $this->assertStringContainsString("route('finish-case'", $operationsModal);
        $this->assertStringContainsString("route('assign-to-stage-employee'", $view);
        $this->assertStringContainsString('@isset($operationsActions)', $caseModal);
        $this->assertStringContainsString('justify-content: space-between;', $view);
        $this->assertStringContainsString('direction: ltr;', $view);
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
        $this->assertStringContainsString('fill: none !important;', $view);
        $this->assertStringContainsString('solent-sidebar-language-menu-item', $view);
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
        $this->assertStringContainsString('paging: false', $partial);
        $this->assertStringNotContainsString("pagingType: 'full_numbers'", $partial);
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
