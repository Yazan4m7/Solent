<?php

namespace App\Modules\Cases\Services;

use App\job;
use App\Modules\Cases\Exceptions\EmployeeProductionBoardException;
use App\Modules\Cases\Support\ProductionStageMap;
use App\sCase;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeProductionBoardQuery
{
    private ProductionStageMap $stages;

    public function __construct(ProductionStageMap $stages)
    {
        $this->stages = $stages;
    }

    public function board(
        User $user,
        int $requestedStage,
        bool $allowUnavailableRequestedStage = false
    ): array
    {
        $allowedStages = $this->allowedStages($user);

        if (! $allowUnavailableRequestedStage && ! in_array($requestedStage, $allowedStages, true)) {
            throw new EmployeeProductionBoardException(
                'You do not have work assigned in this production stage.',
                'stage_not_available',
                403
            );
        }

        $jobs = job::query()
            ->whereIn('stage', $allowedStages)
            ->where(function ($query) use ($user): void {
                if ($this->isAdmin($user)) {
                    $query->whereNull('assignee')
                        ->orWhere('assignee', $user->id)
                        ->orWhere('delivery_accepted', $user->id);

                    return;
                }

                $query->whereNull('assignee')
                    ->orWhere('assignee', $user->id)
                    ->orWhere('delivery_accepted', $user->id);
            })
            ->with([
                'case.client:id,name',
                'jobType:id,name',
                'material:id,name',
            ])
            ->orderBy('case_id')
            ->orderBy('stage')
            ->orderBy('id')
            ->get();

        $items = $jobs
            ->filter(function (job $job): bool {
                return $job->case !== null;
            })
            ->groupBy(function (job $job): string {
                return $job->case_id . ':' . (int) $job->stage;
            })
            ->map(function (Collection $group) use ($user): array {
                $stage = (int) $group->first()->stage;
                $owned = $this->ownedJobs($group, $user, $stage);
                $visibleJobs = $owned->isNotEmpty() ? $owned : $group->whereNull('assignee')->values();

                return $this->card($visibleJobs->isNotEmpty() ? $visibleJobs : $group, $user, $stage);
            })
            ->values();

        $active = $items
            ->where('state', 'active')
            ->sortBy(fn (array $item): string => $item['sort_key'])
            ->values();
        $queue = $items
            ->where('state', 'queue')
            ->sortBy(fn (array $item): string => $item['sort_key'])
            ->values();

        $stageCards = collect($allowedStages)->map(function (int $stage) use ($active, $queue): array {
            $definition = $this->stages->definitions()[$stage];

            return [
                'id' => $stage,
                'stage' => $stage,
                'label' => $definition['label'],
                'url' => route($definition['route'], $stage),
                'active_count' => $active->where('stage', $stage)->count(),
                'queue_count' => $queue->where('stage', $stage)->count(),
            ];
        })->values();

        return [
            'stages' => $stageCards->all(),
            'selected_stage' => 'all',
            'requested_stage' => $requestedStage,
            'active' => $active->map(fn (array $item): array => $this->withoutSortKey($item))->all(),
            'queue' => $queue->map(fn (array $item): array => $this->withoutSortKey($item))->all(),
            'summary' => [
                'active' => $active->count(),
                'queue' => $queue->count(),
                'due_today' => $active->concat($queue)->where('due_today', true)->count(),
            ],
        ];
    }

    public function details(User $user, int $caseId, int $stage): array
    {
        if (! $this->stages->isValid($stage)) {
            throw new EmployeeProductionBoardException(
                'The selected production stage is invalid.',
                'invalid_stage',
                422
            );
        }

        $case = sCase::query()
            ->with([
                'client:id,name',
                'jobs.jobType:id,name',
                'jobs.material:id,name',
                'notes' => fn ($query) => $query->latest('id'),
                'notes.writtenBy:id,first_name,last_name,name_initials',
                'photos',
            ])
            ->find($caseId);

        if (! $case) {
            throw new EmployeeProductionBoardException('The case could not be found.', 'case_not_found', 404);
        }

        if (! $this->canViewCaseStage($user, $case, $stage)) {
            throw new EmployeeProductionBoardException(
                'This case is not assigned to your production board.',
                'case_not_available',
                403
            );
        }

        $stageJobs = $case->jobs->where('stage', $stage)->values();
        $materials = $stageJobs->pluck('material.name')->filter()->unique()->values();
        $shades = $stageJobs->pluck('color')->filter(fn ($shade) => $shade !== '0' && $shade !== 0)->unique()->values();

        return [
            'case_id' => (int) $case->id,
            'display_id' => $this->displayId($case),
            'stage' => $stage,
            'stage_label' => $this->stages->label($stage),
            'patient' => (string) ($case->patient_name ?: 'Not provided'),
            'doctor' => (string) (optional($case->client)->name ?: 'Not assigned'),
            'delivery' => $this->deliveryText($case->initial_delivery_date),
            'material' => $materials->isNotEmpty() ? $materials->implode(', ') : 'Not provided',
            'shade' => $shades->isNotEmpty() ? $shades->implode(', ') : 'Not provided',
            'jobs' => $case->jobs->sortBy('id')->map(function (job $job): array {
                return $this->jobData($job, true);
            })->values()->all(),
            'files' => $case->photos->map(function ($file): array {
                $path = (string) ($file->path ?? '');

                return [
                    'id' => (int) $file->id,
                    'name' => $path !== '' ? basename(str_replace('\\', '/', $path)) : 'Attachment',
                    'url' => $path !== '' ? asset($path) : null,
                ];
            })->values()->all(),
            'notes' => $case->notes->map(function ($note): array {
                $author = $note->writtenBy;
                $authorName = $author
                    ? trim((string) (($author->first_name ?? '') . ' ' . ($author->last_name ?? '')))
                    : '';

                return [
                    'id' => (int) $note->id,
                    'text' => (string) $note->note,
                    'author' => $authorName !== ''
                        ? $authorName
                        : (string) ($author ? ($author->name_initials ?? 'Unknown user') : 'Unknown user'),
                    'created_at' => $note->created_at ? Carbon::parse($note->created_at)->format('d M Y, g:i A') : '',
                ];
            })->values()->all(),
            'full_case_url' => route('view-case', ['id' => $case->id, 'stage' => $stage]),
            'note_action' => route('employee-production-board-v2.notes.store', ['caseId' => $case->id, 'stage' => $stage]),
        ];
    }

    public function allowedStages(User $user): array
    {
        if ($this->isAdmin($user)) {
            return $this->stages->stages();
        }

        $permissionIds = $user->permissions()
            ->pluck('permission_id')
            ->map(fn ($permission): int => (int) $permission)
            ->all();

        $allowed = collect($this->stages->definitions())
            ->filter(function (array $definition) use ($permissionIds): bool {
                return in_array((int) $definition['permission'], $permissionIds, true);
            })
            ->keys()
            ->map(fn ($stage): int => (int) $stage);

        $assigned = job::query()
            ->where(function ($query) use ($user): void {
                $query->where('assignee', $user->id)
                    ->orWhere('delivery_accepted', $user->id);
            })
            ->whereIn('stage', $this->stages->stages())
            ->distinct()
            ->pluck('stage')
            ->map(fn ($stage): int => (int) $stage);

        return $allowed
            ->merge($assigned)
            ->unique()
            ->sortBy(function (int $stage): int {
                return array_search($stage, $this->stages->stages(), true);
            })
            ->values()
            ->all();
    }

    private function card(Collection $jobs, User $user, int $stage): array
    {
        /** @var job $first */
        $first = $jobs->first();
        $case = $first->case;
        $active = $this->isActive($jobs, $user, $stage);
        $materials = $jobs->pluck('material.name')->filter()->unique()->values();
        $shades = $jobs->pluck('color')->filter(fn ($shade) => $shade !== '0' && $shade !== 0)->unique()->values();

        $item = [
            'key' => $case->id . ':' . $stage,
            'case_id' => (int) $case->id,
            'id' => $this->displayId($case),
            'display_id' => $this->displayId($case),
            'stage' => $stage,
            'stage_id' => $stage,
            'stage_label' => $this->stages->label($stage),
            'patient' => (string) ($case->patient_name ?: 'Not provided'),
            'patient_name' => (string) ($case->patient_name ?: 'Not provided'),
            'doctor' => (string) (optional($case->client)->name ?: 'Not assigned'),
            'doctor_name' => (string) (optional($case->client)->name ?: 'Not assigned'),
            'delivery' => $this->deliveryText($case->initial_delivery_date),
            'delivery_text' => $this->deliveryText($case->initial_delivery_date),
            'material' => $materials->isNotEmpty() ? $materials->implode(', ') : 'Not provided',
            'material_text' => $materials->isNotEmpty() ? $materials->implode(', ') : 'Not provided',
            'shade' => $shades->isNotEmpty() ? $shades->implode(', ') : 'Not provided',
            'shade_text' => $shades->isNotEmpty() ? $shades->implode(', ') : 'Not provided',
            'jobs' => $jobs->map(fn (job $job): array => $this->jobData($job))->values()->all(),
            'state' => $active ? 'active' : 'queue',
            'due_today' => $this->isDueToday($case->initial_delivery_date),
            'details_url' => route('employee-production-board-v2.details', ['caseId' => $case->id, 'stage' => $stage]),
            'detail_url' => route('employee-production-board-v2.details', ['caseId' => $case->id, 'stage' => $stage]),
            'full_case_url' => route('view-case', ['id' => $case->id, 'stage' => $stage]),
            'sort_key' => $this->sortKey($case->initial_delivery_date, (int) $case->id),
        ];

        if ($active) {
            $item['complete_action'] = [
                'url' => route('employee-production-board-v2.complete', ['caseId' => $case->id, 'stage' => $stage]),
                'label' => $stage === 8 ? 'Delivered in box' : 'Complete',
                'confirm' => $stage === 8
                    ? 'Mark this case as delivered in box?'
                    : 'Complete this case in ' . $this->stages->label($stage) . '?',
            ];
        } else {
            $item['start_action'] = [
                'url' => route('employee-production-board-v2.start', ['caseId' => $case->id, 'stage' => $stage]),
                'label' => $stage === 8 ? 'Accept' : 'Start',
            ];
        }

        return $item;
    }

    private function ownedJobs(Collection $jobs, User $user, int $stage): Collection
    {
        return $jobs->filter(function (job $job) use ($user, $stage): bool {
            if ($stage === 8) {
                return (int) $job->delivery_accepted === (int) $user->id
                    || (int) $job->assignee === (int) $user->id;
            }

            return (int) $job->assignee === (int) $user->id;
        })->values();
    }

    private function isActive(Collection $jobs, User $user, int $stage): bool
    {
        if ($stage === 8) {
            return $jobs->contains(function (job $job) use ($user): bool {
                return (int) $job->delivery_accepted === (int) $user->id;
            });
        }

        if (in_array($stage, [2, 3], true)) {
            return $jobs->contains(function (job $job) use ($user): bool {
                return (int) $job->assignee === (int) $user->id && (int) $job->is_set === 1;
            });
        }

        return $jobs->contains(function (job $job) use ($user): bool {
            return (int) $job->assignee === (int) $user->id && (int) $job->is_active === 1;
        });
    }

    private function canViewCaseStage(User $user, sCase $case, int $stage): bool
    {
        if (! $case->jobs->contains('stage', $stage)) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->permissions()
            ->where('permission_id', $this->stages->permissionFor($stage))
            ->exists()) {
            return true;
        }

        return $case->jobs->contains(function (job $job) use ($user, $stage): bool {
            return (int) $job->stage === $stage
                && ((int) $job->assignee === (int) $user->id
                    || (int) $job->delivery_accepted === (int) $user->id);
        });
    }

    private function jobData(job $job, bool $includeStage = false): array
    {
        $parts = array_filter([
            trim((string) $job->unit_num),
            optional($job->jobType)->name,
            optional($job->material)->name,
            ($job->color !== null && $job->color !== '0' && $job->color !== 0) ? $job->color : null,
            ($job->style !== null && $job->style !== 'None') ? $job->style : null,
        ], fn ($value): bool => trim((string) $value) !== '');

        $data = [
            'id' => (int) $job->id,
            'units' => (string) ($job->unit_num ?? ''),
            'type' => (string) (optional($job->jobType)->name ?? 'Job'),
            'material' => (string) (optional($job->material)->name ?? ''),
            'shade' => ($job->color !== '0' && $job->color !== 0) ? (string) ($job->color ?? '') : '',
            'style' => $job->style !== 'None' ? (string) ($job->style ?? '') : '',
            'label' => implode(' · ', $parts),
        ];

        if ($includeStage) {
            $data['stage'] = (int) $job->stage;
            $data['stage_label'] = $this->stages->isValid((int) $job->stage)
                ? $this->stages->label((int) $job->stage)
                : ((int) $job->stage === -1 ? 'Completed' : 'Other');
        }

        return $data;
    }

    private function displayId(sCase $case): string
    {
        return trim((string) ($case->case_id ?? '')) !== ''
            ? (string) $case->case_id
            : '#' . $case->id;
    }

    private function deliveryText($value): string
    {
        if (! $value) {
            return 'Not scheduled';
        }

        try {
            return Carbon::parse(str_replace('T', ' ', (string) $value))->format('d M, g:i A');
        } catch (\Throwable $exception) {
            return str_replace('T', ' ', (string) $value);
        }
    }

    private function isDueToday($value): bool
    {
        if (! $value) {
            return false;
        }

        try {
            return Carbon::parse(str_replace('T', ' ', (string) $value))->isToday();
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function sortKey($value, int $caseId): string
    {
        try {
            $date = $value ? Carbon::parse(str_replace('T', ' ', (string) $value))->format('YmdHis') : '99999999999999';
        } catch (\Throwable $exception) {
            $date = '99999999999999';
        }

        return $date . ':' . str_pad((string) $caseId, 12, '0', STR_PAD_LEFT);
    }

    private function isAdmin(User $user): bool
    {
        return (int) $user->is_admin === 1;
    }

    private function withoutSortKey(array $item): array
    {
        unset($item['sort_key']);

        return $item;
    }
}
