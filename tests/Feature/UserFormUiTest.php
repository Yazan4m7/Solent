<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserFormUiTest extends TestCase
{
    public function test_create_and_edit_user_forms_share_the_reference_ui(): void
    {
        $create = file_get_contents(base_path('app/Modules/Users/Resources/views/users/create.blade.php'));
        $edit = file_get_contents(base_path('app/Modules/Users/Resources/views/users/edit.blade.php'));

        foreach ([$create, $edit] as $form) {
            $this->assertStringContainsString("@include('users._form-styles')", $form);
            $this->assertStringContainsString("@include('users._permissions-box'", $form);
            $this->assertStringContainsString("@include('users._access-script')", $form);
            $this->assertStringContainsString('class="kt-form user-management-form"', $form);
            $this->assertStringContainsString('id="status" name="status"', $form);
            $this->assertStringContainsString('name="permission[]"', file_get_contents(base_path('app/Modules/Users/Resources/views/users/_permissions-box.blade.php')));
        }

        $this->assertStringContainsString("action=\"{{ route('new-user') }}\"", $create);
        $this->assertStringContainsString("action=\"{{ route('edit-user') }}\"", $edit);
        $this->assertStringContainsString('name="id" value="{{ $user->id }}"', $edit);
        $this->assertStringNotContainsString('<select', $edit);

        $controller = file_get_contents(base_path('app/Modules/Users/Http/Controllers/UserController.php'));
        $this->assertStringContainsString("'status' => \$request->has('status_control_present')", $controller);
        $this->assertStringContainsString("? (\$request->status == 'on' ? 1 : 0)", $controller);
    }

    public function test_permissions_panel_is_scrollable_responsive_and_uses_state_badges(): void
    {
        $styles = file_get_contents(base_path('app/Modules/Users/Resources/views/users/_form-styles.blade.php'));
        $permissions = file_get_contents(base_path('app/Modules/Users/Resources/views/users/_permissions-box.blade.php'));
        $script = file_get_contents(base_path('app/Modules/Users/Resources/views/users/_access-script.blade.php'));

        $this->assertStringContainsString('grid-template-columns: repeat(5, minmax(170px, 1fr));', $styles);
        $this->assertStringContainsString('max-height: min(420px, 55vh);', $styles);
        $this->assertStringContainsString('overflow-y: auto;', $styles);
        $this->assertStringContainsString('min-height: 44px;', $styles);
        $this->assertStringContainsString('max-width: 1320px;', $styles);
        $this->assertStringContainsString('.user-management-card__header,', $styles);
        $this->assertStringContainsString('justify-content: flex-end;', $styles);
        $this->assertStringContainsString('.user-switch__input:checked + .user-switch__track', $styles);
        $this->assertStringContainsString('permission-icon-off', $permissions);
        $this->assertStringContainsString('permission-icon-on', $permissions);
        $this->assertStringContainsString(".prop('disabled', isAdmin)", $script);
        $this->assertStringContainsString('.permission-checkbox[value="131"]', $script);
        $this->assertStringNotContainsString("visibility', 'hidden", $script);
    }

    public function test_create_user_uses_one_card_and_the_profile_picker_is_scoped(): void
    {
        $create = file_get_contents(base_path('app/Modules/Users/Resources/views/users/create.blade.php'));
        $picker = file_get_contents(resource_path('views/components/user-image-picker.blade.php'));

        $this->assertStringContainsString('class="card user-management-card"', $create);
        $this->assertStringContainsString('class="user-management-card__header"', $create);
        $this->assertStringNotContainsString('<div class="kt-portlet">', $create);
        $this->assertStringContainsString('.user-image-picker-container > .row', $picker);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1.35fr) minmax(220px, .65fr);', $picker);
        $this->assertStringNotContainsString('height: 12rem;', $picker);
        $this->assertStringNotContainsString('.input-container{', $picker);
        $this->assertStringContainsString('class="user-image-placeholder"', $picker);
        $this->assertStringNotContainsString('/assets/images/default-avatar.png', $picker);
    }

    public function test_new_user_form_labels_have_arabic_dom_translations(): void
    {
        $messages = require resource_path('lang/ar/ui.php');
        $messages = $messages['dom'];

        foreach ([
            'Admin Privileges',
            'Grant administrator access',
            'Account Status',
            'Active account',
            'Security',
            'Permissions',
            'Profile Image',
        ] as $label) {
            $this->assertArrayHasKey($label, $messages);
            $this->assertNotSame($label, $messages[$label]);
        }
    }

    public function test_edit_password_uses_the_new_password_only_after_confirmation(): void
    {
        $controller = file_get_contents(base_path('app/Modules/Users/Http/Controllers/UserController.php'));
        $rules = [
            'password' => 'nullable|confirmed|min:1|max:200|required_with:password_confirmation',
            'password_confirmation' => 'nullable|min:1|max:200|required_with:password',
        ];

        $this->assertStringContainsString("'password' => 'nullable|confirmed|min:1|max:200|required_with:password_confirmation'", $controller);
        $this->assertStringContainsString("'password_confirmation' => 'nullable|min:1|max:200|required_with:password'", $controller);
        $this->assertStringContainsString("if (\$request->filled('password'))", $controller);
        $this->assertStringContainsString("\$user->password = Hash::make(\$request->password);", $controller);
        $this->assertStringNotContainsString("\$request->get('password_confirmation')", $controller);
        $this->assertTrue(validator([], $rules)->passes());
        $this->assertFalse(validator(['password' => 'new-secret'], $rules)->passes());
        $this->assertFalse(validator(['password_confirmation' => 'new-secret'], $rules)->passes());
        $this->assertFalse(validator([
            'password' => 'new-secret',
            'password_confirmation' => 'different-secret',
        ], $rules)->passes());
        $this->assertTrue(validator([
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ], $rules)->passes());
    }
}
