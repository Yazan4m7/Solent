@extends('layouts.app', [
    'pageSlug' => 'My Work',
    'class' => ' employee-production-board-v2-page',
])

@php
    $boardData = $board ?? [];

    if ($boardData instanceof \Illuminate\Contracts\Support\Arrayable) {
        $boardData = $boardData->toArray();
    }

    if (!is_array($boardData)) {
        $boardData = [];
    }

    $ui = trans('ui.dom');
    $epbText = static function (string $key, ?string $fallback = null) use ($ui): string {
        return is_array($ui) && isset($ui[$key]) ? (string) $ui[$key] : ($fallback ?? $key);
    };

    $selectedStage = (string) data_get($boardData, 'selected_stage', 'all');
    $selectedStage = $selectedStage !== '' ? $selectedStage : 'all';
    $detailsUrlTemplate = (string) (
        data_get($boardData, 'fragment_routes.details')
        ?? data_get($boardData, 'routes.details')
        ?? data_get($boardData, 'details_url_template')
        ?? ''
    );
    $refreshUrl = (string) (
        data_get($boardData, 'fragment_routes.refresh')
        ?? data_get($boardData, 'routes.refresh')
        ?? request()->fullUrl()
    );
@endphp

@push('css')
    <link href="{{ asset('assets/css/employee-production-board-v2.css') }}?v=20260823-2" rel="stylesheet">
@endpush

@section('content')
    <main class="epb-board"
          data-epb-root
          data-epb-selected-stage="{{ $selectedStage }}"
          data-epb-details-url-template="{{ $detailsUrlTemplate }}"
          data-epb-refresh-url="{{ $refreshUrl }}"
          data-epb-empty-active-message="{{ $epbText('No active cases in this stage.', 'No active cases in this stage.') }}"
          data-epb-empty-queue-message="{{ $epbText('No queued cases in this stage.', 'No queued cases in this stage.') }}"
          data-epb-busy-message="{{ $epbText('Another update is still being saved.', 'Another update is still being saved.') }}"
          data-epb-session-message="{{ $epbText('Your session has expired. Reload the page and try again.', 'Your session has expired. Reload the page and try again.') }}"
          data-epb-forbidden-message="{{ $epbText('This action is not available for this case.', 'This action is not available for this case.') }}"
          data-epb-error-message="{{ $epbText('The update could not be saved. Please try again.', 'The update could not be saved. Please try again.') }}"
          data-epb-processing-message="{{ $epbText('Processing your request...', 'Processing your request...') }}"
          data-epb-details-error-message="{{ $epbText('Case details could not be loaded.', 'Case details could not be loaded.') }}">
        <header class="epb-page-header">
            <div>
                <h1 id="epb-page-title">{{ $epbText('My Work', 'My Work') }}</h1>
                <p class="epb-muted">
                    {{ $epbText('Active cases and available work across your production stages.', 'Active cases and available work across your production stages.') }}
                </p>
            </div>
        </header>

        @include('generic.employee-production-board-v2._board', [
            'board' => $boardData,
            'selectedStage' => $selectedStage,
        ])

        @include('generic.employee-production-board-v2._sheet')

        <div class="epb-toast" data-epb-toast role="status" aria-live="polite" hidden></div>
    </main>
@endsection

@push('js')
    <script src="{{ asset('assets/js/ysh-custom-js/employeeProductionBoardV2.js') }}?v=20260823-2"></script>
@endpush
