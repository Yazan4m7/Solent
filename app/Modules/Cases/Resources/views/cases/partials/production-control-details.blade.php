<div class="pc-detail" data-pc-detail-fragment>
    <header class="pc-detail-header">
        <div class="pc-detail-header__copy">
            <span class="pc-detail-stage pc-detail-stage--{{ $state }}">
                <span class="pc-detail-stage__icon" aria-hidden="true"><i class="fa-solid {{ $stage['icon'] }}"></i></span>
                <span>
                    <small>{{ $stage['label'] }}</small>
                    <strong><i aria-hidden="true"></i>{{ $state === 'active' ? 'In progress' : 'Ready' }}</strong>
                </span>
            </span>
            <h2 id="pc-detail-title">{{ $case->patient_name ?: 'Patient not provided' }}</h2>
            <p>Case #{{ $case->id }}</p>
        </div>
        <button class="pc-detail-close" type="button" data-pc-detail-close aria-label="Close details"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    </header>

    <div class="pc-detail-body">
        <dl class="pc-detail-grid">
            <div><i class="fa-solid fa-user-doctor" aria-hidden="true"></i><span><dt>Doctor</dt><dd>{{ $case->client?->name ?? 'Not assigned' }}</dd></span></div>
            <div><i class="fa-regular fa-calendar" aria-hidden="true"></i><span><dt>Delivery</dt><dd>{{ $deliveryText }}</dd></span></div>
            <div><i class="fa-regular fa-user" aria-hidden="true"></i><span><dt>Assigned to</dt><dd>{{ $assigneeText }}</dd></span></div>
            <div><i class="fa-solid fa-layer-group" aria-hidden="true"></i><span><dt>Stage</dt><dd>{{ $stage['label'] }}</dd></span></div>
        </dl>

        <section class="pc-detail-section">
            <div class="pc-detail-section__heading"><h3><i class="fa-solid fa-tooth" aria-hidden="true"></i>Jobs</h3><span>{{ $stageJobs->count() }}</span></div>
            <div class="pc-detail-jobs">
                @forelse($stageJobs as $job)
                    <article>
                        <span class="pc-detail-job__icon" aria-hidden="true"><i class="fa-solid fa-cube"></i></span>
                        <span>
                            <strong>{{ $job->unit_num ?: 'No unit number' }} · {{ $job->jobType?->name ?? 'Job' }}</strong>
                            <small>{{ collect([$job->material?->name, ($job->color && $job->color !== '0') ? $job->color : null, ($job->style && $job->style !== 'None') ? $job->style : null])->filter()->implode(' · ') ?: 'No additional details' }}</small>
                        </span>
                    </article>
                @empty
                    <p class="pc-detail-empty">No jobs listed for this stage.</p>
                @endforelse
            </div>
        </section>

        @if($case->notes->isNotEmpty())
            <section class="pc-detail-section">
                <div class="pc-detail-section__heading"><h3><i class="fa-regular fa-note-sticky" aria-hidden="true"></i>Recent notes</h3><span>{{ $case->notes->count() }}</span></div>
                <div class="pc-detail-notes">
                    @foreach($case->notes->sortByDesc('created_at')->take(3) as $note)
                        <article><span class="pc-detail-note__avatar">{{ \Illuminate\Support\Str::substr($note->writtenBy?->name_initials ?? 'S', 0, 2) }}</span><span><strong>{{ $note->writtenBy?->name_initials ?? 'System' }}</strong><p>{{ $note->note }}</p></span></article>
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
                <label for="pc-employee-{{ $cardKey }}"><i class="fa-solid fa-user-plus" aria-hidden="true"></i>Assign employee</label>
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

    </div>

    <section class="pc-detail-action-zone" aria-label="Case actions">
        <div class="pc-detail-actions">
            <div class="pc-detail-actions__workflow">
                @if($state === 'waiting')
                    <a class="pc-button pc-button--primary"
                       data-pc-processing
                       href="{{ $stage['number'] === 8 ? route('delivery-accept', $case->id) : route('assign-to-me', ['caseId' => $case->id, 'stage' => $stage['number']]) }}">
                        <i class="fa-solid {{ $stage['number'] === 8 ? 'fa-handshake' : 'fa-user-check' }}" aria-hidden="true"></i>
                        {{ $stage['number'] === 8 ? 'Accept' : 'Assign to me' }}
                    </a>
                    @if($stage['number'] === 7)
                        <a class="pc-button" data-pc-processing data-pc-confirm="Assign this case to you and complete Quality Control?" href="{{ route('assign-and-finish', ['caseId' => $case->id, 'stage' => $stage['number']]) }}"><i class="fa-solid fa-check-double" aria-hidden="true"></i>Assign &amp; complete</a>
                    @endif
                @else
                    <a class="pc-button pc-button--primary"
                       data-pc-processing
                       data-pc-confirm="Complete this case in {{ $stage['label'] }}?"
                       href="{{ $isAdmin ? route('complete-by-admin', ['id' => $case->id, 'stage' => $stage['number']]) : route('finish-case', ['caseId' => $case->id, 'stage' => $stage['number']]) }}">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        {{ $stage['number'] === 8 ? 'Complete delivery' : 'Complete stage' }}
                    </a>
                    <a class="pc-button pc-button--reset" data-pc-processing data-pc-confirm="Reset this case to the ready queue?" href="{{ route('reset-to-waiting', ['id' => $case->id, 'stage' => $stage['number']]) }}"><i class="fa-solid fa-arrow-rotate-left" aria-hidden="true"></i>Reset to ready</a>
                @endif
            </div>
            <div class="pc-detail-actions__support">
                <button class="pc-button pc-button--note"
                        type="button"
                        data-pc-note-toggle
                        aria-controls="pc-note-composer-{{ $cardKey }}"
                        aria-expanded="false">
                    <i class="fa-regular fa-note-sticky" aria-hidden="true"></i>Add note
                </button>
                <a class="pc-button pc-button--quiet" href="{{ route('view-case', ['id' => $case->id, 'stage' => $stage['number']]) }}"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>Open case</a>
            </div>
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
                <button class="pc-button pc-button--primary" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i>Save note</button>
            </div>
        </form>
    </section>
</div>
