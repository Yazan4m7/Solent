@php
    $processingUi = trans('ui.dom');
    $processingText = is_array($processingUi)
        ? ($processingUi['Processing your request...'] ?? 'Processing your request...')
        : 'Processing your request...';
@endphp

<div class="solent-processing-overlay"
     data-solent-processing-overlay
     role="status"
     aria-live="polite"
     aria-label="{{ $processingText }}"
     hidden>
    <div class="solent-processing-overlay__panel">
        <span class="solent-processing-overlay__spinner" aria-hidden="true"></span>
        <strong data-solent-processing-message>{{ $processingText }}</strong>
    </div>
</div>

<script>
    window.SolentProcessingOverlay = window.SolentProcessingOverlay || (function () {
        function element() {
            return document.querySelector('[data-solent-processing-overlay]');
        }

        return {
            show: function (message) {
                var overlay = element();
                if (!overlay) return;

                var label = overlay.querySelector('[data-solent-processing-message]');
                if (label && message) label.textContent = String(message);
                overlay.hidden = false;
                document.body.classList.add('solent-processing');
            },
            hide: function () {
                var overlay = element();
                if (!overlay) return;

                overlay.hidden = true;
                document.body.classList.remove('solent-processing');
            }
        };
    }());
</script>
