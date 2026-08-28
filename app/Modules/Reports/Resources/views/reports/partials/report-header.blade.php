@php
    $reportNavigation = [
        ['route' => 'num-of-units-report', 'label' => 'Units Summary', 'icon' => 'fas fa-layer-group'],
        ['route' => 'job-types-report', 'label' => 'Job Mix', 'icon' => 'fas fa-briefcase'],
        ['route' => 'QC-report', 'label' => 'QC Summary', 'icon' => 'fas fa-shield-alt'],
        ['route' => 'repeats-report', 'label' => 'Remakes', 'icon' => 'fas fa-redo-alt'],
        ['route' => 'materials-report', 'label' => 'Materials Usage', 'icon' => 'fas fa-cubes'],
    ];
    $currentReportRoute = optional(request()->route())->getName();
@endphp

<header class="report-page-header">
    <div class="report-page-heading">
        <span class="report-page-icon" aria-hidden="true">
            <i class="{{ $icon ?? 'fas fa-chart-bar' }}"></i>
        </span>
        <div class="report-page-heading-copy">
            <span class="report-page-eyebrow">Reports</span>
            <h1>{{ $title ?? 'Report' }}</h1>
            @if(!empty($description))
                <p>{{ $description }}</p>
            @endif
        </div>
    </div>

    <nav class="report-page-navigation" aria-label="Reports">
        @foreach($reportNavigation as $reportNavigationItem)
            @php($isCurrentReport = $currentReportRoute === $reportNavigationItem['route'])
            <a href="{{ route($reportNavigationItem['route']) }}"
               class="report-page-navigation-link{{ $isCurrentReport ? ' is-active' : '' }}"
               @if($isCurrentReport) aria-current="page" @endif>
                <i class="{{ $reportNavigationItem['icon'] }}" aria-hidden="true"></i>
                <span>{{ $reportNavigationItem['label'] }}</span>
            </a>
        @endforeach
    </nav>
</header>
