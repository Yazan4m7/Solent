@php
    $ui = trans('ui.dom');
    $epbText = static function (string $key, ?string $fallback = null) use ($ui): string {
        return is_array($ui) && isset($ui[$key]) ? (string) $ui[$key] : ($fallback ?? $key);
    };

    $caseId = (string) (data_get($item, 'case_id') ?? data_get($item, 'id') ?? '');
    $stageId = (string) (
        data_get($item, 'stage_id')
        ?? data_get($item, 'stage')
        ?? data_get($item, 'stage.number')
        ?? ''
    );
    $stageLabel = (string) (
        data_get($item, 'stage_label')
        ?? data_get($item, 'stage_name')
        ?? data_get($item, 'stage.label')
        ?? $stageId
    );
    $patient = (string) (data_get($item, 'patient') ?? data_get($item, 'patient_name') ?? $epbText('Not provided', 'Not provided'));
    $doctor = (string) (data_get($item, 'doctor') ?? data_get($item, 'doctor_name') ?? data_get($item, 'client.name') ?? $epbText('Not assigned', 'Not assigned'));
    $jobs = collect(data_get($item, 'jobs', []))->values();
    $selectedStage = (string) ($selectedStage ?? 'all');
    $isVisible = $selectedStage === 'all' || $selectedStage === $stageId;
    $cardKey = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $caseId . '-' . $stageId);

    $detailsUrl = (string) (
        data_get($item, 'details_url')
        ?? data_get($item, 'detail_url')
        ?? data_get($item, 'actions.details.url')
        ?? ''
    );

    if ($detailsUrl === '' && !empty($detailsUrlTemplate)) {
        $detailsUrl = str_replace(
            ['{case_id}', '{case}', '__CASE__', '{stage_id}', '{stage}', '__STAGE__'],
            [rawurlencode($caseId), rawurlencode($caseId), rawurlencode($caseId), rawurlencode($stageId), rawurlencode($stageId), rawurlencode($stageId)],
            (string) $detailsUrlTemplate
        );
    }

    $normalizeAction = static function ($action, string $defaultLabel): ?array {
        if (is_string($action) || $action instanceof \Stringable) {
            $url = trim((string) $action);
            return $url === '' ? null : [
                'url' => $url,
                'label' => $defaultLabel,
                'pending_label' => $defaultLabel . '...',
                'confirm' => '',
                'disabled' => false,
                'reason' => '',
            ];
        }

        if ($action instanceof \Illuminate\Contracts\Support\Arrayable) {
            $action = $action->toArray();
        }

        if (!is_array($action)) {
            return null;
        }

        $url = trim((string) (data_get($action, 'url') ?? data_get($action, 'action') ?? ''));
        if ($url === '') {
            return null;
        }

        $enabled = data_get($action, 'enabled', true);

        return [
            'url' => $url,
            'label' => (string) (data_get($action, 'label') ?? $defaultLabel),
            'pending_label' => (string) (data_get($action, 'pending_label') ?? ($defaultLabel . '...')),
            'confirm' => (string) (data_get($action, 'confirm') ?? data_get($action, 'confirmation') ?? ''),
            'disabled' => filter_var(data_get($action, 'disabled', false), FILTER_VALIDATE_BOOLEAN) || $enabled === false,
            'reason' => (string) (data_get($action, 'reason') ?? data_get($action, 'disabled_reason') ?? ''),
        ];
    };

    $startAction = $normalizeAction(
        data_get($item, 'start_action') ?? data_get($item, 'actions.start') ?? data_get($item, 'start_url'),
        $epbText('Start', 'Start')
    );
    $completeAction = $normalizeAction(
        data_get($item, 'complete_action') ?? data_get($item, 'actions.complete') ?? data_get($item, 'complete_url'),
        $epbText('Complete', 'Complete')
    );

    if ($completeAction && $completeAction['confirm'] === '') {
        $completeAction['confirm'] = $epbText(
            'Complete this case and send it to the next stage?',
            'Complete this case and send it to the next stage?'
        );
    }

    $actionReason = $group === 'queue'
        ? (string) data_get($startAction, 'reason', '')
        : (string) data_get($completeAction, 'reason', '');

    $formatJob = static function ($job): string {
        if (is_scalar($job) || $job instanceof \Stringable) {
            return trim((string) $job);
        }

        $label = trim((string) (data_get($job, 'label') ?? data_get($job, 'text') ?? ''));
        if ($label !== '') {
            return $label;
        }

        return collect([
            data_get($job, 'unit_num') ?? data_get($job, 'units'),
            data_get($job, 'job_type') ?? data_get($job, 'type') ?? data_get($job, 'jobType.name'),
            data_get($job, 'material') ?? data_get($job, 'material.name'),
        ])->filter(fn ($value): bool => filled($value))->implode(' · ');
    };
