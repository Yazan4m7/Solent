<?php

namespace Tests\Feature;

use Tests\TestCase;

class MaterialFormUiTest extends TestCase
{
    public function test_create_and_edit_material_forms_share_the_sigma_inspired_ui(): void
    {
        $create = file_get_contents(base_path('app/Modules/Materials/Resources/views/create.blade.php'));
        $edit = file_get_contents(base_path('app/Modules/Materials/Resources/views/edit.blade.php'));

        foreach ([$create, $edit] as $form) {
            $this->assertStringContainsString("@include('materials::_form-styles')", $form);
            $this->assertStringContainsString("@include('materials::_form-script')", $form);
            $this->assertStringContainsString('class="solent-material-form"', $form);
            $this->assertStringContainsString('Material Information', $form);
            $this->assertStringContainsString('Workflow Configuration', $form);
            $this->assertStringContainsString('name="mat_name"', $form);
            $this->assertStringContainsString('name="price"', $form);
            $this->assertStringContainsString('name="jobTypes[]"', $form);
            $this->assertStringContainsString('name="count_as_unit"', $form);
            $this->assertStringContainsString('name="manufacturing"', $form);
            $this->assertStringContainsString('name="furnace"', $form);
            $this->assertStringContainsString("9 => 'Metal Work'", $form);
            $this->assertStringNotContainsString('name="material_milling_mode"', $form);
            $this->assertStringNotContainsString('name="materialTypes[]"', $form);
            $this->assertStringNotContainsString('name="default_type_id"', $form);
        }

        $this->assertStringContainsString("action=\"{{ route('material-add-post') }}\"", $create);
        $this->assertStringContainsString("action=\"{{ route('edit-material') }}\"", $edit);
        $this->assertStringContainsString('name="mat_id" value="{{ $material->id }}"', $edit);
    }

    public function test_material_form_is_mobile_first_and_uses_sigma_style_controls(): void
    {
        $styles = file_get_contents(base_path('app/Modules/Materials/Resources/views/_form-styles.blade.php'));
        $script = file_get_contents(base_path('app/Modules/Materials/Resources/views/_form-script.blade.php'));

        $this->assertStringContainsString('--material-accent: #0f766e;', $styles);
        $this->assertStringContainsString('grid-template-columns: minmax(190px, 1fr)', $styles);
        $this->assertStringContainsString('@media (max-width: 767.98px)', $styles);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $styles);
        $this->assertStringContainsString('min-height: 44px;', $styles);
        $this->assertStringContainsString('.material-toggle input:checked + .material-toggle-track', $styles);
        $this->assertStringContainsString('.material-choice-control input:checked + .material-choice-indicator', $styles);
        $this->assertStringContainsString("\$jobTypes.selectpicker('refresh')", $script);
    }

    public function test_material_controller_validates_the_fields_presented_by_both_forms(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Materials/Http/Controllers/MaterialController.php'));

        $this->assertStringContainsString("'mat_name' => 'required|max:30'", $controller);
        $this->assertStringContainsString("'price' => 'required|numeric'", $controller);
        $this->assertStringContainsString("'jobTypes' => 'required|array|min:1'", $controller);
        $this->assertStringNotContainsString("'name'  => 'mat_name|max:30'", $controller);
    }
}
