<?php

namespace Tests\Feature;

use Tests\TestCase;

class GlobalSearchAuditSafetyTest extends TestCase
{
    public function test_blank_global_search_returns_an_empty_result_before_querying_cases(): void
    {
        $controller = file_get_contents(app_path('Modules/Cases/Http/Controllers/CaseController.php'));

        $blankGuard = strpos($controller, "if ($" . "searchText === '')");
        $caseQuery = strpos($controller, '$cases = sCase::query();', $blankGuard ?: 0);

        $this->assertNotFalse($blankGuard);
        $this->assertNotFalse($caseQuery);
        $this->assertLessThan($caseQuery, $blankGuard);
        $this->assertStringContainsString('$cases = collect();', $controller);
        $this->assertStringContainsString("$" . "request->input('searchText', '')", $controller);
    }
}
