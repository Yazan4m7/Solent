<?php

namespace App\Modules\Cases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cases\Exceptions\EmployeeProductionBoardException;
use App\Modules\Cases\Services\EmployeeProductionBoardActionService;
use App\Modules\Cases\Services\EmployeeProductionBoardQuery;
use App\Support\FeatureFlags\FeatureManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeProductionBoardController extends Controller
{
    private FeatureManager $features;
    private EmployeeProductionBoardQuery $query;
    private EmployeeProductionBoardActionService $actions;
    private CaseController $legacyCases;

    public function __construct(
        FeatureManager $features,
        EmployeeProductionBoardQuery $query,
        EmployeeProductionBoardActionService $actions,
        CaseController $legacyCases
    ) {
        $this->features = $features;
        $this->query = $query;
        $this->actions = $actions;
        $this->legacyCases = $legacyCases;
    }

    public function show($stage)
    {
        $stage = (int) $stage;

        if (! $this->enabled()) {
            return $this->legacyCases->employeeDashboard($stage);
        }

        try {
            $this->assertStage($stage);
            $board = $this->query->board(auth()->user(), $stage);
        } catch (EmployeeProductionBoardException $exception) {
            abort($exception->status(), $exception->getMessage());
        }

        return view('generic.emp-cases-v2', compact('board'))
            ->with('pageSlug', 'Production control');
    }

    public function details($caseId, $stage): Response
    {
        try {
            $this->assertEnabled();
            $stage = (int) $stage;
            $this->assertStage($stage);
            $details = $this->query->details(auth()->user(), (int) $caseId, $stage);

            return response(
                view('generic.employee-production-board-v2._sheet-content', compact('details'))->render()
            );
        } catch (EmployeeProductionBoardException $exception) {
            return response($exception->getMessage(), $exception->status());
        } catch (Throwable $exception) {
            Log::error('[employee-production-board-v2] details failed', [
                'case_id' => (int) $caseId,
                'stage' => (int) $stage,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            return response('Case details could not be loaded.', 500);
        }
    }

    public function start(Request $request, $caseId, $stage): JsonResponse
    {
        return $this->mutation($request, (int) $caseId, (int) $stage, function (string $requestKey) use ($caseId, $stage): array {
            return $this->actions->start(auth()->user(), (int) $caseId, (int) $stage, $requestKey);
        });
    }

    public function complete(Request $request, $caseId, $stage): JsonResponse
    {
        return $this->mutation($request, (int) $caseId, (int) $stage, function (string $requestKey) use ($caseId, $stage): array {
            return $this->actions->complete(auth()->user(), (int) $caseId, (int) $stage, $requestKey);
        });
    }

    public function addNote(Request $request, $caseId, $stage): JsonResponse
    {
        return $this->mutation($request, (int) $caseId, (int) $stage, function (string $requestKey) use ($request, $caseId, $stage): array {
            $note = trim((string) $request->input('note', ''));
            $result = $this->actions->addNote(auth()->user(), (int) $caseId, (int) $stage, $note, $requestKey);
            $details = $this->query->details(auth()->user(), (int) $caseId, (int) $stage);
            $result['sheet_html'] = view('generic.employee-production-board-v2._sheet-content', compact('details'))->render();

            return $result;
        });
    }

    private function mutation(Request $request, int $caseId, int $stage, callable $action): JsonResponse
    {
        try {
            $this->assertEnabled();
            $this->assertStage($stage);
            $requestKey = trim((string) $request->header('Idempotency-Key', $request->input('idempotency_key', '')));
            $result = $action($requestKey);
            // Completion can remove the user's last assignment in this stage.
            // The action still succeeded, so return the remaining board instead
            // of turning the committed mutation into a misleading 403 response.
            $board = $this->query->board(auth()->user(), $stage, true);

            return response()->json(array_merge([
                'ok' => true,
                'board_html' => view('generic.employee-production-board-v2._board', compact('board'))->render(),
                'sheet_html' => null,
                'case_id' => $caseId,
                'stage' => $stage,
            ], $result));
        } catch (EmployeeProductionBoardException $exception) {
            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->errorCode(),
                'errors' => $exception->context(),
            ], $exception->status());
        } catch (Throwable $exception) {
            Log::error('[employee-production-board-v2] mutation failed', [
                'case_id' => $caseId,
                'stage' => $stage,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'The action could not be completed. Please refresh the board and try again.',
            ], 500);
        }
    }

    private function enabled(): bool
    {
        $tenant = app()->bound('app.tenant_context')
            ? app('app.tenant_context')->cacheKey()
            : 'default';

        return $this->features->enabled('employee-production-board-v2', $tenant);
    }

    private function assertEnabled(): void
    {
        if (! $this->enabled()) {
            throw new EmployeeProductionBoardException(
                'The new production board is currently disabled.',
                'production_board_disabled',
                404
            );
        }
    }

    private function assertStage(int $stage): void
    {
        if (! in_array($stage, [1, 2, 3, 4, 5, 6, 7, 8, 9], true)) {
            throw new EmployeeProductionBoardException(
                'The selected production stage is invalid.',
                'invalid_stage',
                422,
                ['stage' => ['Select a valid production stage.']]
            );
        }
    }
}
