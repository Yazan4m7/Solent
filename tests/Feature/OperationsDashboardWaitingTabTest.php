<?php

namespace Tests\Feature;

use Tests\TestCase;

class OperationsDashboardWaitingTabTest extends TestCase
{
    public function test_an_unopened_stage_defaults_to_waiting_without_overriding_an_explicit_tab_selection(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/admin-dashboardv2.blade.php'));

        $this->assertStringContainsString("var stagePanelId = btnElement.getAttribute('aria-controls');", $view);
        $this->assertStringContainsString("stagePanel.querySelector('.innerBtn[aria-selected=\"true\"]')", $view);
        $this->assertStringContainsString("var waitingTab = stagePanel.querySelector('.innerWaitingBtn');", $view);
        $this->assertStringContainsString('waitingTab.click();', $view);
    }
}
