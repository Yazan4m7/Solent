@php
    $stageJobs = $case->jobs->where('stage', $stage['number'])->values();
    $cardStageJobs = $stageJobs;

    if (!$isAdmin) {
        $currentUserId = (int) Auth::id();
        $ownedJobs = $stageJobs->filter(function ($job) use ($stage, $currentUserId): bool {
            if ($stage['number'] === 8) {
                return (int) $job->delivery_accepted === $currentUserId
                    || (int) $job->assignee === $currentUserId;
            }

            return (int) $job->assignee === $currentUserId;
        })->values();

        $cardStageJobs = $ownedJobs->isNotEmpty()
            ? $ownedJobs
            : $stageJobs->whereNull('assignee')->values();
    }

    $assigneeNames = $cardStageJobs
        ->map(fn ($job) => $job->assignedTo?->name_initials)
        ->filter()
        ->unique()
        ->values();
    $hasDeliveryOwner = $stage['number'] === 8 && $cardStageJobs->contains(fn ($job): bool => filled($job->delivery_accepted));
    $assigneeText = $assigneeNames->isNotEmpty()
        ? $assigneeNames->implode(', ')
        : ($hasDeliveryOwner ? 'Assigned' : 'Unassigned');
    $deliveryText = 'Not scheduled';
    $dueToday = false;

    try {
        if ($case->initial_delivery_date) {
            $deliveryDate = \Carbon\Carbon::parse(str_replace('T', ' ', (string) $case->initial_delivery_date));
            $deliveryText = $deliveryDate->format('d M, g:i A');
            $dueToday = $deliveryDate->isToday();
        }
    } catch (\Throwable $exception) {
        $deliveryText = str_replace('T', ' ', (string) $case->initial_delivery_date);
    }

    $jobLabels = $cardStageJobs->map(function ($job): string {
        return collect([
            $job->unit_num,
            $job->jobType?->name,
            $job->material?->name,
            ($job->color && $job->color !== '0') ? $job->color : null,
        ])->filter(fn ($value): bool => filled($value))->implode(' · ');
    })->filter()->values();
    $visibleJobLabels = $jobLabels->take(2);
    $remainingJobCount = max(0, $jobLabels->count() - $visibleJobLabels->count());

    $searchText = collect([
        $case->id,
        $case->case_id,
        $case->patient_name,
        $case->client?->name,
        $assigneeText,
        $jobLabels->implode(' '),
    ])->filter()->implode(' ');
    $cardKey = $stage['key'] . '-' . $state . '-' . $case->id;
@endphp

<article class="pc-case-card-wrap"
         data-pc-case-card
         data-case-id="{{ $case->id }}"
         data-stage="{{ $stage['key'] }}"
         data-state="{{ $state }}"
         data-due-today="{{ $dueToday ? 'true' : 'false' }}"
         data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
    <button class="pc-case-card"
            type="button"
            data-pc-open-details
            aria-current="false"
            aria-label="Open case #{{ $case->id }} for {{ $case->patient_name ?: 'patient not provided' }}">
        <div class="pc-case-card__top">
            <span class="pc-case-id"><i class="fa-regular fa-folder-open" aria-hidden="true"></i>#{{ $case->id }}</span>
            <span class="pc-case-card__signals">
                @if($dueToday)
                    <span class="pc-due-badge"><i class="fa-regular fa-clock" aria-hidden="true"></i>Due today</span>
                @endif
                <i class="fa-solid fa-chevron-right pc-case-chevron" aria-hidden="true"></i>
            </span>
        </div>
        <h3>{{ $case->patient_name ?: 'Patient not provided' }}</h3>
        <p class="pc-case-doctor"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i>{{ $case->client?->name ?? 'Doctor not assigned' }}</p>
        <div class="pc-case-jobs" aria-label="Stage jobs">
            @forelse($visibleJobLabels as $jobLabel)
                <span>{{ $jobLabel }}</span>
            @empty
                <span>No jobs listed for this stage</span>
            @endforelse
            @if($remainingJobCount > 0)
                <span class="pc-case-jobs__more">+{{ $remainingJobCount }} more</span>
            @endif
        </div>
        <div class="pc-case-card__footer" aria-label="Case delivery and assignment">
            <span>
                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                <span><small>Delivery</small><strong>{{ $deliveryText }}</strong></span>
            </span>
            <span>
                <i class="fa-regular fa-user" aria-hidden="true"></i>
                <span><small>Assigned</small><strong>{{ $assigneeText }}</strong></span>
            </span>
        </div>
    </button>

    <template data-pc-detail-template>
        @include('cases.partials.production-control-details', [
            'case' => $case,
            'stage' => $stage,
            'state' => $state,
            'stageJobs' => $cardStageJobs,
            'assigneeText' => $assigneeText,
            'deliveryText' => $deliveryText,
            'canAssignEmployees' => $canAssignEmployees,
            'isAdmin' => $isAdmin,
            'cardKey' => $cardKey,
        ])
    </template>
</article>
