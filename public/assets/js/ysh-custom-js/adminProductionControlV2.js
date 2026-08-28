(function () {
    'use strict';

    var root = document.querySelector('[data-pc-root]');

    if (!root || root.getAttribute('data-pc-initialized') === 'true') {
        return;
    }

    root.setAttribute('data-pc-initialized', 'true');

    var stageButtons = Array.prototype.slice.call(root.querySelectorAll('[data-pc-stage-button]'));
    var cards = Array.prototype.slice.call(root.querySelectorAll('[data-pc-case-card]'));
    var search = root.querySelector('[data-pc-search]');
    var statusFilter = root.querySelector('[data-pc-status-filter]');
    var assignmentFilter = root.querySelector('[data-pc-assignment-filter]');
    var dueFilter = root.querySelector('[data-pc-due-filter]');
    var detailPanel = root.querySelector('[data-pc-detail-panel]');
    var detailContent = root.querySelector('[data-pc-detail-content]');
    var detailBackdrop = root.querySelector('[data-pc-detail-backdrop]');
    var visibleCount = root.querySelector('[data-pc-visible-count]');
    var stageTitle = root.querySelector('[data-pc-stage-title]');
    var selectedStage = root.getAttribute('data-pc-selected-stage') || (stageButtons[0] ? stageButtons[0].getAttribute('data-stage') : '');
    var requestedCaseId = root.getAttribute('data-pc-selected-case') || '';
    var selectedCard = null;
    var lastFocused = null;

    function showProcessing() {
        if (window.SolentProcessingOverlay && typeof window.SolentProcessingOverlay.show === 'function') {
            window.SolentProcessingOverlay.show(root.getAttribute('data-pc-processing-message') || 'Processing your request...');
        }
    }

    function summaryValue(name, value) {
        var element = root.querySelector('[data-pc-summary="' + name + '"]');
        if (element) element.textContent = String(value);
    }

    function updateSummary(button) {
        if (!button) return;

        var ready = Number(button.getAttribute('data-ready') || 0);
        var active = Number(button.getAttribute('data-active') || 0);
        summaryValue('ready', ready);
        summaryValue('active', active);
        summaryValue('due', Number(button.getAttribute('data-due') || 0));
        summaryValue('total', ready + active);
        if (stageTitle) stageTitle.textContent = button.getAttribute('data-label') || '';
    }

    function cardMatches(card) {
        var term = search ? search.value.trim().toLowerCase() : '';
        var status = statusFilter ? statusFilter.value : 'all';
        var assignment = assignmentFilter ? assignmentFilter.value : 'all';
        var due = dueFilter ? dueFilter.value : 'all';

        return card.getAttribute('data-stage') === selectedStage
            && (status === 'all' || card.getAttribute('data-state') === status)
            && (assignment === 'all' || card.getAttribute('data-assignment') === assignment)
            && (due === 'all' || card.getAttribute('data-due-today') === 'true')
            && (!term || (card.getAttribute('data-search') || '').indexOf(term) !== -1);
    }

    function closeDetails() {
        root.classList.remove('pc-detail-open');
        if (detailBackdrop) detailBackdrop.hidden = true;
        if (lastFocused && document.documentElement.contains(lastFocused)) {
            lastFocused.focus({ preventScroll: true });
        }
    }

    function openDetails(card, focusTrigger) {
        var template = card ? card.querySelector('[data-pc-detail-template]') : null;
        var trigger = card ? card.querySelector('[data-pc-open-details]') : null;

        if (!template || !detailContent) return;

        cards.forEach(function (item) {
            var itemTrigger = item.querySelector('[data-pc-open-details]');
            if (itemTrigger) itemTrigger.setAttribute('aria-current', item === card ? 'true' : 'false');
        });

        detailContent.replaceChildren(template.content.cloneNode(true));
        selectedCard = card;
        if (focusTrigger) lastFocused = trigger || document.activeElement;
        root.classList.add('pc-detail-open');
        if (detailBackdrop) detailBackdrop.hidden = false;
    }

    function applyFilters(preferCurrent) {
        var visible = [];

        cards.forEach(function (card) {
            var matches = cardMatches(card);
            card.hidden = !matches;
            if (matches) visible.push(card);
        });

        if (visibleCount) visibleCount.textContent = String(visible.length);
        var empty = root.querySelector('[data-pc-empty]');
        if (empty) empty.hidden = visible.length !== 0;

        if (preferCurrent && selectedCard && visible.indexOf(selectedCard) !== -1) {
            return;
        }

        selectedCard = requestedCaseId
            ? visible.find(function (card) { return card.getAttribute('data-case-id') === requestedCaseId; }) || visible[0] || null
            : visible[0] || null;
        requestedCaseId = '';
        if (selectedCard) {
            openDetails(selectedCard, false);
            if (window.matchMedia('(max-width: 919px)').matches) closeDetails();
        } else if (detailContent) {
            detailContent.innerHTML = '<div class="pc-detail-placeholder"><p>No case is available for these filters.</p></div>';
            closeDetails();
        }
    }

    function selectStage(button) {
        if (!button) return;
        selectedStage = button.getAttribute('data-stage') || selectedStage;
        root.setAttribute('data-pc-selected-stage', selectedStage);
        stageButtons.forEach(function (item) {
            item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
        });
        updateSummary(button);
        applyFilters(false);
    }

    root.addEventListener('click', function (event) {
        var stageButton = event.target.closest('[data-pc-stage-button]');
        var cardTrigger = event.target.closest('[data-pc-open-details]');
        var close = event.target.closest('[data-pc-detail-close]');
        var noteToggle = event.target.closest('[data-pc-note-toggle]');
        var noteCancel = event.target.closest('[data-pc-note-cancel]');
        var processingLink = event.target.closest('a[data-pc-processing]');

        if (stageButton && root.contains(stageButton)) {
            selectStage(stageButton);
            return;
        }

        if (cardTrigger && root.contains(cardTrigger)) {
            openDetails(cardTrigger.closest('[data-pc-case-card]'), true);
            return;
        }

        if (close && root.contains(close)) {
            closeDetails();
            return;
        }

        if (noteToggle && root.contains(noteToggle)) {
            var noteForm = noteToggle.closest('.pc-detail-action-zone').querySelector('[data-pc-note-form]');
            if (noteForm) {
                noteForm.hidden = false;
                noteToggle.setAttribute('aria-expanded', 'true');
                var noteInput = noteForm.querySelector('[data-pc-note-input]');
                if (noteInput) noteInput.focus({ preventScroll: true });
            }
            return;
        }

        if (noteCancel && root.contains(noteCancel)) {
            var cancelledForm = noteCancel.closest('[data-pc-note-form]');
            var cancelledToggle = cancelledForm
                ? cancelledForm.closest('.pc-detail-action-zone').querySelector('[data-pc-note-toggle]')
                : null;
            if (cancelledForm) {
                cancelledForm.reset();
                cancelledForm.hidden = true;
            }
            if (cancelledToggle) {
                cancelledToggle.setAttribute('aria-expanded', 'false');
                cancelledToggle.focus({ preventScroll: true });
            }
            return;
        }

        if (processingLink && !event.ctrlKey && !event.metaKey && !event.shiftKey) {
            var confirmation = processingLink.getAttribute('data-pc-confirm');
            if (confirmation && !window.confirm(confirmation)) {
                event.preventDefault();
                return;
            }
            showProcessing();
        }
    });

    root.addEventListener('submit', function (event) {
        if (event.target.matches('[data-pc-processing]') && event.target.checkValidity()) {
            showProcessing();
        }
    });

    [search, statusFilter, assignmentFilter, dueFilter].forEach(function (control) {
        if (!control) return;
        control.addEventListener(control === search ? 'input' : 'change', function () {
            applyFilters(true);
        });
    });

    if (detailBackdrop) detailBackdrop.addEventListener('click', closeDetails);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && root.classList.contains('pc-detail-open')) closeDetails();
    });

    selectStage(stageButtons.find(function (button) {
        return button.getAttribute('data-stage') === selectedStage;
    }) || stageButtons[0]);
}());
