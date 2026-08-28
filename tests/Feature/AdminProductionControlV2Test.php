<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminProductionControlV2Test extends TestCase
{
    public function test_operations_dashboard_uses_the_new_production_control_view(): void
    {
        $controller = file_get_contents(
            app_path('Modules/Cases/Http/Controllers/CaseController.php')
        );
        $view = file_get_contents(
            base_path('app/Modules/Cases/Resources/views/cases/production-control-v2.blade.php')
        );
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString("'cases.production-control-v2'", $controller);
        $this->assertStringContainsString("'cases.admin-dashboardv2'", $controller);
        $this->assertStringContainsString("request()->query('view') === 'classic'", $controller);
        $this->assertStringContainsString('data-pc-root', $view);
        $this->assertStringContainsString('data-pc-stage-button', $view);
        $this->assertStringContainsString('data-pc-case-list', $view);
        $this->assertStringContainsString('data-pc-detail-panel', $view);
        $this->assertStringContainsString('admin-production-control-v2.css', $view);
        $this->assertStringContainsString('adminProductionControlV2.js', $view);
        $this->assertStringContainsString("route('admin-dashboard-v2', ['view' => 'classic'])", $view);
        $this->assertStringContainsString("name('production-control.notes.store')", $routes);
        $this->assertStringContainsString('addProductionControlNote', $controller);
        $this->assertStringContainsString('EmployeeProductionBoardActionService $actions', $controller);
        $this->assertStringNotContainsString('Needs attention', $view);
        $this->assertStringNotContainsString('Priority', $view);
    }

    public function test_production_control_assets_are_mobile_first_and_use_the_shared_overlay(): void
    {
        $css = file_get_contents(public_path('assets/css/admin-production-control-v2.css'));
        $js = file_get_contents(public_path('assets/js/ysh-custom-js/adminProductionControlV2.js'));
        $details = file_get_contents(
            base_path('app/Modules/Cases/Resources/views/cases/partials/production-control-details.blade.php')
        );

        $this->assertStringContainsString('SolentProcessingOverlay.show', $js);
        $this->assertStringContainsString('window.confirm', $js);
        $this->assertStringContainsString('data-pc-processing', $details);
        $this->assertStringContainsString('data-pc-confirm', $details);
        $this->assertStringContainsString("route('production-control.notes.store'", $details);
        $this->assertStringContainsString('name="idempotency_key"', $details);
        $this->assertStringContainsString('name="note"', $details);
        $this->assertStringContainsString('data-pc-note-toggle', $details);
        $this->assertStringContainsString('data-pc-note-form', $details);
        $this->assertLessThan(
            strpos($details, 'Open case'),
            strpos($details, 'Add note')
        );
        $this->assertStringContainsString("route('assign-to-stage-employee')", $details);
        $this->assertStringContainsString("route('complete-by-admin'", $details);
        $this->assertMatchesRegularExpression(
            '/\.pc-detail-panel\s*\{[^}]*position:\s*fixed[^}]*height:\s*100dvh/is',
            $css
        );
        $this->assertMatchesRegularExpression(
            '/@media\s*\(min-width:\s*920px\)[\s\S]*\.pc-workspace\s*\{[^}]*grid-template-columns:/i',
            $css
        );
        $this->assertStringNotContainsString('overflow-x: auto', $css);
        $this->assertStringContainsString('--pc-blue-wash:', $css);
        $this->assertStringContainsString('min-height: 48px', $css);
        $this->assertStringContainsString('height: auto;', $css);
    }
}
