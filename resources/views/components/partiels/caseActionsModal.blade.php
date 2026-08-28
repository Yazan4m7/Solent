@props([
    'case',
    'modalId' => null,
    'showCaseManagement' => false,
    'allowDeliveryDateChange' => false,
    'deliveryDateModalId' => null,
    'trashed' => false,
])

@php
    $modalId = $modalId ?: 'case-actions-' . $case->id;
    $permissions = Cache::get('user' . Auth::id());
    $firstJob = $case->jobs->first();
    $isAdmin = Auth::user()->is_admin;
    $canEditCase = !$case->locked && (
        $isAdmin ||
        ($permissions && $permissions->contains('permission_id', 102)) ||
        ($permissions && !$case->actual_delivery_date && $permissions->contains('permission_id', 115)) ||
        ($permissions && $firstJob && $firstJob->stage == 1 && $permissions->contains('permission_id', 1))
    );
    $canManageLock = $showCaseManagement && ($isAdmin || ($permissions && $permissions->contains('permission_id', 130)));
    $canReject = $showCaseManagement && !$case->locked && $case->actual_delivery_date &&
        ($isAdmin || ($permissions && $permissions->contains('permission_id', 116)));
    $canRepeat = $showCaseManagement && !$case->locked && $case->actual_delivery_date &&
        ($isAdmin || ($permissions && $permissions->contains('permission_id', 117)));
    $canModify = $showCaseManagement && !$case->locked && $case->actual_delivery_date &&
        ($isAdmin || ($permissions && $permissions->contains('permission_id', 118)));
    $canRedo = $showCaseManagement && !$case->locked && $case->delivered_to_client == 1 &&
        ($isAdmin || ($permissions && $permissions->contains('permission_id', 119)));
    $canChangeDeliveryDate = $allowDeliveryDateChange &&
        ($isAdmin || ($permissions && $permissions->contains('permission_id', 110)));
    $deliveryDate = $case->initial_delivery_date ? \Carbon\Carbon::parse($case->initial_delivery_date)->format('d M Y, g:i a') : 'Not set';
@endphp

