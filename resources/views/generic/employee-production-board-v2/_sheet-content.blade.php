@php
    $sheetData = $sheet ?? $details ?? $item ?? $case ?? [];
    $ui = trans('ui.dom');
    $epbText = static function (string $key, ?string $fallback = null) use ($ui): string {
        return is_array($ui) && isset($ui[$key]) ? (string) $ui[$key] : ($fallback ?? $key);
    };

    $caseId = (string) (data_get($sheetData, 'case_id') ?? data_get($sheetData, 'id') ?? '');
    $displayId = (string) (
        data_get($sheetData, 'display_id')
        ?? data_get($sheetData, 'case_display_id')
        ?? data_get($sheetData, 'case_number')
        ?? ($caseId !== '' ? 'Case #' . $caseId : $epbText('Case details', 'Case details'))
    );
    $stageId = (string) (
        data_get($sheetData, 'stage_id')
        ?? data_get($sheetData, 'stage')
        ?? data_get($sheetData, 'stage.number')
        ?? ''
    );
    $stageLabel = (string) (
        data_get($sheetData, 'stage_label')
        ?? data_get($sheetData, 'stage_name')
        ?? data_get($sheetData, 'stage.label')
        ?? $stageId
    );
    $patient = (string) (data_get($sheetData, 'patient') ?? data_get($sheetData, 'patient_name') ?? $epbText('Not provided', 'Not provided'));
    $doctor = (string) (data_get($sheetData, 'doctor') ?? data_get($sheetData, 'doctor_name') ?? data_get($sheetData, 'client.name') ?? $epbText('Not assigned', 'Not assigned'));
    $delivery = (string) (data_get($sheetData, 'delivery') ?? data_get($sheetData, 'delivery_text') ?? $epbText('Not set', 'Not set'));
    $material = (string) (data_get($sheetData, 'material') ?? data_get($sheetData, 'material_text') ?? $epbText('Not set', 'Not set'));
    $shade = (string) (data_get($sheetData, 'shade') ?? data_get($sheetData, 'shade_text') ?? $epbText('Not set', 'Not set'));
    $jobs = collect(data_get($sheetData, 'jobs', []))->values();
    $files = collect(data_get($sheetData, 'files', data_get($sheetData, 'photos', [])))->values();
    $notes = collect(data_get($sheetData, 'notes', []))->values();
    $fullCaseUrl = (string) (
        data_get($sheetData, 'full_case_url')
        ?? data_get($sheetData, 'case_url')
        ?? data_get($sheetData, 'actions.view.url')
        ?? ''
    );

    $noteActionValue = data_get($sheetData, 'note_action')
        ?? data_get($sheetData, 'actions.note')
        ?? data_get($sheetData, 'note_url');
    $noteAction = is_string($noteActionValue) || $noteActionValue instanceof \Stringable
        ? (string) $noteActionValue
        : (string) (data_get($noteActionValue, 'url') ?? data_get($noteActionValue, 'action') ?? '');
    $noteDisabled = !($noteAction !== '')
        || filter_var(data_get($noteActionValue, 'disabled', false), FILTER_VALIDATE_BOOLEAN)
        || data_get($noteActionValue, 'enabled', true) === false;
    $noteReason = (string) (
        data_get($noteActionValue, 'reason')
        ?? data_get($noteActionValue, 'disabled_reason')
        ?? ''
    );

    $formatJob = static function ($job): array {
        if (is_scalar($job) || $job instanceof \Stringable) {
            return ['title' => trim((string) $job), 'details' => []];
        }

        $title = trim((string) (
            data_get($job, 'label')
            ?? data_get($job, 'unit_num')
            ?? data_get($job, 'units')
            ?? data_get($job, 'text')
            ?? ''
        ));
        $details = collect([
            data_get($job, 'job_type') ?? data_get($job, 'type') ?? data_get($job, 'jobType.name'),
            data_get($job, 'material') ?? data_get($job, 'material.name'),
            data_get($job, 'shade') ?? data_get($job, 'color'),
            data_get($job, 'style'),
        ])->filter(fn ($value): bool => filled($value))->map(fn ($value): string => (string) $value)->values()->all();

        return ['title' => $title, 'details' => $details];
    };

    $fileName = static function ($file): string {
        if (is_scalar($file) || $file instanceof \Stringable) {
            $value = (string) $file;
        } else {
            $value = (string) (
                data_get($file, 'name')
                ?? data_get($file, 'filename')
                ?? data_get($file, 'original_name')
                ?? data_get($file, 'path')
                ?? ''
            );
        }

        $normalized = str_replace('\\', '/', $value);
        return trim((string) basename($normalized));
    };
@endphp

<header class="epb-sheet__header"
        data-epb-sheet-case-id="{{ $caseId }}"
        data-epb-sheet-stage="{{ $stageId }}">
    <div>
        <h2 id="epb-sheet-title">{{ $displayId }}</h2>
        <p class="epb-muted epb-small">{{ $patient }} <span aria-hidden="true">·</span> {{ $doctor }}</p>
    </div>
    <button class="epb-icon-button"
            type="button"
            data-epb-sheet-close
            aria-label="{{ $epbText('Close details', 'Close details') }}">
        <span aria-hidden="true">&times;</span>
    </button>
</header>

