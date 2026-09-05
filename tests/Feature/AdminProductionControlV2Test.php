<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminProductionControlV2Test extends TestCase
{
    public function test_both_designs_render_the_same_two_way_view_toggle(): void
    {
        foreach (['new' => 'production-control-v2', 'classic' => 'admin-dashboardv2'] as $selected => $view) {
            $source = file_get_contents(base_path("app/Modules/Cases/Resources/views/cases/{$view}.blade.php"));
            $this->assertStringContainsString("@include('cases.partials.operations-view-toggle', ['operationsView' => '{$selected}'])", $source);
            $html = view('cases.partials.operations-view-toggle', ['operationsView' => $selected])->render();
            $this->assertStringContainsString('view=new', $html);
            $this->assertStringContainsString('view=classic', $html);
            $this->assertMatchesRegularExpression('/view=' . $selected . '"\s+aria-current="page"/', $html);
            $this->assertSame(1, preg_match_all('/<a\b[^>]*aria-current="page"/', $html));
        }
    }

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
        $this->assertStringContainsString('pc-page-header__icon', $view);
        $this->assertStringContainsString('pc-stage-card__icon', $view);
        $this->assertStringContainsString("'icon' =>", $view);
        $this->assertStringContainsString('admin-production-control-v2.css', $view);
        $this->assertStringContainsString('adminProductionControlV2.js', $view);
        $this->assertStringContainsString("@include('cases.partials.operations-view-toggle', ['operationsView' => 'new'])", $view);
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
        $view = file_get_contents(
            base_path('app/Modules/Cases/Resources/views/cases/production-control-v2.blade.php')
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
        $this->assertMatchesRegularExpression(
            '/\.pc-stage-strip\s*\{[^}]*overflow-x:\s*auto/is',
            $css
        );
        $this->assertMatchesRegularExpression(
            '/(?:body\.production-control-v2-page|\.pc-board)\s*\{[^}]*overflow-x:\s*(?:hidden|clip)/is',
            $css
        );
        $this->assertStringContainsString('var(--accent)', $css);
        $this->assertStringContainsString('var(--surface)', $css);
        $this->assertStringContainsString('min-height: 44px', $css);
        $this->assertMatchesRegularExpression(
            '/\.pc-detail-action-zone\s*\{[^}]*position:\s*sticky[^}]*bottom:\s*0/is',
            $css
        );
        $this->assertMatchesRegularExpression(
            '/<div class="pc-detail-body">[\s\S]*?<\/div>\s*<section class="pc-detail-action-zone"/',
            $details
        );
        $this->assertStringContainsString('aria-hidden="true"', $view);
        $this->assertStringContainsString('aria-modal', $view . $js);
        $this->assertStringContainsString("detailPanel.setAttribute('aria-hidden'", $js);
        $this->assertStringContainsString("querySelector('[data-pc-detail-close]')", $js);
    }

    public function test_work_state_uses_the_legacy_operational_flags_and_employee_visibility(): void
    {
        $controller = file_get_contents(
            app_path('Modules/Cases/Http/Controllers/CaseController.php')
        );
        $card = file_get_contents(
            base_path('app/Modules/Cases/Resources/views/cases/partials/production-control-case-card.blade.php')
        );

        $this->assertStringContainsString('$splitStageCases', $controller);
        $this->assertStringContainsString("in_array(\$stage, [2, 3], true)", $controller);
        $this->assertStringContainsString('(int) $job->is_set === 1', $controller);
        $this->assertStringContainsString('(int) $job->is_active === 1', $controller);
        $this->assertStringContainsString("\$stageJobs->whereNull('assignee')->values()", $controller);
        $this->assertStringNotContainsString('With the new logic, there are no waiting cases for non-admins.', $controller);
        $this->assertStringContainsString("'stageJobs' => \$cardStageJobs", $card);
        $this->assertStringNotContainsString("'stageJobs' => \$stageJobs", $card);
    }

    public function test_ready_and_in_progress_are_a_toggle_in_the_merged_control_row(): void
    {
        $view = file_get_contents(
            base_path('app/Modules/Cases/Resources/views/cases/production-control-v2.blade.php')
        );
        $css = file_get_contents(public_path('assets/css/admin-production-control-v2.css'));
        $js = file_get_contents(public_path('assets/js/ysh-custom-js/adminProductionControlV2.js'));

        $this->assertStringContainsString('class="pc-control-deck"', $view);
        $this->assertStringContainsString('data-pc-status-toggle="waiting"', $view);
        $this->assertStringContainsString('data-pc-status-toggle="active"', $view);
        $this->assertStringContainsString('data-pc-due-toggle', $view);
        $this->assertStringContainsString('aria-pressed="false"', $view);
        $this->assertStringNotContainsString('data-pc-status-filter', $view);
        $this->assertStringNotContainsString('data-pc-assignment-filter', $view);
        $this->assertStringNotContainsString('data-pc-due-filter', $view);
        $this->assertStringNotContainsString('pc-summary-grid', $view);
        $this->assertStringNotContainsString('data-pc-summary="total"', $view);
        $this->assertStringContainsString('.pc-status-toggle__button', $css);
        $this->assertStringContainsString('.pc-due-toggle', $css);
        $this->assertStringContainsString('.pc-search', $css);
        $this->assertStringContainsString("var selectedStatus = root.getAttribute('data-pc-selected-status')", $js);
        $this->assertStringContainsString("var dueToggle = root.querySelector('[data-pc-due-toggle]')", $js);
        $this->assertStringNotContainsString('assignmentFilter', $js);
        $this->assertStringNotContainsString('dueFilter', $js);
    }
}
