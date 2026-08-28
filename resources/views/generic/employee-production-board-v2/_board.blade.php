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

    $selectedStage = (string) ($selectedStage ?? data_get($boardData, 'selected_stage', 'all'));
    $selectedStage = $selectedStage !== '' ? $selectedStage : 'all';
    $activeItems = collect(data_get($boardData, 'active', []))->values();
    $queueItems = collect(data_get($boardData, 'queue', []))->values();

    $epbItemStage = static function ($item): string {
        return (string) (
            data_get($item, 'stage_id')
            ?? data_get($item, 'stage')
            ?? data_get($item, 'stage.number')
            ?? ''
        );
    };

    $epbCountValue = static function ($value, int $fallback): int {
        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        if ($value instanceof \Countable || is_array($value)) {
            return count($value);
        }

        return $fallback;
    };

    $stages = collect(data_get($boardData, 'stages', []))
        ->map(static function ($stage, $key) use ($activeItems, $queueItems, $epbItemStage, $epbCountValue): array {
            if (is_scalar($stage)) {
                $stage = ['id' => $key, 'label' => (string) $stage];
            } elseif ($stage instanceof \Illuminate\Contracts\Support\Arrayable) {
                $stage = $stage->toArray();
            }

            $stageId = (string) (
                data_get($stage, 'id')
                ?? data_get($stage, 'stage')
                ?? data_get($stage, 'number')
                ?? $key
            );
            $stageLabel = (string) (
                data_get($stage, 'label')
                ?? data_get($stage, 'name')
                ?? $stageId
            );
            $activeFallback = $activeItems->filter(fn ($item): bool => $epbItemStage($item) === $stageId)->count();
            $queueFallback = $queueItems->filter(fn ($item): bool => $epbItemStage($item) === $stageId)->count();

            return [
                'id' => $stageId,
                'label' => $stageLabel,
                'active_count' => $epbCountValue(
                    data_get($stage, 'active_count') ?? data_get($stage, 'counts.active') ?? data_get($stage, 'active'),
                    $activeFallback
                ),
                'queue_count' => $epbCountValue(
                    data_get($stage, 'queue_count') ?? data_get($stage, 'counts.queue') ?? data_get($stage, 'queue'),
                    $queueFallback
                ),
            ];
        })
        ->filter(fn (array $stage): bool => $stage['id'] !== '' && strtolower($stage['id']) !== 'all')
        ->values();

    $activeTotal = $epbCountValue(data_get($boardData, 'summary.active'), $activeItems->count());
    $queueTotal = $epbCountValue(data_get($boardData, 'summary.queue'), $queueItems->count());
    $dueFallback = $activeItems->concat($queueItems)->filter(static function ($item): bool {
        return filter_var(data_get($item, 'due_today', false), FILTER_VALIDATE_BOOLEAN);
    })->count();
    $dueToday = $epbCountValue(data_get($boardData, 'summary.due_today'), $dueFallback);

    $activeVisibleCount = $selectedStage === 'all'
        ? $activeItems->count()
        : $activeItems->filter(fn ($item): bool => $epbItemStage($item) === $selectedStage)->count();
    $queueVisibleCount = $selectedStage === 'all'
        ? $queueItems->count()
        : $queueItems->filter(fn ($item): bool => $epbItemStage($item) === $selectedStage)->count();

    $detailsUrlTemplate = (string) (
        data_get($boardData, 'fragment_routes.details')
        ?? data_get($boardData, 'routes.details')
        ?? data_get($boardData, 'details_url_template')
        ?? ''
    );
@endphp

