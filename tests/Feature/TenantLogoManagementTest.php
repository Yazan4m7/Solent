<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TenantLogoManagementTest extends TestCase
{
    public function test_platform_admin_registers_tenant_logo_routes(): void
    {
        $this->assertTrue(Route::has('system.tenants.logo.edit'));
        $this->assertTrue(Route::has('system.tenants.logo.update'));

        $edit = Route::getRoutes()->getByName('system.tenants.logo.edit');
        $update = Route::getRoutes()->getByName('system.tenants.logo.update');

        $this->assertSame('admin.solentjo.com', $edit->getDomain());
        $this->assertSame('system/tenants/{tenant}/logo', $edit->uri());
        $this->assertContains('GET', $edit->methods());
        $this->assertContains('PUT', $update->methods());
    }

    public function test_tenant_logo_editor_stores_login_and_sidebar_logos_independently(): void
    {
        $controller = file_get_contents(app_path('Modules/System/Http/Controllers/TenantController.php'));

        $this->assertStringContainsString("'login_logo' => $" . "logoRules", $controller);
        $this->assertStringContainsString("'sidebar_logo' => $" . "logoRules", $controller);
        $this->assertStringContainsString("'mimetypes:image/png,image/jpeg,image/webp'", $controller);
        $this->assertStringContainsString("'max:5120'", $controller);
        $this->assertStringContainsString('TenantDatabaseManager $databases', $controller);
        $this->assertStringContainsString("->table('brand_settings')->updateOrInsert(", $controller);
        $this->assertStringContainsString("$" . "extra['login_logo_path'] = $" . "relativePaths['login_logo']", $controller);
        $this->assertStringContainsString("$" . "extra['sidebar_mark_path'] = $" . "relativePaths['sidebar_logo']", $controller);
        $this->assertStringContainsString("'logo_path' => $" . "relativePaths['login_logo']", $controller);
        $this->assertStringContainsString("Cache::forget('branding:' . $" . "brandingKey)", $controller);
        $this->assertStringContainsString("'step' => 'branding_logos'", $controller);
    }

    public function test_tenant_pages_expose_a_mobile_safe_logo_editor(): void
    {
        $index = file_get_contents(resource_path('views/system/tenants/index.blade.php'));
        $show = file_get_contents(resource_path('views/system/tenants/show.blade.php'));
        $editor = file_get_contents(resource_path('views/system/tenants/logo.blade.php'));

        $this->assertStringContainsString('Edit logos', $index);
        $this->assertStringContainsString("route('system.tenants.logo.edit', $" . "tenant)", $index);
        $this->assertStringContainsString('tenant-logo-cell__fallback', $index);
        $this->assertStringContainsString('{{ $tenant->name }}', $index);
        $this->assertStringContainsString('Edit logos', $show);
        $this->assertStringContainsString('enctype="multipart/form-data"', $editor);
        $this->assertStringContainsString('name="login_logo"', $editor);
        $this->assertStringContainsString('name="sidebar_logo"', $editor);
        $this->assertSame(2, substr_count($editor, 'required data-logo-input'));
        $this->assertStringContainsString('data-logo-preview-image', $editor);
        $this->assertStringContainsString('data-logo-fallback', $editor);
        $this->assertStringContainsString('Both files are required when saving.', $editor);
        $this->assertStringContainsString('accept="image/png,image/jpeg,image/webp"', $editor);
        $this->assertStringContainsString('@media (max-width: 680px)', $editor);
        $this->assertStringContainsString('min-height: 46px;', $editor);
        $this->assertStringContainsString('formplugins/cropperjs/cropper.css', $editor);
        $this->assertStringContainsString('formplugins/cropperjs/cropper.js', $editor);
        $this->assertSame(2, substr_count($editor, '<button type="button" class="tenant-logo-crop-button" data-logo-adjust-crop>'));
        $this->assertStringContainsString('aspectRatio: NaN', $editor);
        $this->assertStringContainsString('getCroppedCanvas', $editor);
        $this->assertStringContainsString('maxWidth: 4096', $editor);
        $this->assertStringContainsString('new DataTransfer()', $editor);
        $this->assertStringContainsString('The selected crop must be at least 64 × 64 pixels.', $editor);
    }

    public function test_login_and_sidebar_use_separate_paths_with_the_supplied_icon_fallback(): void
    {
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
        $login = file_get_contents(resource_path('views/auth/login.blade.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertSame(
            'images/brands/solent/solent_ui_icon.png',
            config('branding.defaults.login_logo_path')
        );
        $this->assertSame(
            'images/brands/solent/solent_ui_icon.png',
            config('branding.defaults.sidebar_mark_path')
        );
        $this->assertFileExists(public_path('images/brands/solent/solent_ui_icon.png'));
        $this->assertStringContainsString("'brandingLoginLogoPath'", $provider);
        $this->assertStringNotContainsString("$" . "settings->extra['login_logo_path']\n                ?? $" . "settings->extra['sidebar_mark_path']", $provider);
        $this->assertStringContainsString('$brandingLoginLogoPath', $login);
        $this->assertStringContainsString('$brandingSidebarMarkPath', $sidebar);
        $this->assertStringContainsString('filter: brightness(4);', $login);
        $this->assertStringContainsString('filter: brightness(4);', $sidebar);
        $this->assertStringContainsString('onerror="this.onerror=null;', $login);
        $this->assertStringContainsString('onerror="this.onerror=null;', $sidebar);
    }
}