<div class="modal fade solent-case-actions-modal" id="{{ $modalId }}" tabindex="-1" role="dialog"
     aria-labelledby="{{ $modalId }}-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="solent-case-actions-modal__eyebrow">Case details</span>
                    <h5 class="modal-title" id="{{ $modalId }}-title">{{ $case->case_id ?: 'Case #' . $case->id }}</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <section class="solent-case-actions-modal__summary" aria-label="Case summary">
                    <div>
                        <span>Doctor</span>
                        <strong>{{ $case->client?->name ?? 'Not assigned' }}</strong>
                    </div>
                    <div>
                        <span>Patient</span>
                        <strong>{{ $case->patient_name ?: 'Not provided' }}</strong>
                    </div>
                    <div>
                        <span>Delivery date</span>
                        <strong>{{ $deliveryDate }}</strong>
                    </div>
                    <div>
                        <span>Status</span>
                        <strong>{{ $case->status() }}</strong>
                    </div>
                </section>

                <section class="solent-case-actions-modal__section" aria-labelledby="{{ $modalId }}-jobs">
                    <div class="solent-case-actions-modal__section-heading">
                        <h6 id="{{ $modalId }}-jobs">Jobs</h6>
                        <span>{{ $case->jobs->count() }}</span>
                    </div>
                    <div class="solent-case-actions-modal__jobs">
                        @forelse($case->jobs as $job)
                            @php
                                $isImplantJob = $job->jobType?->id == 6;
                            @endphp
                            <article class="solent-case-actions-modal__job">
                                <strong>{{ $job->unit_num ?: 'No unit number' }}</strong>
                                <div class="solent-case-actions-modal__job-details">
                                    <span>{{ $job->jobType?->name ?? 'No job type' }} &middot; {{ $job->material?->name ?? 'No material' }}</span>
                                    @if($job->color && $job->color !== '0')
                                        <small>{{ $job->color }}</small>
                                    @endif
                                    @if($job->style && $job->style !== 'None')
                                        <small>{{ $job->style }}</small>
                                    @endif
                                    @if($isImplantJob && $job->implantR)
                                        <small>Implant: {{ $job->implantR->name }}</small>
                                    @endif
                                    @if($isImplantJob && $job->abutmentR)
                                        <small>Abutment: {{ $job->abutmentR->name }}</small>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="solent-case-actions-modal__empty">No jobs have been added to this case.</p>
                        @endforelse
                    </div>
                </section>

                @if($case->notes->isNotEmpty())
                    <section class="solent-case-actions-modal__section" aria-labelledby="{{ $modalId }}-notes">
                        <div class="solent-case-actions-modal__section-heading">
                            <h6 id="{{ $modalId }}-notes">Notes</h6>
                            <span>{{ $case->notes->count() }}</span>
                        </div>
                        <div class="solent-case-actions-modal__notes">
                            @foreach($case->notes as $note)
                                <article class="solent-case-actions-modal__note">
                                    <div>
                                        <strong>{{ $note->writtenBy?->name_initials ?? 'System' }}</strong>
                                        <time datetime="{{ $note->created_at }}">{{ substr($note->created_at, 0, 16) }}</time>
                                    </div>
                                    <p>{{ $note->note }}</p>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <div class="modal-footer">
                <div class="solent-case-actions-modal__actions">
                    @if($trashed)
                        <a class="btn btn-primary solent-case-action--restore" href="{{ route('restore-case', $case->id) }}">Restore case</a>
                    @else
                        @isset($operationsActions)
                            {{ $operationsActions }}
                        @endisset
                        <a class="btn btn-outline-secondary solent-case-action--voucher" href="{{ route('view-voucher', $case->id) }}">
                            <i class="fas fa-print" aria-hidden="true"></i> View voucher
                        </a>
                        <a class="btn btn-primary solent-case-action--view" href="{{ route('view-case', ['id' => $case->id, 'stage' => -2]) }}">
                            <i class="far fa-file-alt" aria-hidden="true"></i> View case
                        </a>
                        @if($canEditCase)
                            <a class="btn btn-outline-primary solent-case-action--edit" href="{{ route('edit-case-view', $case->id) }}">
                                <i class="fas fa-pen" aria-hidden="true"></i> Edit case
                            </a>
                        @endif
                        @if($canChangeDeliveryDate && $deliveryDateModalId)
                            <button type="button" class="btn btn-outline-secondary solent-case-action--delivery" data-dismiss="modal"
                                    data-toggle="modal" data-target="#{{ $deliveryDateModalId }}">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> Delivery date
                            </button>
                        @endif
                        @if($canManageLock)
                            <a class="btn btn-outline-secondary solent-case-action--lock" href="{{ route($case->locked ? 'unlock-case' : 'lock-case', $case->id) }}">
                                <i class="fas {{ $case->locked ? 'fa-lock-open' : 'fa-lock' }}" aria-hidden="true"></i>
                                {{ $case->locked ? 'Unlock case' : 'Lock case' }}
                            </a>
                        @endif
                        @if($isAdmin && $showCaseManagement && !$case->locked)
                            <a class="btn btn-outline-danger solent-case-action--delete" data-clientName="{{ $case->client?->name }}"
                               data-patientName="{{ $case->patient_name }}" onclick="caseDelConfirmation(event)"
                               href="{{ route('delete-case', $case->id) }}">
                                <i class="fas fa-trash" aria-hidden="true"></i> Delete case
                            </a>
                        @endif
                        @if($canReject)
                            <a class="btn btn-outline-danger solent-case-action--reject" href="{{ route('reject-case-view', $case->id) }}">Reject case</a>
                        @endif
                        @if($canRepeat)
                            <a class="btn btn-outline-secondary solent-case-action--repeat" href="{{ route('repeat-case-view', $case->id) }}">Repeat case</a>
                        @endif
                        @if($canModify)
                            <a class="btn btn-outline-secondary solent-case-action--modify" href="{{ route('modify-case-view', $case->id) }}">Modify case</a>
                        @endif
                        @if($canRedo)
                            <a class="btn btn-outline-secondary solent-case-action--redo" href="{{ route('redo-case-view', $case->id) }}">Redo case</a>
                        @endif
                    @endif
                    <button type="button" class="btn btn-light solent-case-action--close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