<div class="epb-board-fragment" id="epb-board-fragment" data-epb-board-fragment>
    <nav class="epb-stage-strip" aria-label="{{ $epbText('Filter cases by production stage', 'Filter cases by production stage') }}">
        <button class="epb-stage-card"
                type="button"
                data-epb-stage-button
                data-epb-stage="all"
                aria-pressed="{{ $selectedStage === 'all' ? 'true' : 'false' }}">
            <strong>{{ $epbText('All assigned stages', 'All assigned stages') }}</strong>
            <span class="epb-stage-counts">
                <span><b>{{ $activeTotal }}</b> {{ $epbText('active', 'active') }}</span>
                <span><b>{{ $queueTotal }}</b> {{ $epbText('queue', 'queue') }}</span>
            </span>
        </button>

        @foreach($stages as $stage)
            <button class="epb-stage-card"
                    type="button"
                    data-epb-stage-button
                    data-epb-stage="{{ $stage['id'] }}"
                    aria-pressed="{{ $selectedStage === $stage['id'] ? 'true' : 'false' }}">
                <strong>{{ $stage['label'] }}</strong>
                <span class="epb-stage-counts">
                    <span><b>{{ $stage['active_count'] }}</b> {{ $epbText('active', 'active') }}</span>
                    <span><b>{{ $stage['queue_count'] }}</b> {{ $epbText('queue', 'queue') }}</span>
                </span>
            </button>
        @endforeach
    </nav>

    <div class="epb-work-summary" aria-label="{{ $epbText('Work summary', 'Work summary') }}">
        <span class="epb-summary-card epb-summary-card--active">
            <strong>{{ $activeTotal }}</strong>
            <small>{{ $epbText('active cases', 'active cases') }}</small>
        </span>
        <span class="epb-summary-card epb-summary-card--queue">
            <strong>{{ $queueTotal }}</strong>
            <small>{{ $epbText('cases in work queue', 'cases in work queue') }}</small>
        </span>
        <span class="epb-summary-card epb-summary-card--today">
            <strong>{{ $dueToday }}</strong>
            <small>{{ $epbText('due today', 'due today') }}</small>
        </span>
    </div>

    <section class="epb-section epb-section--active" aria-labelledby="epb-active-title">
        <header class="epb-section-header">
            <div>
                <h2 id="epb-active-title">{{ $epbText('Active cases', 'Active cases') }}</h2>
                <p class="epb-muted epb-small">{{ $epbText('All cases you are currently working on.', 'All cases you are currently working on.') }}</p>
            </div>
            <span class="epb-section-count" data-epb-visible-count="active">{{ $activeVisibleCount }}</span>
        </header>
        <div class="epb-grid-scroll">
            <div class="epb-case-grid" data-epb-grid="active">
                @foreach($activeItems as $item)
                    @include('generic.employee-production-board-v2._case-card', [
                        'item' => $item,
                        'group' => 'active',
                        'selectedStage' => $selectedStage,
                        'detailsUrlTemplate' => $detailsUrlTemplate,
                    ])
                @endforeach

                <div class="epb-empty-state"
                     data-epb-empty-state="active"
                     @if($activeVisibleCount > 0) hidden @endif>
                    {{ $epbText('No active cases in this stage.', 'No active cases in this stage.') }}
                </div>
            </div>
        </div>
    </section>

    <section class="epb-section epb-section--queue" aria-labelledby="epb-queue-title">
        <header class="epb-section-header">
            <div>
                <h2 id="epb-queue-title">{{ $epbText('Work queue', 'Work queue') }}</h2>
                <p class="epb-muted epb-small">{{ $epbText('Cases available to start in your assigned stages.', 'Cases available to start in your assigned stages.') }}</p>
            </div>
            <span class="epb-section-count" data-epb-visible-count="queue">{{ $queueVisibleCount }}</span>
        </header>
        <div class="epb-grid-scroll">
            <div class="epb-case-grid" data-epb-grid="queue">
                @foreach($queueItems as $item)
                    @include('generic.employee-production-board-v2._case-card', [
                        'item' => $item,
                        'group' => 'queue',
                        'selectedStage' => $selectedStage,
                        'detailsUrlTemplate' => $detailsUrlTemplate,
                    ])
                @endforeach

                <div class="epb-empty-state"
                     data-epb-empty-state="queue"
                     @if($queueVisibleCount > 0) hidden @endif>
                    {{ $epbText('No queued cases in this stage.', 'No queued cases in this stage.') }}
                </div>
            </div>
        </div>
    </section>
</div>
