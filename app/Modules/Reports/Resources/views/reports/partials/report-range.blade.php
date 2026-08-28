@php
    $rangeMonths = collect($selectedMonths ?? [])->filter()->sort()->values();
    $displayRangeStart = isset($reportRangeStart)
        ? \Carbon\Carbon::parse($reportRangeStart)->startOfDay()
        : ($rangeMonths->isNotEmpty()
            ? \Carbon\Carbon::parse($rangeMonths->first() . '-01')->startOfMonth()
            : now()->startOfMonth());
    $displayRangeEnd = isset($reportRangeEnd)
        ? \Carbon\Carbon::parse($reportRangeEnd)->endOfDay()
        : ($rangeMonths->isNotEmpty()
            ? \Carbon\Carbon::parse($rangeMonths->last() . '-01')->endOfMonth()
            : now()->endOfMonth());
@endphp

<div class="report-applied-range" aria-label="{{ trans('ui.dom')['Date Range:'] ?? 'Date Range:' }}">
    <i class="far fa-calendar" aria-hidden="true"></i>
    <span>{{ trans('ui.dom')['Date Range:'] ?? 'Date Range:' }}</span>
    <bdi dir="ltr">{{ $displayRangeStart->format('Y-m-d H:i') }} &mdash; {{ $displayRangeEnd->format('Y-m-d H:i') }}</bdi>
</div>
