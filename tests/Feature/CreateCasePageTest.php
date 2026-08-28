<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class CreateCasePageTest extends TestCase
{
    public function test_units_dialog_uses_centered_soft_white_shell_styles(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/create.blade.php'));

        $this->assertStringContainsString('min-height: calc(100% - 1rem);', $view);
        $this->assertStringContainsString('display: flex;', $view);
        $this->assertStringContainsString('align-items: center;', $view);
        $this->assertStringContainsString('linear-gradient(145deg, #ffffff 0%, #fbfcff 100%)', $view);
    }

    public function test_create_case_page_uses_mobile_first_workflow_without_changing_form_contracts(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/create.blade.php'));

        $this->assertStringContainsString('class="cc-workspace"', $view);
        $this->assertStringContainsString('class="cc-primary-column"', $view);
        $this->assertStringContainsString('class="cc-secondary-column"', $view);
        $this->assertStringContainsString('Patient and delivery details', $view);
        $this->assertStringContainsString('Laboratory jobs', $view);
        $this->assertStringContainsString('name="patient_name"', $view);
        $this->assertStringContainsString('name="doctor"', $view);
        $this->assertStringContainsString('name="delivery_date"', $view);
        $this->assertStringContainsString('data-repeater-list="repeat"', $view);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $view);
        $this->assertStringContainsString('white-space: nowrap !important;', $view);
        $this->assertStringContainsString('Create case', $view);
        $this->assertStringNotContainsString('>Final step<', $view);
        $this->assertStringNotContainsString('Ready to create the case?', $view);
        $this->assertStringNotContainsString('cc-submit-card', $view);
        $this->assertStringContainsString('class="cc-page-actions"', $view);
        $this->assertLessThan(strpos($view, 'class="cc-workspace"'), strpos($view, 'class="cc-page-actions"'));
        $this->assertStringContainsString('class="toggle-container cc-style-toggle"', $view);
        $this->assertStringContainsString('class="cc-style-value" name="style" value="Single"', $view);
        $this->assertStringContainsString("this.checked ? 'Bridge' : 'Single'", $view);
        $this->assertStringContainsString('width: min(136px, 100%);', $view);
        $this->assertStringContainsString('height: 44px;', $view);
        $this->assertStringContainsString('font-size: 13px;', $view);
        $this->assertStringContainsString('inset-inline-end: 10px;', $view);
        $this->assertStringContainsString('font-size: 2em;', $view);
    }

    public function test_create_case_page_renders_for_admin_user(): void
    {
        $user = new User([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'username' => 'test-admin',
            'email' => 'test-admin@example.com',
            'password' => 'unused',
            'is_admin' => 1,
        ]);
        $user->id = 1;
        $user->exists = true;

        $response = $this->actingAs($user)->get('/new-case');

        $response->assertOk();
        $response->assertSee('Amount (JOD)');
        $response->assertSee('Select units');
        $response->assertDontSee('@selected', false);
    }

    public function test_tooth_selection_updates_job_type_and_materials_with_row_scoped_selectors(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/create.blade.php'));

        $this->assertStringContainsString('function getJobBlock(element)', $view);
        $this->assertStringContainsString('currentJobBlock = getJobBlock(element);', $view);
        $this->assertStringContainsString('function getSelectedUnitsForJob(row)', $view);
        $this->assertStringContainsString("getSelectInJob(jobRow, 'jobType')", $view);
        $this->assertStringContainsString("getSelectInJob(jobRow, 'material_id')", $view);
        $this->assertStringContainsString('var jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 1);', $view);
        $this->assertStringContainsString('replaceSelectOptions(jobTypeBox, jawOnlyTypes, \'id\', \'name\');', $view);
        $this->assertStringContainsString('const jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 0);', $view);
        $this->assertStringContainsString('jobTypeChanged(jobTypeBox);', $view);
        $this->assertStringContainsString('var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId);', $view);
        $this->assertStringContainsString('materialBox.append(options);', $view);
        $this->assertStringNotContainsString('element.name.substr', $view);
        $this->assertStringNotContainsString('var repeaterName', $view);
    }

    public function test_attachment_previews_use_a_mobile_safe_grid_and_contained_remove_button(): void
    {
        $view = file_get_contents(base_path('app/Modules/Cases/Resources/views/cases/create.blade.php'));

        $this->assertStringContainsString('id="file-preview-list" class="cc-file-preview__list"', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(150px, 100%), 1fr));', $view);
        $this->assertStringContainsString('class="cc-file-preview__entry"', $view);
        $this->assertStringContainsString('padding-inline-end: 32px;', $view);
        $this->assertStringContainsString('inset-inline-end: 7px;', $view);
        $this->assertStringContainsString('text-overflow: ellipsis;', $view);
        $this->assertStringNotContainsString('class="col-md-3 col-sm-4 mb-3"', $view);
    }
}
