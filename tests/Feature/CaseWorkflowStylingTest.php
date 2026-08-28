<?php

namespace Tests\Feature;

use Tests\TestCase;

class CaseWorkflowStylingTest extends TestCase
{
    public function test_edit_case_uses_the_shared_case_workflow_visual_system(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/edit-case.blade.php'));

        $this->assertStringContainsString("asset('assets/css/solent-case-workflows.css')", $view);
        $this->assertStringContainsString('case-workflow-hero--edit', $view);
        $this->assertStringContainsString('case-workflow-form--edit', $view);
        $this->assertSame(2, substr_count($view, 'case-workflow-summary'));
        $this->assertStringContainsString('form="edit-case-note-form"', $view);
        $this->assertStringContainsString('id="edit-case-note-form"', $view);
        $this->assertStringContainsString('case-workflow-section case-workflow-section--notes', $view);
        $this->assertStringContainsString('case-workflow-section case-workflow-section--attachments', $view);
        $this->assertStringContainsString('case-note-composer', $view);
        $this->assertStringContainsString('case-attachment-actions', $view);
        $this->assertStringNotContainsString('<form  style="" class="noteform', $view);
    }

    public function test_repeat_case_uses_the_same_responsive_workflow_shell(): void
    {
        $view = file_get_contents(base_path('app/Modules/Failures/Resources/views/failures/repeat-case.blade.php'));

        $this->assertStringContainsString("asset('assets/css/solent-case-workflows.css')", $view);
        $this->assertStringContainsString('case-workflow-hero--repeat', $view);
        $this->assertStringContainsString('case-workflow-form--repeat', $view);
        $this->assertSame(2, substr_count($view, 'case-workflow-summary'));
        $this->assertStringContainsString('name="failure_cause_id"', $view);
        $this->assertStringContainsString('name="repeatedJobStage{{$job->id}}"', $view);
    }

    public function test_shared_case_workflow_styles_are_mobile_first_and_rtl_safe(): void
    {
        $css = file_get_contents(public_path('assets/css/solent-case-workflows.css'));

        $this->assertStringContainsString('border-inline-start', file_get_contents(base_path('resources/views/generic/invoices-list.blade.php')));
        $this->assertStringContainsString('margin-inline: auto;', $css);
        $this->assertStringContainsString('html[dir="rtl"] .case-workflow-hero', $css);
        $this->assertStringContainsString('@media (max-width: 767.98px)', $css);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $css);
        $this->assertStringContainsString('width: calc(100% - 16px);', $css);
        $this->assertStringContainsString('.case-workflow-form .form-control:disabled,', $css);
        $this->assertStringContainsString('.case-workflow-form .slctUnitsBtn:disabled,', $css);
        $this->assertStringContainsString('.case-workflow-form .slctUnitsBtn:disabled:hover,', $css);
        $this->assertStringContainsString('cursor: not-allowed !important;', $css);
        $this->assertStringContainsString('.case-workflow-form .case-workflow-section__body', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));', $css);
        $this->assertGreaterThan(
            strpos($css, '.case-workflow-form .slctUnitsBtn:hover,'),
            strpos($css, '.case-workflow-form .slctUnitsBtn:disabled,')
        );
    }

    public function test_invoice_filters_and_results_are_two_themed_bordered_cards(): void
    {
        $view = file_get_contents(base_path('resources/views/generic/invoices-list.blade.php'));

        $this->assertStringContainsString('invoice-panel invoice-panel--filters', $view);
        $this->assertStringContainsString('invoice-panel invoice-panel--results', $view);
        $this->assertStringContainsString('background: var(--color-card, #ffffff) !important;', $view);
        $this->assertStringContainsString('border-inline-start: 4px solid var(--invoice-accent);', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr));', $view);
        $this->assertStringContainsString('@media (max-width: 575.98px)', $view);
    }
}
