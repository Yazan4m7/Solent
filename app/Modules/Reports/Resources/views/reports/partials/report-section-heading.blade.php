<div class="report-section-heading{{ !empty($compact) ? ' report-section-heading--compact' : '' }}">
    <div class="report-section-heading-copy">
        <span class="report-section-icon" aria-hidden="true">
            <i class="{{ $icon ?? 'fas fa-chart-bar' }}"></i>
        </span>
        <div>
            <h2>{{ $title ?? 'Report results' }}</h2>
            @if(!empty($description))
                <p>{{ $description }}</p>
            @endif
        </div>
    </div>

    @if(isset($count))
        <span class="report-result-count">
            <strong>{{ number_format((int) $count) }}</strong>
            <span>{{ $countLabel ?? 'records' }}</span>
        </span>
    @endif
</div>