<div class="epb-sheet__body">
    <section class="epb-detail-block" aria-label="{{ $epbText('Case information', 'Case information') }}">
        <div class="epb-detail-row"><span>{{ $epbText('Patient', 'Patient') }}</span><strong>{{ $patient }}</strong></div>
        <div class="epb-detail-row"><span>{{ $epbText('Doctor', 'Doctor') }}</span><strong>{{ $doctor }}</strong></div>
        <div class="epb-detail-row"><span>{{ $epbText('Production stage', 'Production stage') }}</span><strong>{{ $stageLabel ?: $epbText('Not set', 'Not set') }}</strong></div>
        <div class="epb-detail-row"><span>{{ $epbText('Delivery', 'Delivery') }}</span><strong>{{ $delivery }}</strong></div>
        <div class="epb-detail-row"><span>{{ $epbText('Material', 'Material') }}</span><strong>{{ $material }}</strong></div>
        <div class="epb-detail-row"><span>{{ $epbText('Shade', 'Shade') }}</span><strong>{{ $shade }}</strong></div>
    </section>

    <section class="epb-detail-block" aria-labelledby="epb-sheet-jobs-title">
        <div class="epb-detail-heading">
            <h3 id="epb-sheet-jobs-title">{{ $epbText('Jobs in this case', 'Jobs in this case') }}</h3>
            <span>{{ $jobs->count() }}</span>
        </div>
        <ul class="epb-sheet-job-list">
            @forelse($jobs as $job)
                @php
                    $jobData = $formatJob($job);
                @endphp
                <li>
                    <strong>{{ $jobData['title'] !== '' ? $jobData['title'] : $epbText('Job', 'Job') }}</strong>
                    @if(count($jobData['details']) > 0)
                        <span>{{ implode(' · ', $jobData['details']) }}</span>
                    @endif
                </li>
            @empty
                <li class="epb-sheet-list-empty">{{ $epbText('No jobs have been added to this case.', 'No jobs have been added to this case.') }}</li>
            @endforelse
        </ul>
    </section>

    <section class="epb-detail-block" aria-labelledby="epb-sheet-files-title">
        <div class="epb-detail-heading">
            <h3 id="epb-sheet-files-title">{{ $epbText('Files', 'Files') }}</h3>
            <span>{{ $files->count() }}</span>
        </div>
        <ul class="epb-file-list">
            @forelse($files as $file)
                @php
                    $name = $fileName($file);
                @endphp
                @if($name !== '')
                    <li>{{ $name }}</li>
                @endif
            @empty
                <li class="epb-sheet-list-empty">{{ $epbText('No files attached to this case.', 'No files attached to this case.') }}</li>
            @endforelse
        </ul>
    </section>

    <section class="epb-detail-block" aria-labelledby="epb-sheet-notes-title">
        <div class="epb-detail-heading">
            <h3 id="epb-sheet-notes-title">{{ $epbText('Case notes', 'Case notes') }}</h3>
            <span>{{ $notes->count() }}</span>
        </div>
        <ul class="epb-note-list">
            @forelse($notes as $note)
                @php
                    $noteText = (string) (data_get($note, 'text') ?? data_get($note, 'note') ?? '');
                    $noteAuthor = (string) (
                        data_get($note, 'author')
                        ?? data_get($note, 'written_by_name')
                        ?? data_get($note, 'writtenBy.name_initials')
                        ?? data_get($note, 'writtenBy.first_name')
                        ?? ''
                    );
                    $noteDate = (string) (data_get($note, 'created_at_text') ?? data_get($note, 'created_at') ?? '');
                @endphp
                <li>
                    @if($noteAuthor !== '' || $noteDate !== '')
                        <span class="epb-note-list__meta">
                            <strong>{{ $noteAuthor ?: $epbText('System', 'System') }}</strong>
                            @if($noteDate !== '')<time>{{ $noteDate }}</time>@endif
                        </span>
                    @endif
                    <p>{{ $noteText }}</p>
                </li>
            @empty
                <li class="epb-sheet-list-empty">{{ $epbText('No notes added to this case.', 'No notes added to this case.') }}</li>
            @endforelse
        </ul>
    </section>

    @if($noteAction !== '')
        <form class="epb-note-form"
              action="{{ $noteAction }}"
              method="POST"
              data-epb-mutation="note"
              data-epb-case-id="{{ $caseId }}"
              data-epb-stage="{{ $stageId }}">
            @csrf
            <input type="hidden" name="case_id" value="{{ $caseId }}">
            <input type="hidden" name="stage" value="{{ $stageId }}">
            <input type="hidden" name="idempotency_key" value="" data-epb-idempotency-key>

            <label for="epb-note-text-{{ preg_replace('/[^a-zA-Z0-9_-]+/', '-', $caseId . '-' . $stageId) }}">
                {{ $epbText('Add note', 'Add note') }}
            </label>
            <textarea id="epb-note-text-{{ preg_replace('/[^a-zA-Z0-9_-]+/', '-', $caseId . '-' . $stageId) }}"
                      name="note"
                      rows="4"
                      maxlength="255"
                      required
                      data-epb-note-input
                      placeholder="{{ $epbText('Write a note for this case', 'Write a note for this case') }}"
                      @if($noteDisabled) disabled @endif></textarea>
            @if($noteReason !== '')
                <p class="epb-card-action-reason">{{ $noteReason }}</p>
            @endif
            <p class="epb-form-error" data-epb-form-error role="alert" hidden></p>
            <div class="epb-note-form__actions">
                <button class="epb-button epb-button--primary"
                        type="submit"
                        data-epb-submit-button
                        data-epb-pending-label="{{ $epbText('Saving...', 'Saving...') }}"
                        @if($noteDisabled) disabled @endif>
                    <span data-epb-button-label>{{ $epbText('Save note', 'Save note') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>

<footer class="epb-sheet__footer">
    @if($fullCaseUrl !== '')
        <a class="epb-button epb-button--quiet" href="{{ $fullCaseUrl }}">
            {{ $epbText('Open full case', 'Open full case') }}
        </a>
    @endif
    <button class="epb-button" type="button" data-epb-sheet-close>
        {{ $epbText('Close', 'Close') }}
    </button>
</footer>
