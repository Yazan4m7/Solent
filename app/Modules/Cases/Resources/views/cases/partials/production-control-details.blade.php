<div class="pc-detail" data-pc-detail-fragment>
    <header class="pc-detail-header">
        <div>
            <span>{{ $stage['label'] }} · {{ $state === 'active' ? 'In progress' : 'Ready' }}</span>
            <h2 id="pc-detail-title">#{{ $case->id }} {{ $case->patient_name ?: 'Patient not provided' }}</h2>
        </div>
        <button class="pc-detail-close" type="button" data-pc-detail-close aria-label="Close details">&times;</button>
    </header>

    <div class="pc-detail-body">
        <dl class="pc-detail-grid">
            <div><dt>Doctor</dt><dd>{{ $case->client?->name ?? 'Not assigned' }}</dd></div>
            <div><dt>Delivery</dt><dd>{{ $deliveryText }}</dd></div>
            <div><dt>Assigned to</dt><dd>{{ $assigneeText }}</dd></div>
            <div><dt>Stage</dt><dd>{{ $stage['label'] }}</dd></div>
        </dl>

        <section class="pc-detail-section">
            <div class="pc-detail-section__heading"><h3>Jobs</h3><span>{{ $stageJobs->count() }}</span></div>
            <div class="pc-detail-jobs">
                @forelse($stageJobs as $job)
                    <article>
                        <strong>{{ $job->unit_num ?: 'No unit number' }} · {{ $job->jobType?->name ?? 'Job' }}</strong>
                        <span>{{ collect([$job->material?->name, ($job->color && $job->color !== '0') ? $job->color : null, ($job->style && $job->style !== 'None') ? $job->style : null])->filter()->implode(' · ') }}</span>
                    </article>
                @empty
                    <p class="pc-detail-empty">No jobs listed for this stage.</p>
                @endforelse
            </div>
        </section>

        @if($case->notes->isNotEmpty())
            <section class="pc-detail-section">
                <div class="pc-detail-section__heading"><h3>Recent notes</h3><span>{{ $case->notes->count() }}</span></div>
                <div class="pc-detail-notes">
                    @foreach($case->notes->sortByDesc('created_at')->take(3) as $note)
                        <article><strong>{{ $note->writtenBy?->name_initials ?? 'System' }}</strong><p>{{ $note->note }}</p></article>
                    @endforeach
                </div>
            </section>
        @endif

        @if($state === 'waiting' && $canAssignEmployees && $stage['employees']->isNotEmpty())
            <form class="pc-assignment-form" action="{{ route('assign-to-stage-employee') }}" method="POST" data-pc-processing>
                @csrf
                <input type="hidden" name="case_id" value="{{ $case->id }}">
                <input type="hidden" name="stage" value="{{ $stage['number'] }}">
                <input type="hidden" name="stage_name" value="{{ $stage['label'] }}">
                <label for="pc-employee-{{ $cardKey }}">Assign employee</label>
                <div>
                    <select id="pc-employee-{{ $cardKey }}" name="employee_id" required>
                        <option value="">Choose employee</option>
                        @foreach($stage['employees'] as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name_initials ?: trim($employee->first_name . ' ' . $employee->last_name) }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Assign</button>
                </div>
            </form>
        @endif

        <section class="pc-detail-action-zone" aria-label="Case actions">
            <div class="pc-detail-actions">
                <button class="pc-button pc-button--note"
                        type="button"
                        data-pc-note-toggle
                        aria-controls="pc-note-composer-{{ $cardKey }}"
                        aria-expanded="false">
                    Add note
                </button>
                <a class="pc-button pc-button--quiet" href="{{ route('view-case', ['id' => $case->id, 'stage' => $stage['number']]) }}">Open case</a>

                @if($state === 'waiting')
                    <a class="pc-button pc-button--primary"
                       data-pc-processing
                       href="{{ $stage['number'] === 8 ? route('delivery-accept', $case->id) : route('assign-to-me', ['caseId' => $case->id, 'stage' => $stage['number']]) }}">
                        {{ $stage['number'] === 8 ? 'Accept' : 'Assign to me' }}
                    </a>
                    @if($stage['number'] === 7)
                        <a class="pc-button" data-pc-processing data-pc-confirm="Assign this case to you and complete Quality Control?" href="{{ route('assign-and-finish', ['caseId' => $case->id, 'stage' => $stage['number']]) }}">Assign &amp; complete</a>
                    @endif
                @else
                    <a class="pc-button pc-button--primary"
                       data-pc-processing
                       data-pc-confirm="Complete this case in {{ $stage['label'] }}?"
                       href="{{ $isAdmin ? route('complete-by-admin', ['id' => $case->id, 'stage' => $stage['number']]) : route('finish-case', ['caseId' => $case->id, 'stage' => $stage['number']]) }}">
                        {{ $stage['number'] === 8 ? 'Complete delivery' : 'Complete stage' }}
                    </a>
                    <a class="pc-button" data-pc-processing data-pc-confirm="Reset this case to the ready queue?" href="{{ route('reset-to-waiting', ['id' => $case->id, 'stage' => $stage['number']]) }}">Reset to ready</a>
                @endif
            </div>

            <form id="pc-note-composer-{{ $cardKey }}"
                  class="pc-note-composer"
                  action="{{ route('production-control.notes.store', ['caseId' => $case->id, 'stage' => $stage['number']]) }}"
                  method="POST"
                  data-pc-note-form
                  data-pc-processing
                  hidden>
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                <label for="pc-note-{{ $cardKey }}">Add a note to case #{{ $case->id }}</label>
                <textarea id="pc-note-{{ $cardKey }}"
                          name="note"
                          rows="3"
                          maxlength="255"
                          required
                          data-pc-note-input
                          placeholder="Write a short production note"></textarea>
                <div class="pc-note-composer__actions">
                    <button class="pc-button pc-button--quiet" type="button" data-pc-note-cancel>Cancel</button>
                    <button class="pc-button pc-button--primary" type="submit">Save note</button>
                </div>
            </form>
        </section>
    </div>
</div>