@endphp

<article class="epb-case-card"
         id="epb-card-{{ $cardKey }}"
         data-epb-case-card
         data-epb-case-id="{{ $caseId }}"
         data-epb-stage="{{ $stageId }}"
         data-epb-group="{{ $group }}"
         @if(!$isVisible) hidden @endif>
    <div class="epb-case-card__top">
        <div class="epb-case-card__identity">
            <strong><span class="epb-case-card__id">#{{ ltrim($caseId, '#') }}</span> {{ $patient }}</strong>
            <span>{{ $doctor }}</span>
        </div>
        @if($stageLabel !== '')
            <span class="epb-stage-label">{{ $stageLabel }}</span>
        @endif
    </div>

    <ul class="epb-job-list" aria-label="{{ $epbText('Jobs in this case', 'Jobs in this case') }}">
        @forelse($jobs as $job)
            @php
                $jobLabel = $formatJob($job);
            @endphp
            @if($jobLabel !== '')
                <li>{{ $jobLabel }}</li>
            @endif
        @empty
            <li class="epb-job-list__empty">{{ $epbText('No jobs listed for this stage.', 'No jobs listed for this stage.') }}</li>
        @endforelse
    </ul>

    <div class="epb-case-card__actions">
        <button class="epb-button epb-button--quiet"
                type="button"
                data-epb-open-details
                data-epb-details-url="{{ $detailsUrl }}"
                data-epb-case-id="{{ $caseId }}"
                data-epb-stage="{{ $stageId }}"
                @if($detailsUrl === '') disabled @endif>
            {{ $epbText('Details', 'Details') }}
        </button>

        <button class="epb-button"
                type="button"
                data-epb-open-details
                data-epb-focus-note="true"
                data-epb-details-url="{{ $detailsUrl }}"
                data-epb-case-id="{{ $caseId }}"
                data-epb-stage="{{ $stageId }}"
                @if($detailsUrl === '') disabled @endif>
            {{ $epbText('Add note', 'Add note') }}
        </button>

        @if($group === 'queue' && $startAction)
            <form action="{{ $startAction['url'] }}"
                  method="POST"
                  data-epb-mutation="start"
                  data-epb-case-id="{{ $caseId }}"
                  data-epb-stage="{{ $stageId }}"
                  @if($startAction['confirm'] !== '') data-epb-confirm="{{ $startAction['confirm'] }}" @endif>
                @csrf
                <input type="hidden" name="case_id" value="{{ $caseId }}">
                <input type="hidden" name="stage" value="{{ $stageId }}">
                <input type="hidden" name="idempotency_key" value="" data-epb-idempotency-key>
                <button class="epb-button epb-button--primary"
                        type="submit"
                        data-epb-submit-button
                        data-epb-pending-label="{{ $startAction['pending_label'] }}"
                        @if($startAction['disabled']) disabled @endif>
                    <span data-epb-button-label>{{ $startAction['label'] }}</span>
                </button>
            </form>
        @elseif($group === 'active' && $completeAction)
            <form action="{{ $completeAction['url'] }}"
                  method="POST"
                  data-epb-mutation="complete"
                  data-epb-case-id="{{ $caseId }}"
                  data-epb-stage="{{ $stageId }}"
                  data-epb-confirm="{{ $completeAction['confirm'] }}">
                @csrf
                <input type="hidden" name="case_id" value="{{ $caseId }}">
                <input type="hidden" name="stage" value="{{ $stageId }}">
                <input type="hidden" name="idempotency_key" value="" data-epb-idempotency-key>
                <button class="epb-button epb-button--primary"
                        type="submit"
                        data-epb-submit-button
                        data-epb-pending-label="{{ $completeAction['pending_label'] }}"
                        @if($completeAction['disabled']) disabled @endif>
                    <span data-epb-button-label>{{ $completeAction['label'] }}</span>
                </button>
            </form>
        @endif
    </div>

    @if($actionReason !== '')
        <p class="epb-card-action-reason">{{ $actionReason }}</p>
    @endif

    <p class="epb-form-error" data-epb-form-error role="alert" hidden></p>
</article>
