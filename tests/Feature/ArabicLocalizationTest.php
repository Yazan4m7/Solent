<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArabicLocalizationTest extends TestCase
{
    public function test_arabic_is_the_default_web_locale(): void
    {
        config()->set('localization.default', 'ar');
        $request = Request::create('/cases', 'GET');

        $response = (new SetLocale())->handle($request, function () {
            return new Response(App::getLocale());
        });

        $this->assertSame('ar', $response->getContent());
    }

    public function test_locale_cookie_switches_web_ui_to_english(): void
    {
        $request = Request::create('/cases', 'GET');
        $request->cookies->set('ui_locale', 'en');

        $response = (new SetLocale())->handle($request, function () {
            return new Response(App::getLocale());
        });

        $this->assertSame('en', $response->getContent());
    }

    public function test_query_switch_is_persisted_and_removed_from_the_url(): void
    {
        $request = Request::create('/cases?status=active&ui_locale=en', 'GET');

        $response = (new SetLocale())->handle($request, function () {
            return new Response('unreachable');
        });

        $this->assertTrue($response->isRedirect('http://localhost/cases?status=active'));
        $this->assertSame('en', $response->headers->getCookies()[0]->getValue());
        $this->assertSame('ui_locale', $response->headers->getCookies()[0]->getName());
    }

    public function test_api_requests_keep_the_stable_english_locale(): void
    {
        $request = Request::create('/api/cases', 'GET', [], ['ui_locale' => 'ar']);

        $response = (new SetLocale())->handle($request, function () {
            return new Response(App::getLocale());
        });

        $this->assertSame('en', $response->getContent());
    }

    public function test_requested_ui_words_are_arabic_but_technical_terms_remain_english(): void
    {
        $arabic = require resource_path('lang/ar/ui.php');

        $this->assertSame('قيد الانتظار', $arabic['dom']['Waiting']);
        $this->assertSame('الحالات', $arabic['dom']['Cases']);
        $this->assertSame('إسناد إليّ', $arabic['dom']['Assign to me']);
        $this->assertSame('مستحقة غداً', $arabic['dom']['Due tomorrow']);
        $this->assertSame('طباعة الجدول', $arabic['dom']['Print schedule']);
        $this->assertSame('المستخدمون', $arabic['dom']['Users']);
        $this->assertSame('قريباً', $arabic['dom']['Coming soon']);

        foreach (['Materials', 'Material', 'Job Types', 'Job Type', 'Job', 'Design'] as $term) {
            $this->assertArrayNotHasKey($term, $arabic['dom']);
        }
    }

    public function test_shared_shell_declares_dynamic_direction_and_rtl_sidebar_rules(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $sidebar = file_get_contents(resource_path('views/layouts/navbars/leftsidebar.blade.php'));

        $this->assertStringContainsString('dir="{{ trans(\'ui.direction\') }}"', $layout);
        $this->assertStringContainsString('html[dir="rtl"] .sidebar', $sidebar);
        $this->assertStringContainsString('right: 0 !important;', $sidebar);
        $this->assertStringContainsString('transform: translate3d(290px, 0, 0)', $sidebar);
        $this->assertStringContainsString('html[dir="rtl"].nav-open .sidebar', $sidebar);
        $this->assertStringNotContainsString('<x-language-switcher', $layout);
        $this->assertStringContainsString('solent-sidebar-language-menu-item', $sidebar);
        $this->assertStringContainsString('<x-language-switcher class="solent-sidebar-language"', $sidebar);
        $this->assertLessThan(
            strpos($sidebar, "route('logout')"),
            strpos($sidebar, '<x-language-switcher class="solent-sidebar-language"')
        );
    }

    public function test_flash_alerts_and_client_actions_have_arabic_translations(): void
    {
        $arabic = require resource_path('lang/ar/ui.php');
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $clients = file_get_contents(base_path('app/Modules/Clients/Resources/views/clients/index.blade.php'));

        $this->assertSame('تم إسناد الحالة إليك!', $arabic['dom']['Case has been assigned to you!']);
        $this->assertSame('حساب الطبيب', $arabic['dom']['Doctor account']);
        $this->assertStringContainsString('$ui[$flashSuccess]', $layout);
        $this->assertStringContainsString('$clientsUi[\'Account statement\']', $clients);
    }

    public function test_reports_finance_and_user_management_terms_have_arabic_translations(): void
    {
        $arabic = require resource_path('lang/ar/ui.php');
        $messages = $arabic['dom'];
        $reportUi = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/partials/report-ui.blade.php'));
        $materialsReport = file_get_contents(base_path('app/Modules/Reports/Resources/views/reports/case-materials-report.blade.php'));
        $createUser = file_get_contents(base_path('app/Modules/Users/Resources/views/users/create.blade.php'));
        $editUser = file_get_contents(base_path('app/Modules/Users/Resources/views/users/edit.blade.php'));
        $createMaterial = file_get_contents(base_path('app/Modules/Materials/Resources/views/create.blade.php'));
        $editMaterial = file_get_contents(base_path('app/Modules/Materials/Resources/views/edit.blade.php'));

        foreach ([
            'From:' => 'من:',
            'Total Amount' => 'إجمالي المبلغ',
            'Total Amount:' => 'إجمالي المبلغ:',
            'Date Range:' => 'نطاق التاريخ:',
            'Total units:' => 'إجمالي الوحدات:',
            'Percentage' => 'النسبة المئوية',
            'Search report...' => 'ابحث في التقرير...',
            'Job Mix' => 'مزيج الأعمال',
            'Materials Usage' => 'استخدام المواد',
            'Delivered on' => 'تاريخ التسليم',
            'Type' => 'النوع',
            'Received internally by' => 'استُلم داخلياً بواسطة',
            'Collector' => 'المُحصّل',
            'Paid on' => 'تاريخ الدفع',
            'Price' => 'السعر',
            'Price:' => 'السعر:',
            'Teeth or jaw' => 'الأسنان أو الفك',
            'Employees and their details.' => 'الموظفون وتفاصيلهم.',
            'Show:' => 'عرض:',
            'Add User' => 'إضافة مستخدم',
            'Confirm Password' => 'تأكيد كلمة المرور',
            'Enter the first name' => 'أدخل الاسم الأول',
            'Enter the last name' => 'أدخل اسم العائلة',
            'Reset' => 'إعادة ضبط',
        ] as $english => $translation) {
            $this->assertSame($translation, $messages[$english], "Missing Arabic translation for {$english}");
        }

        $this->assertStringContainsString("searchPlaceholder: @json(trans('ui.dom')['Search report...']", $reportUi);
        $this->assertStringContainsString("searchPlaceholder: @json(trans('ui.dom')['Search report...']", $materialsReport);
        $this->assertStringContainsString('name="last_name" placeholder="Enter the last name"', $createUser);
        $this->assertStringContainsString('name="last_name" placeholder="Enter the last name"', $editUser);
        $this->assertStringContainsString("placeholder=\"{{ trans('ui.dom')['Price'] ?? 'Price' }}", $createMaterial);
        $this->assertStringContainsString("placeholder=\"{{ trans('ui.dom')['Price'] ?? 'Price' }}", $editMaterial);
    }

    public function test_all_layout_paths_load_the_shared_cairo_typography(): void
    {
        $typography = file_get_contents(public_path('assets/css/site-typography.css'));
        $appLayout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $legacyHead = file_get_contents(resource_path('views/layout/partials/head.blade.php'));
        $portal = file_get_contents(resource_path('views/layouts/portal.blade.php'));
        $login = file_get_contents(resource_path('views/auth/login.blade.php'));
        $i18nAssets = file_get_contents(resource_path('views/components/i18n-assets.blade.php'));
        $documentation = file_get_contents(base_path('app/Modules/Documentation/Resources/views/documentation/features.blade.php'));

        $this->assertStringContainsString('--solent-font-family: "Cairo", Arial, sans-serif;', $typography);
        $this->assertStringContainsString('body button,', $typography);
        $this->assertStringContainsString('body input,', $typography);
        $this->assertStringNotContainsString('body *', $typography);

        foreach ([$appLayout, $legacyHead, $portal, $login, $i18nAssets] as $layout) {
            $this->assertStringContainsString('assets/css/site-typography.css', $layout);
        }

        $this->assertStringContainsString("font-family: 'Cairo', 'DejaVu Sans', sans-serif;", $documentation);
        $this->assertStringNotContainsString("font-family: 'Segoe UI'", $documentation);
    }
}
