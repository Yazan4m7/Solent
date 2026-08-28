<?php

namespace App\Modules\Cases\Services;

use App\abutmentDeliveryRecord;
use App\caseLog;
use App\caseTag;
use App\client;
use App\failureLog;
use App\invoice;
use App\job;
use App\Models\EmployeeBoardActionRequest;
use App\Modules\Cases\Exceptions\EmployeeProductionBoardException;
use App\Modules\Cases\Http\Controllers\CaseController;
use App\Modules\Cases\Support\ProductionStageMap;
use App\note as CaseNote;
use App\sCase;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EmployeeProductionBoardActionService
{
    private ProductionStageMap $stages;
    private CaseController $legacyCaseController;

    public function __construct(ProductionStageMap $stages, CaseController $legacyCaseController)
    {
        $this->stages = $stages;
        $this->legacyCaseController = $legacyCaseController;
    }

    public function start(User $user, int $caseId, int $stage, string $requestKey): array
    {
        $result = $this->executeIdempotently(
            $user,
            'start',
            $caseId,
            $stage,
            $requestKey,
            [],
            function () use ($user, $caseId, $stage): array {
                $this->assertStageAccess($user, $caseId, $stage);
                [, $allJobs] = $this->lockCaseAndJobs($caseId);
                $stageJobs = $this->jobsAtStage($allJobs, $stage);

                if ($stageJobs->isEmpty()) {
                    throw $this->conflict(
                        'case_stage_changed',
                        "Case #{$caseId} no longer has work in {$this->stages->label($stage)}.",
                        $caseId,
                        $stage
                    );
                }

                if ($this->stages->isDelivery($stage)) {
                    return $this->startDelivery($user, $caseId, $stage, $stageJobs);
                }

                $userId = (int) $user->getKey();
                $eligibleJobs = $stageJobs->filter(function (job $job) use ($userId): bool {
                    return $job->assignee === null || (int) $job->assignee === $userId;
                })->values();

                if ($eligibleJobs->isEmpty()) {
                    throw $this->conflict(
                        'case_owned_by_another_user',
                        'This work has already been assigned to another employee.',
                        $caseId,
                        $stage
                    );
                }

                $changed = false;
                $activate = $this->stages->activatesWhenStarted($stage);

                foreach ($eligibleJobs as $stageJob) {
                    $jobChanged = (int) $stageJob->assignee !== $userId
                        || (int) $stageJob->is_set !== 1
                        || ($activate && (int) $stageJob->is_active !== 1);

                    if (!$jobChanged) {
                        continue;
                    }

                    $stageJob->assignee = $userId;
                    $stageJob->is_set = 1;
                    if ($activate) {
                        $stageJob->is_active = 1;
                    }
                    $stageJob->save();
                    $changed = true;
                }

                if ($changed) {
                    $this->writeCaseLog($userId, $caseId, $this->stages->startLogStage($stage), false);
                }

                return $this->result(
                    $changed,
                    $changed
                        ? "Case #{$caseId} has been started in {$this->stages->label($stage)}."
                        : "Case #{$caseId} is already active in {$this->stages->label($stage)}.",
                    $caseId,
                    $stage
                );
            }
        );

        $this->invalidateBoardCache($result);

        return $result;
    }

    public function complete(User $user, int $caseId, int $stage, string $requestKey): array
    {
        $result = $this->executeIdempotently(
            $user,
            'complete',
            $caseId,
            $stage,
            $requestKey,
            [],
            function () use ($user, $caseId, $stage): array {
                $this->assertStageAccess($user, $caseId, $stage);
                [$case, $allJobs] = $this->lockCaseAndJobs($caseId);
                $stageJobs = $this->jobsAtStage($allJobs, $stage);

                if ($stageJobs->isEmpty()) {
                    throw $this->conflict(
                        'case_stage_changed',
                        "Case #{$caseId} no longer has work in {$this->stages->label($stage)}.",
                        $caseId,
                        $stage
                    );
                }

                $userId = (int) $user->getKey();
                $userJobs = $stageJobs->filter(function (job $job) use ($userId): bool {
                    return (int) $job->assignee === $userId;
                })->values();

                if ($userJobs->isEmpty()) {
                    $hasAssignedJobs = $stageJobs->contains(function (job $job): bool {
                        return $job->assignee !== null;
                    });

                    throw $this->conflict(
                        $hasAssignedJobs ? 'case_owned_by_another_user' : 'case_not_started',
                        $hasAssignedJobs
                            ? 'This work is assigned to another employee.'
                            : 'Start this work before completing it.',
                        $caseId,
                        $stage
                    );
                }

                if ($this->stages->isDelivery($stage)) {
                    $notAccepted = $userJobs->contains(function (job $job) use ($userId): bool {
                        return (int) $job->delivery_accepted !== $userId;
                    });

                    if ($notAccepted) {
                        throw $this->conflict(
                            'delivery_not_accepted',
                            'Accept this delivery before completing it.',
                            $caseId,
                            $stage
                        );
                    }
                }

                if ($stage === 6) {
                    $this->assertFinishingCanComplete($caseId, $stage, $allJobs);
                }

                $nextStages = [];
                foreach ($userJobs as $stageJob) {
                    $nextStages[(int) $stageJob->id] = $this->nextStageFor($stageJob);
                }

                if (in_array(7, $nextStages, true)) {
                    $this->assertAllUnitsHaveReachedFinishing($caseId, $stage, $allJobs);
                }

                $invoiceJob = $this->invoiceSeedJob($allJobs, $userJobs);
                if ($this->transitionNeedsInvoice($nextStages)) {
                    $this->ensureInvoiceExists($case, $invoiceJob);
                }

                foreach ($userJobs as $stageJob) {
                    $stageJob->stage = $nextStages[(int) $stageJob->id];
                    $stageJob->assignee = null;
                    $stageJob->is_active = null;
                    $stageJob->is_set = null;
                    $stageJob->device_id = null;
                    $stageJob->save();
                }

                $this->writeCaseLog($userId, $caseId, $this->stages->completeLogStage($stage), true);

                $wasFinalized = $this->finalizeCaseWhenComplete(
                    $case,
                    $allJobs,
                    $invoiceJob,
                    $userId,
                    $this->stages->isDelivery($stage)
                );

                $message = $wasFinalized && $this->stages->isDelivery($stage)
                    ? "Case #{$caseId} has been delivered in box."
                    : "Case #{$caseId} has been completed in {$this->stages->label($stage)}.";

                return $this->result(true, $message, $caseId, $stage);
            }
        );

        $this->invalidateBoardCache($result);

        return $result;
    }

    public function addNote(
        User $user,
        int $caseId,
        int $stage,
        string $note,
        string $requestKey
    ): array {
        $note = trim($note);

        if ($note === '') {
            throw new EmployeeProductionBoardException(
                'Enter a note before saving.',
                'note_required',
                422,
                ['case_id' => $caseId, 'stage' => $stage]
            );
        }

        if (mb_strlen($note) > 255) {
            throw new EmployeeProductionBoardException(
                'Notes may not be longer than 255 characters.',
                'note_too_long',
                422,
                ['case_id' => $caseId, 'stage' => $stage]
            );
        }

        $result = $this->executeIdempotently(
            $user,
            'note',
            $caseId,
            $stage,
            $requestKey,
            ['note' => $note],
            function () use ($user, $caseId, $stage, $note): array {
                $this->assertStageAccess($user, $caseId, $stage);
                [$case, $allJobs] = $this->lockCaseAndJobs($caseId);

                if ($this->jobsAtStage($allJobs, $stage)->isEmpty()) {
                    throw $this->conflict(
                        'case_stage_changed',
                        "Case #{$caseId} no longer has work in {$this->stages->label($stage)}.",
                        $caseId,
                        $stage
                    );
                }

                CaseNote::create([
                    'case_id' => $caseId,
                    'note' => $note,
                    'written_by' => (int) $user->getKey(),
                ]);

                $this->ensureCaseTag($case, 5, (int) $user->getKey());

                return $this->result(
                    true,
                    "Note added to case #{$caseId}.",
                    $caseId,
                    $stage
                );
            }
        );

        $this->invalidateBoardCache($result);

        return $result;
    }

    private function startDelivery(User $user, int $caseId, int $stage, Collection $stageJobs): array
    {
        $userId = (int) $user->getKey();
        $ownedByAnotherUser = $stageJobs->contains(function (job $job) use ($userId): bool {
            return ($job->assignee !== null && (int) $job->assignee !== $userId)
                || ($job->delivery_accepted !== null && (int) $job->delivery_accepted !== $userId);
        });

        if ($ownedByAnotherUser) {
            throw $this->conflict(
                'delivery_owned_by_another_user',
                'This delivery has already been assigned to another driver.',
                $caseId,
                $stage
            );
        }

        $changed = false;
        foreach ($stageJobs as $stageJob) {
            if ((int) $stageJob->assignee === $userId
                && (int) $stageJob->delivery_accepted === $userId) {
                continue;
            }

            $stageJob->assignee = $userId;
            $stageJob->delivery_accepted = $userId;
            $stageJob->save();
            $changed = true;
        }

        if ($changed) {
            $this->writeCaseLog($userId, $caseId, $this->stages->startLogStage($stage), false);
        }

        return $this->result(
            $changed,
            $changed
                ? "Delivery for case #{$caseId} has been accepted."
                : "Delivery for case #{$caseId} was already accepted.",
            $caseId,
            $stage
        );
    }

    private function executeIdempotently(
        User $user,
        string $action,
        int $caseId,
        int $stage,
        string $requestKey,
        array $payload,
        callable $callback
    ): array {
        $requestKey = strtolower(trim($requestKey));
        $this->assertValidRequestKey($requestKey, $caseId, $stage);

        $fingerprint = hash('sha256', json_encode([
            'user_id' => (int) $user->getKey(),
            'action' => $action,
            'case_id' => $caseId,
            'stage' => $stage,
            'payload' => $payload,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        try {
            return DB::transaction(function () use (
                $user,
                $action,
                $caseId,
                $stage,
                $requestKey,
                $fingerprint,
                $callback
            ): array {
                $existing = EmployeeBoardActionRequest::where('request_key', $requestKey)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $this->replay($existing, $fingerprint, $caseId, $stage);
                }

                $actionRequest = EmployeeBoardActionRequest::create([
                    'request_key' => $requestKey,
                    'user_id' => (int) $user->getKey(),
                    'action' => $action,
                    'case_id' => $caseId,
                    'stage' => $stage,
                    'payload_hash' => $fingerprint,
                ]);

                $result = $callback();
                $actionRequest->response_payload = $result;
                $actionRequest->completed_at = now();
                $actionRequest->save();

                return $result;
            }, 3);
        } catch (QueryException $exception) {
            if (!$this->isDuplicateKeyException($exception)) {
                throw $exception;
            }

            $existing = DB::transaction(function () use ($requestKey): ?EmployeeBoardActionRequest {
                return EmployeeBoardActionRequest::where('request_key', $requestKey)
                    ->lockForUpdate()
                    ->first();
            }, 3);

            if (!$existing) {
                throw $exception;
            }

            return $this->replay($existing, $fingerprint, $caseId, $stage);
        }
    }

    private function replay(
        EmployeeBoardActionRequest $request,
        string $fingerprint,
        int $caseId,
        int $stage
    ): array {
        if (!hash_equals((string) $request->payload_hash, $fingerprint)) {
            throw new EmployeeProductionBoardException(
                'This request key has already been used for a different action.',
                'idempotency_key_reused',
                409,
                ['case_id' => $caseId, 'stage' => $stage]
            );
        }

        $result = $request->response_payload;
        if (!$request->completed_at || !is_array($result)) {
            throw new EmployeeProductionBoardException(
                'The original request has not completed. Retry shortly.',
                'idempotency_request_incomplete',
                409,
                ['case_id' => $caseId, 'stage' => $stage]
            );
        }

        $result['replayed'] = true;

        return $result;
    }

    private function assertStageAccess(User $user, int $caseId, int $stage): void
    {
        if (!$this->stages->isValid($stage)) {
            throw new EmployeeProductionBoardException(
                'The selected production stage is invalid.',
                'invalid_stage',
                422,
                ['stage' => $stage]
            );
        }

        if ((bool) $user->is_admin) {
            return;
        }

        $hasPermission = $user->permissions()
            ->where('permission_id', $this->stages->permissionFor($stage))
            ->exists();

        if ($hasPermission) {
            return;
        }

        $ownsCurrentWork = job::where('case_id', $caseId)
            ->where('stage', $stage)
            ->where(function ($query) use ($user): void {
                $query->where('assignee', $user->getKey())
                    ->orWhere('delivery_accepted', $user->getKey());
            })
            ->exists();

        if (!$ownsCurrentWork) {
            throw new EmployeeProductionBoardException(
                "You do not have access to {$this->stages->label($stage)}.",
                'stage_access_denied',
                403,
                ['stage' => $stage]
            );
        }
    }

    private function assertValidRequestKey(string $requestKey, int $caseId, int $stage): void
    {
        if (!Str::isUuid($requestKey)) {
            throw new EmployeeProductionBoardException(
                'A valid UUID request key is required.',
                'invalid_idempotency_key',
                422,
                ['case_id' => $caseId, 'stage' => $stage]
            );
        }
    }

    private function lockCaseAndJobs(int $caseId): array
    {
        $case = sCase::whereKey($caseId)->lockForUpdate()->first();
        if (!$case) {
            throw new EmployeeProductionBoardException(
                "Case #{$caseId} was not found.",
                'case_not_found',
                404,
                ['case_id' => $caseId]
            );
        }

        $jobs = job::where('case_id', $caseId)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        return [$case, $jobs];
    }

    private function jobsAtStage(Collection $jobs, int $stage): Collection
    {
        return $jobs->filter(function (job $job) use ($stage): bool {
            return (int) $job->stage === $stage;
        })->values();
    }

    private function assertFinishingCanComplete(int $caseId, int $stage, Collection $allJobs): void
    {
        $this->assertAllUnitsHaveReachedFinishing($caseId, $stage, $allJobs);

        $abutmentRecords = abutmentDeliveryRecord::where('case_id', $caseId)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($abutmentRecords->contains(function (abutmentDeliveryRecord $record): bool {
            return (int) $record->status !== 3;
        })) {
            throw $this->conflict(
                'abutments_not_received',
                'All abutments must be received before Finishing can be completed.',
                $caseId,
                $stage
            );
        }
    }

    private function assertAllUnitsHaveReachedFinishing(
        int $caseId,
        int $stage,
        Collection $allJobs
    ): void {
        $hasEarlierWork = $allJobs->contains(function (job $job): bool {
            // Stage 9 (Metal Work) is semantically before Finishing even though
            // its numeric identifier is greater than 6.
            return in_array((int) $job->stage, [1, 2, 3, 4, 5, 9], true);
        });

        if ($hasEarlierWork) {
            throw $this->conflict(
                'case_not_ready_for_finishing',
                'Not all jobs have reached the Finishing stage.',
                $caseId,
                $stage
            );
        }
    }

    private function nextStageFor(job $stageJob): int
    {
        $material = $stageJob->material;
        if (!$material) {
            throw $this->conflict(
                'job_material_missing',
                "Job #{$stageJob->id} has no material configuration.",
                (int) $stageJob->case_id,
                (int) $stageJob->stage
            );
        }

        $workflow = [
            1 => 'design',
            2 => 'mill',
            3 => 'print_3d',
            4 => 'sinter_furnace',
            5 => 'press_furnace',
            9 => 'metal_work',
            6 => 'finish',
            7 => 'qc',
            8 => 'delivery',
        ];
        $currentStage = (int) $stageJob->stage;
        $currentPosition = array_search($currentStage, array_keys($workflow), true);

        if ($currentPosition === false) {
            throw $this->conflict(
                'invalid_job_stage',
                "Job #{$stageJob->id} is not in a supported production stage.",
                (int) $stageJob->case_id,
                $currentStage
            );
        }

        foreach (array_slice($workflow, $currentPosition + 1, null, true) as $nextStage => $flag) {
            if ((bool) $material->{$flag}) {
                return (int) $nextStage;
            }
        }

        return -1;
    }

    private function transitionNeedsInvoice(array $nextStages): bool
    {
        return count(array_intersect([7, 8, -1], array_values($nextStages))) > 0;
    }

    private function invoiceSeedJob(Collection $allJobs, Collection $userJobs): job
    {
        $billableJob = $allJobs->first(function (job $job): bool {
            return !(bool) $job->is_repeat && !(bool) $job->is_modification;
        });

        return $billableJob ?: $userJobs->first();
    }

    private function ensureInvoiceExists(sCase $case, job $invoiceJob): Collection
    {
        $invoices = invoice::where('case_id', $case->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($invoices->isNotEmpty() || (bool) $case->contains_modification) {
            return $invoices;
        }

        // The legacy helper owns the established pricing/discount calculation.
        // Calling it once under the locked case row avoids its per-job duplicate path.
        $this->legacyCaseController->issueInvoice($invoiceJob);

        return invoice::where('case_id', $case->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function finalizeCaseWhenComplete(
        sCase $case,
        Collection $allJobs,
        job $invoiceJob,
        int $userId,
        bool $deliveredInBox
    ): bool {
        $allCompleted = $allJobs->isNotEmpty() && $allJobs->every(function (job $job): bool {
            return (int) $job->stage === -1;
        });

        if (!$allCompleted) {
            return false;
        }

        if ((bool) $case->contains_modification) {
            $failure = failureLog::where('case_id', $case->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if ($deliveredInBox) {
                $case->actual_delivery_date = now();
            } else {
                $case->actual_delivery_date = $failure && $failure->old_delivery_date
                    ? $failure->old_delivery_date
                    : now();
            }

            CaseNote::create([
                'case_id' => $case->id,
                'note' => $failure
                    ? 'Modification Delivered On: ' . now()
                    : 'Failure Log was not found, no previous delivery date',
                'written_by' => $userId,
            ]);
        } else {
            $case->actual_delivery_date = now();
        }

        $case->delivered_to_client = 1;
        if ($deliveredInBox) {
            $case->delivered_in_box = 1;
            $this->ensureCaseTag($case, 15, $userId);
        }
        $case->save();

        $invoices = $this->ensureInvoiceExists($case, $invoiceJob);
        $this->applyInvoiceBalanceOnce($case, $invoices);

        return true;
    }

    private function applyInvoiceBalanceOnce(sCase $case, Collection $invoices): void
    {
        if ($invoices->isEmpty() || $invoices->contains(function (invoice $invoice): bool {
            return (int) $invoice->status === 1;
        })) {
            return;
        }

        $canonicalInvoice = $invoices->first();
        $doctor = client::whereKey($case->doctor_id)->lockForUpdate()->first();

        if (!$doctor) {
            throw new EmployeeProductionBoardException(
                'The case doctor could not be found while applying its invoice.',
                'invoice_doctor_missing',
                409,
                ['case_id' => (int) $case->id]
            );
        }

        $doctor->balance = (float) $doctor->balance + (float) ($canonicalInvoice->amount ?? 0);
        $doctor->save();

        $canonicalInvoice->status = 1;
        $canonicalInvoice->date_applied = now();
        $canonicalInvoice->save();
    }

    private function ensureCaseTag(sCase $case, int $tagId, int $userId): void
    {
        $existingTag = caseTag::where('case_id', $case->id)
            ->where('tag_id', $tagId)
            ->lockForUpdate()
            ->first();

        if ($existingTag) {
            return;
        }

        caseTag::create([
            'case_id' => $case->id,
            'tag_id' => $tagId,
            'added_by' => $userId,
        ]);
    }

    private function writeCaseLog(int $userId, int $caseId, float $stage, bool $completion): void
    {
        caseLog::create([
            'user_id' => $userId,
            'case_id' => $caseId,
            'stage' => $stage,
            'is_completion' => $completion ? 1 : 0,
        ]);
    }

    private function result(bool $changed, string $message, int $caseId, int $stage): array
    {
        return [
            'changed' => $changed,
            'message' => $message,
            'case_id' => $caseId,
            'stage' => $stage,
        ];
    }

    private function conflict(string $code, string $message, int $caseId, int $stage): EmployeeProductionBoardException
    {
        return new EmployeeProductionBoardException(
            $message,
            $code,
            409,
            ['case_id' => $caseId, 'stage' => $stage]
        );
    }

    private function isDuplicateKeyException(QueryException $exception): bool
    {
        $driverCode = $exception->errorInfo[1] ?? null;
        $sqlState = (string) ($exception->errorInfo[0] ?? $exception->getCode());

        return (int) $driverCode === 1062 || in_array($sqlState, ['23000', '23505'], true);
    }

    private function invalidateBoardCache(array $result): void
    {
        if (empty($result['changed']) || !empty($result['replayed'])) {
            return;
        }

        try {
            if (!Cache::has('dashboard_cache_version')) {
                Cache::forever('dashboard_cache_version', 1);
            }
            Cache::increment('dashboard_cache_version');
        } catch (Throwable $exception) {
            Log::warning('[EmployeeProductionBoard] Unable to invalidate dashboard cache', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
