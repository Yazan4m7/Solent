<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class EmployeeProductionBoardV2Test extends TestCase
{
    public function test_legacy_employee_mutations_and_view_remain_available_unchanged(): void
    {
        $assign = $this->namedRoute('assign-to-me');
        $complete = $this->namedRoute('finish-case');
        $note = $this->namedRoute('new-note');

        $this->assertSame(['GET', 'HEAD'], $assign->methods());
        $this->assertSame('assign-case/{caseId}/{stage}', $assign->uri());
        $this->assertStringEndsWith('CaseController@assignToMe', $assign->getActionName());

        $this->assertSame(['GET', 'HEAD'], $complete->methods());
        $this->assertSame('finish-case/{caseId}/{stage}', $complete->uri());
        $this->assertStringEndsWith('CaseController@finishCaseStage', $complete->getActionName());

        $this->assertSame(['POST'], $note->methods());
        $this->assertSame('case/note', $note->uri());
        $this->assertStringEndsWith('CaseController@addNote', $note->getActionName());

        $legacyView = $this->source(resource_path('views/generic/emp-cases.blade.php'));

        $this->assertStringContainsString("route('assign-to-me'", $legacyView);
        $this->assertStringContainsString("route('finish-case'", $legacyView);
        $this->assertMatchesRegularExpression(
            '/route\(\'finish-case\'.{0,180}method="GET"/s',
            $legacyView
        );
        $this->assertStringNotContainsString('data-epb-root', $legacyView);
    }

    public function test_feature_flag_and_dispatcher_own_all_existing_employee_stage_pages(): void
    {
        $flags = config('features.flags.default', []);

        $this->assertArrayHasKey('employee-production-board-v2', $flags);

        $stageRouteNames = [
            'designer-cases-list',
            'Miller-cases-list',
            'Print3D-cases-list',
            'SinterFurnace-cases-list',
            'PressFurnace-cases-list',
            'Finishing-cases-list',
            'QC-cases-list',
            'Delivery-cases-list',
        ];

        foreach ($stageRouteNames as $routeName) {
            $route = $this->namedRoute($routeName);

            $this->assertSame(['GET', 'HEAD'], $route->methods(), $routeName);
            $this->assertStringContainsString(
                'EmployeeProductionBoardController@',
                $route->getActionName(),
                $routeName . ' must pass through the feature-aware dispatcher.'
            );
        }

        $controller = $this->source(
            app_path('Modules/Cases/Http/Controllers/EmployeeProductionBoardController.php')
        );

        $this->assertStringContainsString('employee-production-board-v2', $controller);
        $this->assertMatchesRegularExpression(
            '/(?:Feature::(?:enabled|active|isEnabled)|\$this->features->enabled)\s*\(/',
            $controller
        );
        $this->assertStringContainsString('generic.emp-cases-v2', $controller);
        $this->assertTrue(
            str_contains($controller, 'employeeDashboard(')
                || str_contains($controller, "view('generic.emp-cases'")
                || str_contains($controller, "view(\"generic.emp-cases\""),
            'The disabled flag must retain a route to the legacy employee dashboard.'
        );
    }

    public function test_v2_details_and_mutations_are_separate_named_routes_with_post_only_writes(): void
    {
        $details = $this->namedRoute('employee-production-board-v2.details');
        $start = $this->namedRoute('employee-production-board-v2.start');
        $complete = $this->namedRoute('employee-production-board-v2.complete');
        $note = $this->namedRoute('employee-production-board-v2.notes.store');

        $this->assertSame(['GET', 'HEAD'], $details->methods());

        foreach ([$start, $complete, $note] as $route) {
            $this->assertSame(['POST'], $route->methods(), $route->getName());
            $this->assertStringContainsString('production-board', $route->uri());
            $this->assertStringContainsString('{caseId}', $route->uri());
            $this->assertStringContainsString('{stage}', $route->uri());
            $this->assertStringContainsString(
                'EmployeeProductionBoardController@',
                $route->getActionName()
            );
        }

        $this->assertNotSame($assignUri = $this->namedRoute('assign-to-me')->uri(), $start->uri());
        $this->assertNotSame($completeUri = $this->namedRoute('finish-case')->uri(), $complete->uri());
        $this->assertNotSame($this->namedRoute('new-note')->uri(), $note->uri());
        $this->assertSame('assign-case/{caseId}/{stage}', $assignUri);
        $this->assertSame('finish-case/{caseId}/{stage}', $completeUri);
    }

    public function test_v2_backend_guards_concurrent_and_retried_mutations(): void
    {
        $controller = $this->source(
            app_path('Modules/Cases/Http/Controllers/EmployeeProductionBoardController.php')
        );
        $service = $this->source(
            app_path('Modules/Cases/Services/EmployeeProductionBoardActionService.php')
        );
        $exception = $this->source(
            app_path('Modules/Cases/Exceptions/EmployeeProductionBoardException.php')
        );
        $stageMap = $this->source(
            app_path('Modules/Cases/Support/ProductionStageMap.php')
        );
        $idempotencyModel = $this->source(app_path('Models/EmployeeBoardActionRequest.php'));
        $idempotencyMigration = $this->source(
            database_path('migrations/2026_08_23_000001_create_employee_board_action_requests_table.php')
        );
        $backend = implode("\n", [
            $controller,
            $service,
            $exception,
            $stageMap,
            $idempotencyModel,
            $idempotencyMigration,
        ]);

        $this->assertMatchesRegularExpression('/function\s+start\s*\(/', $service);
        $this->assertMatchesRegularExpression('/function\s+complete\s*\(/', $service);
        $this->assertMatchesRegularExpression('/function\s+addNote\s*\(/', $service);
        $this->assertMatchesRegularExpression('/DB::transaction\s*\(/', $service);
        $this->assertStringContainsString('lockForUpdate(', $service);

        $this->assertStringContainsString('Idempotency-Key', $controller);
        $this->assertMatchesRegularExpression('/idempotenc(?:y|t)/i', $service);
        $this->assertMatchesRegularExpression('/request_key|requestKey/', $backend);
        $this->assertMatchesRegularExpression(
            '/request_key[^;\n]{0,100}->unique\s*\(/i',
            $idempotencyMigration
        );

        $this->assertMatchesRegularExpression('/\b409\b/', $backend);
        $this->assertMatchesRegularExpression('/\b422\b/', $backend);
        $this->assertStringContainsString('trim(', $controller);
        $this->assertMatchesRegularExpression('/Str::isUuid\s*\(|isUuid\s*\(/', $backend);
        $this->assertMatchesRegularExpression('/max(?::|\s*=>\s*)255|mb_strlen\s*\([^)]*\)\s*>\s*255/', $backend);
        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $stage) {
            $this->assertMatchesRegularExpression('/\b' . $stage . '\b/', $stageMap);
        }

        $this->assertStringNotContainsString('->assignToMe(', $service);
        $this->assertStringNotContainsString('->finishCaseStage(', $service);
        $this->assertStringNotContainsString('return back()', $controller);
    }

    public function test_v2_javascript_waits_for_server_state_and_handles_each_mutation_once(): void
    {
        $js = $this->source(
            public_path('assets/js/ysh-custom-js/employeeProductionBoardV2.js')
        );

        $this->assertMatchesRegularExpression('/\bfetch\s*\(/', $js);
        $this->assertStringContainsString('meta[name="csrf-token"]', $js);
        $this->assertStringContainsString('X-CSRF-TOKEN', $js);
        $this->assertStringContainsString('Idempotency-Key', $js);
        $this->assertStringContainsString('SolentProcessingOverlay.show', $js);
        $this->assertStringContainsString('SolentProcessingOverlay.hide', $js);

        $this->assertMatchesRegularExpression('/\binFlight\w*\s*=\s*new\s+(?:Set|Map)\s*\(/i', $js);
        $this->assertMatchesRegularExpression('/inFlight\w*\.has\s*\(/i', $js);
        $this->assertMatchesRegularExpression('/inFlight\w*\.(?:add|set)\s*\(/i', $js);
        $this->assertMatchesRegularExpression('/inFlight\w*\.size\s*>\s*0/i', $js);
        $this->assertMatchesRegularExpression('/finally\s*\{[^}]*inFlight\w*\.delete\s*\(/is', $js);

        $this->assertMatchesRegularExpression('/(?:window\.)?confirm\s*\(/', $js);
        $this->assertStringContainsString('response.ok', $js);
        $this->assertMatchesRegularExpression('/\bcatch\s*\(/', $js);
        $this->assertMatchesRegularExpression('/\bfinally\s*\{/', $js);

        $this->assertStringContainsString('data-epb-board-fragment', $js);
        $this->assertMatchesRegularExpression('/await\s+response\.json\s*\(\)/', $js);
        $this->assertMatchesRegularExpression('/board[_A-Z]?html/i', $js);
        $this->assertStringNotContainsString('location.reload', $js);
        $this->assertStringNotContainsString('window.location.reload', $js);
        $this->assertDoesNotMatchRegularExpression(
            '/(?:caseCard|caseElement|\[data-epb-case-card\]).{0,80}\.(?:remove|append|prepend)\s*\(/is',
            $js,
            'Cards must move only when the server-provided board fragment is reconciled.'
        );
    }

    public function test_v2_markup_and_styles_are_scoped_mobile_first_and_sheet_safe(): void
    {
        $view = $this->source(resource_path('views/generic/emp-cases-v2.blade.php'));
        $board = $this->source(
            resource_path('views/generic/employee-production-board-v2/_board.blade.php')
        );
        $card = $this->source(
            resource_path('views/generic/employee-production-board-v2/_case-card.blade.php')
        );
        $sheet = $this->source(
            resource_path('views/generic/employee-production-board-v2/_sheet.blade.php')
        );
        $css = $this->source(public_path('assets/css/employee-production-board-v2.css'));

        $this->assertStringContainsString('data-epb-root', $view);
        $this->assertStringContainsString('employee-production-board-v2.css', $view);
        $this->assertStringContainsString('employeeProductionBoardV2.js', $view);
        $this->assertStringContainsString('data-epb-board-fragment', $board);
        $this->assertStringContainsString('data-epb-case-card', $card);
        $this->assertStringContainsString('data-epb-mutation="start"', $card);
        $this->assertStringContainsString('data-epb-mutation="complete"', $card);
        $this->assertStringNotContainsString('epb-case-card__meta', $card);
        $this->assertStringContainsString("ltrim(\$caseId, '#')", $card);
        $this->assertStringContainsString('data-epb-sheet', $sheet);
        $this->assertStringContainsString('data-epb-sheet-content', $sheet);

        $this->assertGreaterThanOrEqual(15, substr_count($css, '.epb-'));
        $this->assertDoesNotMatchRegularExpression(
            '/(^|})\s*(?:\.card|\.btn|button|input|textarea|table|h[1-6]|p|a)\b[^,{]*\{/m',
            $css,
            'V2 styles must not leak through generic selectors.'
        );
        $this->assertMatchesRegularExpression(
            '/\.epb-button[^{]{0,100}\{[^}]*min-height:\s*44px\s*;/is',
            $css
        );
        $this->assertMatchesRegularExpression(
            '/\.epb-stage-strip\s*\{[^}]*flex-wrap:\s*wrap\s*;/is',
            $css
        );
        $this->assertStringNotContainsString('overflow-x: auto', $css);
        $this->assertMatchesRegularExpression(
            '/not\(\[data-epb-selected-stage="all"\]\)\s+\.epb-stage-label\s*\{[^}]*display:\s*none\s*;/is',
            $css
        );

        $this->assertMatchesRegularExpression(
            '/\.epb-case-grid\s*\{[^}]*grid-template-columns:\s*minmax\(0,\s*1fr\)\s*;[^}]*overflow:\s*visible\s*;/is',
            $css,
            'The mobile-first grid must be one column and must not create a nested scroller.'
        );
        $this->assertMatchesRegularExpression(
            '/@media\s*\(min-width:\s*700px\)[\s\S]*\.epb-case-grid\s*\{[^}]*grid-template-columns:\s*repeat\(2,\s*minmax\(0,\s*1fr\)\)\s*;/i',
            $css
        );

        $this->assertMatchesRegularExpression(
            '/\.epb-sheet-backdrop\s*\{[^}]*z-index:\s*(?:1\d{3,}|[2-9]\d{3,})\s*;/is',
            $css,
            'The full-height details sheet must sit above the application shell.'
        );
        $this->assertMatchesRegularExpression(
            '/\.epb-sheet\s*\{[^}]*height:\s*100dvh\s*;/is',
            $css
        );
    }

    public function test_normalized_board_fragment_renders_without_database_or_authenticated_user(): void
    {
        $board = [
            'selected_stage' => 'all',
            'stages' => [
                ['id' => 1, 'label' => 'Design', 'active_count' => 1, 'queue_count' => 1],
            ],
            'summary' => ['active' => 1, 'queue' => 1, 'due_today' => 0],
            'active' => [[
                'case_id' => 901,
                'display_id' => 'SL-901',
                'stage_id' => 1,
                'stage_label' => 'Design',
                'patient' => 'Sample Patient',
                'doctor' => 'Sample Doctor',
                'delivery' => '2026-08-24 10:00',
                'jobs' => [['label' => '11 · Crown · Zirconia']],
                'actions' => [
                    'details' => ['url' => '/production-board/cases/901/stages/1/details'],
                    'complete' => [
                        'url' => '/production-board/cases/901/stages/1/complete',
                        'label' => 'Complete',
                        'confirmation' => 'Complete this work?',
                    ],
                ],
            ]],
            'queue' => [[
                'case_id' => 902,
                'display_id' => 'SL-902',
                'stage_id' => 1,
                'stage_label' => 'Design',
                'patient' => 'Second Patient',
                'doctor' => 'Second Doctor',
                'delivery' => '2026-08-25 11:00',
                'jobs' => [['label' => '21 · Veneer · E.max']],
                'actions' => [
                    'details' => ['url' => '/production-board/cases/902/stages/1/details'],
                    'start' => [
                        'url' => '/production-board/cases/902/stages/1/start',
                        'label' => 'Start',
                    ],
                ],
            ]],
        ];

        $html = view('generic.employee-production-board-v2._board', compact('board'))->render();

        $this->assertSame(2, substr_count($html, 'data-epb-case-card'));
        $this->assertStringContainsString('#901', $html);
        $this->assertStringNotContainsString('Delivery 2026-08-24', $html);
        $this->assertStringContainsString('11 · Crown · Zirconia', $html);
        $this->assertStringContainsString('data-epb-mutation="complete"', $html);
        $this->assertStringContainsString('data-epb-mutation="start"', $html);
        $this->assertStringNotContainsString('Operator User', $html);
    }

    public function test_details_sheet_fragment_renders_jobs_files_notes_and_note_form(): void
    {
        $details = [
            'case_id' => 903,
            'display_id' => 'SL-903',
            'stage' => 4,
            'stage_label' => 'Sintering',
            'patient' => 'Sample Patient',
            'doctor' => 'Sample Doctor',
            'delivery' => '24 Aug 2026, 10:00 AM',
            'material' => 'Zirconia',
            'shade' => 'A2',
            'jobs' => [
                ['label' => '11 · Crown · Zirconia', 'type' => 'Crown', 'material' => 'Zirconia'],
            ],
            'files' => [
                ['name' => 'scan.stl', 'url' => '/caseImages/903/scan.stl'],
            ],
            'notes' => [
                ['text' => 'Check contact.', 'author' => 'Sample User', 'created_at' => '23 Aug 2026, 1:00 PM'],
            ],
            'full_case_url' => '/view-case/903/4',
            'note_action' => '/employee-production-board/cases/903/stages/4/notes',
        ];

        $html = view('generic.employee-production-board-v2._sheet-content', compact('details'))->render();

        $this->assertStringContainsString('SL-903', $html);
        $this->assertStringContainsString('11 · Crown · Zirconia', $html);
        $this->assertStringContainsString('scan.stl', $html);
        $this->assertStringContainsString('Check contact.', $html);
        $this->assertStringContainsString('data-epb-mutation="note"', $html);
        $this->assertStringContainsString('maxlength="255"', $html);
        $this->assertStringNotContainsString('@forelse', $html);
        $this->assertStringNotContainsString('@php', $html);
    }

    private function namedRoute(string $name): Route
    {
        $route = app('router')->getRoutes()->getByName($name);

        $this->assertNotNull($route, 'Missing named route: ' . $name);

        return $route;
    }

    private function source(string $path): string
    {
        $this->assertFileExists($path);

        $source = file_get_contents($path);
        $this->assertIsString($source);

        return $source;
    }
}
