@php
    $stageJobs = $case->jobs->where('stage', $stage['number'])->values();
    $currentJob = $stageJobs->first();
    $assignee = $currentJob?->assignedTo?->name_initials;
    $isAssigned = filled($assignee) || ($stage['number'] === 8 && filled($currentJob?->delivery_accepted));
    $assigneeText = filled($assignee) ? $assignee : ($isAssigned ? 'Assigned' : 'Unassigned');
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

    $jobLabels = $stageJobs->map(function ($job): string {
        return collect([
            $job->unit_num,
            $job->jobType?->name,
            $job->material?->name,
            ($job->color && $job->color !== '0') ? $job->color : null,
        ])->filter(fn ($value): bool => filled($value))->implode(' · ');
    })->filter()->values();

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
         data-assignment="{{ $isAssigned ? 'assigned' : 'unassigned' }}"
         data-due-today="{{ $dueToday ? 'true' : 'false' }}"
         data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
    <button class="pc-case-card" type="button" data-pc-open-details aria-current="false">
        <div class="pc-case-card__top">
            <strong><span>#{{ $case->id }}</span> {{ $case->patient_name ?: 'Patient not provided' }}</strong>
            <span class="pc-state pc-state--{{ $state }}">{{ $state === 'active' ? 'In progress' : 'Ready' }}</span>
        </div>
        <p>{{ $case->client?->name ?? 'Doctor not assigned' }}</p>
        <ul>
            @forelse($jobLabels as $jobLabel)
                <li>{{ $jobLabel }}</li>
            @empty
                <li>No jobs listed for this stage.</li>
            @endforelse
        </ul>
        <div class="pc-case-card__footer">
            <span>{{ $deliveryText }}</span>
            <span>{{ $assigneeText }}</span>
        </div>
    </button>

    <template data-pc-detail-template>
        @include('cases.partials.production-control-details', [
            'case' => $case,
            'stage' => $stage,
            'state' => $state,
            'stageJobs' => $stageJobs,
            'assigneeText' => $assigneeText,
            'deliveryText' => $deliveryText,
            'canAssignEmployees' => $canAssignEmployees,
            'isAdmin' => $isAdmin,
            'cardKey' => $cardKey,
        ])
    </template>
</article>
