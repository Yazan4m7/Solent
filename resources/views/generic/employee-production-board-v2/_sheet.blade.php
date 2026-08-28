@php
    $ui = trans('ui.dom');
    $epbText = static function (string $key, ?string $fallback = null) use ($ui): string {
        return is_array($ui) && isset($ui[$key]) ? (string) $ui[$key] : ($fallback ?? $key);
    };
@endphp

<div class="epb-sheet-backdrop" data-epb-sheet-backdrop hidden>
    <aside class="epb-sheet"
           data-epb-sheet
           role="dialog"
           aria-modal="true"
           aria-labelledby="epb-sheet-title"
           aria-busy="false"
           tabindex="-1">
        <div class="epb-sheet__surface" data-epb-sheet-content>
            <header class="epb-sheet__header">
                <div>
                    <h2 id="epb-sheet-title">{{ $epbText('Case details', 'Case details') }}</h2>
                    <p class="epb-muted epb-small" data-epb-sheet-loading-message>
                        {{ $epbText('Loading case details...', 'Loading case details...') }}
                    </p>
                </div>
                <button class="epb-icon-button"
                        type="button"
                        data-epb-sheet-close
                        aria-label="{{ $epbText('Close details', 'Close details') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </header>

            <div class="epb-sheet__body epb-sheet__body--loading" aria-live="polite">
                <span class="epb-loading-indicator" aria-hidden="true"></span>
                <span>{{ $epbText('Loading case details...', 'Loading case details...') }}</span>
            </div>

            <footer class="epb-sheet__footer">
                <button class="epb-button" type="button" data-epb-sheet-close>
                    {{ $epbText('Close', 'Close') }}
                </button>
            </footer>
        </div>
    </aside>
</div>
