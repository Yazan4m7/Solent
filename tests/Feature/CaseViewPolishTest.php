<?php

namespace Tests\Feature;

use Tests\TestCase;

class CaseViewPolishTest extends TestCase
{
    public function test_case_actions_modal_uses_a_complete_border_and_distinct_action_hovers(): void
    {
        $modal = file_get_contents(resource_path('views/components/partiels/caseActionsModal.blade.php'));
        $css = file_get_contents(public_path('assets/css/solent-demo.css'));

        $this->assertStringContainsString('solent-case-action--lock', $modal);
        $this->assertStringContainsString('solent-case-action--edit', $modal);
        $this->assertStringContainsString('solent-case-action--repeat', $modal);
        $this->assertStringContainsString('body.white-content .solent-case-actions-modal__actions .solent-case-action--lock:hover', $css);
        $this->assertStringContainsString('background: #111827 !important;', $css);
        $this->assertStringContainsString('body.white-content .solent-case-actions-modal__section {', $css);
        $this->assertDoesNotMatchRegularExpression(
            '/body\.white-content \.solent-case-actions-modal__section\s*\{[^}]*border-inline-start/s',
            $css
        );
    }

    public function test_view_case_contains_its_history_and_keeps_the_job_information_block(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/viewOnly.blade.php'));

        $this->assertStringContainsString('card solent-view-case', $view);
        $this->assertStringContainsString('solent-view-case__details-grid', $view);
        $this->assertStringContainsString('solent-view-case__timeline-scroll', $view);
        $this->assertStringContainsString('overflow-x: auto;', $view);
        $this->assertStringContainsString('min-width: 1050px;', $view);
        $this->assertStringContainsString("{{ \$ui['Case History'] ?? 'Case History' }}", $view);
        $this->assertSame(8, substr_count($view, 'class="eventTitle"'));
        $this->assertStringContainsString('$deliveryStartLog ? substr($deliveryStartLog->created_at, 0, 16) :', $view);
        $this->assertStringContainsString('optional($deliveryCompletionLog->user)->fullName()', $view);
        $this->assertStringContainsString('Job information', $view);
        $this->assertStringContainsString('class="table sunriseTable table-striped jobsTable"', $view);
        $this->assertStringContainsString('$deliveryStartLog ?', $view);
        $this->assertStringContainsString("optional(\$deliveryCompletionLog->user)->fullName() ?: '-'", $view);
        $this->assertStringNotContainsString("where('is_completion', 3)->first()->created_at", $view);
    }

    public function test_view_case_always_receives_a_jobs_collection(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Cases/Http/Controllers/CaseController.php'));
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/viewOnly.blade.php'));

        $this->assertStringContainsString('$jobs = ($stage == -2 || $stage > 5)', $controller);
        $this->assertStringContainsString("'stage', 'jobs'", $controller);
        $this->assertStringContainsString('$jobs = $jobs ?? (', $view);
    }

    public function test_requested_case_labels_have_arabic_translations(): void
    {
        $messages = (require resource_path('lang/ar/ui.php'))['dom'];

        foreach ([
            'Add Note' => 'إضافة ملاحظة',
            'Add note' => 'إضافة ملاحظة',
            'Add a note' => 'أضف ملاحظة',
            'Attachments' => 'المرفقات',
            'Attachments:' => 'المرفقات:',
            'Case History' => 'سجل الحالة',
            'Patient name' => 'اسم المريض',
            'Patient name:' => 'اسم المريض:',
            'Delivery Date' => 'تاريخ التسليم',
            'Delivery Date:' => 'تاريخ التسليم:',
            'Case ID' => 'رقم الحالة',
            'Case ID:' => 'رقم الحالة:',
            'Print Mini Label' => 'طباعة الملصق المصغر',
            'Print Label' => 'طباعة الملصق',
        ] as $english => $arabic) {
            $this->assertSame($arabic, $messages[$english], "Missing Arabic translation for {$english}");
        }
    }
}
