(function () {
    'use strict';

    var root = document.querySelector('[data-pc-root]');

    if (!root || root.getAttribute('data-pc-initialized') === 'true') {
        return;
    }

    root.setAttribute('data-pc-initialized', 'true');

    var stageButtons = Array.prototype.slice.call(root.querySelectorAll('[data-pc-stage-button]'));
    var statusButtons = Array.prototype.slice.call(root.querySelectorAll('[data-pc-status-toggle]'));
    var cards = Array.prototype.slice.call(root.querySelectorAll('[data-pc-case-card]'));
    var search = root.querySelector('[data-pc-search]');
    var dueToggle = root.querySelector('[data-pc-due-toggle]');
    var detailPanel = root.querySelector('[data-pc-detail-panel]');
    var detailContent = root.querySelector('[data-pc-detail-content]');
    var detailBackdrop = root.querySelector('[data-pc-detail-backdrop]');
    var visibleCount = root.querySelector('[data-pc-visible-count]');
    var stageTitles = Array.prototype.slice.call(root.querySelectorAll('[data-pc-stage-title]'));
    var mobileViewport = window.matchMedia('(max-width: 919px)');
    var selectedStage = root.getAttribute('data-pc-selected-stage') || (stageButtons[0] ? stageButtons[0].getAttribute('data-stage') : '');
    var selectedStatus = root.getAttribute('data-pc-selected-status') || 'active';
    var requestedCaseId = root.getAttribute('data-pc-selected-case') || '';
    var dueOnly = dueToggle ? dueToggle.getAttribute('aria-pressed') === 'true' : false;
    var selectedCard = null;
    var lastFocused = null;
    var bodyOverflowBeforeLock = null;
    var bodyScrollLocked = false;

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
        stageTitles.forEach(function (title) {
            title.textContent = button.getAttribute('data-label') || '';
        });
    }

    function cardMatches(card) {
        var term = search ? search.value.trim().toLowerCase() : '';

        return card.getAttribute('data-stage') === selectedStage
            && card.getAttribute('data-state') === selectedStatus
            && (!dueOnly || card.getAttribute('data-due-today') === 'true')
            && (!term || (card.getAttribute('data-search') || '').indexOf(term) !== -1);
    }

    function setDetailPanelInert(inert) {
        if (!detailPanel) return;

        if ('inert' in detailPanel) detailPanel.inert = inert;
        if (inert) detailPanel.setAttribute('inert', '');
        else detailPanel.removeAttribute('inert');
    }

    function lockBodyScroll() {
        if (bodyScrollLocked || !document.body) return;

        bodyOverflowBeforeLock = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        bodyScrollLocked = true;
    }

    function unlockBodyScroll() {
        if (!bodyScrollLocked || !document.body) return;

        document.body.style.overflow = bodyOverflowBeforeLock || '';
        bodyOverflowBeforeLock = null;
        bodyScrollLocked = false;
    }

    function clearCardSelection() {
        cards.forEach(function (card) {
            var trigger = card.querySelector('[data-pc-open-details]');
            if (trigger) trigger.setAttribute('aria-current', 'false');
        });
    }

    function configureMobileDetail(open) {
        if (!detailPanel) return;

        if (open) {
            detailPanel.setAttribute('aria-hidden', 'false');
            detailPanel.setAttribute('role', 'dialog');
            detailPanel.setAttribute('aria-modal', 'true');
            setDetailPanelInert(false);
            lockBodyScroll();
            return;
        }

        detailPanel.setAttribute('aria-hidden', 'true');
        detailPanel.removeAttribute('role');
        detailPanel.removeAttribute('aria-modal');
        setDetailPanelInert(true);
        unlockBodyScroll();
    }

    function configureDesktopDetail() {
        if (detailPanel) {
            detailPanel.removeAttribute('aria-hidden');
            detailPanel.removeAttribute('role');
            detailPanel.removeAttribute('aria-modal');
            setDetailPanelInert(false);
        }
        unlockBodyScroll();
    }

    function closeDetails(options) {
        var settings = options || {};

        root.classList.remove('pc-detail-open');
        if (detailBackdrop) detailBackdrop.hidden = true;

        if (mobileViewport.matches) {
            configureMobileDetail(false);
            clearCardSelection();
            if (settings.keepSelection !== true) selectedCard = null;
        } else {
            configureDesktopDetail();
        }

        if (settings.restoreFocus !== false && lastFocused && document.documentElement.contains(lastFocused)) {
            lastFocused.focus({ preventScroll: true });
        }
        lastFocused = null;
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

        if (mobileViewport.matches) {
            root.classList.add('pc-detail-open');
            if (detailBackdrop) detailBackdrop.hidden = false;
            configureMobileDetail(true);

            var closeButton = detailContent.querySelector('[data-pc-detail-close]');
            if (closeButton) closeButton.focus({ preventScroll: true });
            window.requestAnimationFrame(function () {
                if (closeButton && root.classList.contains('pc-detail-open') && mobileViewport.matches) {
                    closeButton.focus({ preventScroll: true });
                }
            });
        } else {
            root.classList.remove('pc-detail-open');
            if (detailBackdrop) detailBackdrop.hidden = true;
            configureDesktopDetail();
        }
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

        if (mobileViewport.matches) {
            requestedCaseId = '';
            selectedCard = null;
            closeDetails({ restoreFocus: false });
            return;
        }

        if (preferCurrent && selectedCard && visible.indexOf(selectedCard) !== -1) {
            return;
        }

        selectedCard = requestedCaseId
            ? visible.find(function (card) { return card.getAttribute('data-case-id') === requestedCaseId; }) || visible[0] || null
            : visible[0] || null;
        requestedCaseId = '';
        if (selectedCard) {
            openDetails(selectedCard, false);
        } else if (detailContent) {
            detailContent.innerHTML = '<div class="pc-detail-placeholder"><p>No case is available for these filters.</p></div>';
            clearCardSelection();
            closeDetails({ restoreFocus: false });
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

    function selectStatus(button, applyImmediately) {
        if (!button) return;
        selectedStatus = button.getAttribute('data-pc-status-toggle') || selectedStatus;
        root.setAttribute('data-pc-selected-status', selectedStatus);
        statusButtons.forEach(function (item) {
            item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
        });
        if (applyImmediately !== false) applyFilters(false);
    }

    root.addEventListener('click', function (event) {
        var stageButton = event.target.closest('[data-pc-stage-button]');
        var statusButton = event.target.closest('[data-pc-status-toggle]');
        var cardTrigger = event.target.closest('[data-pc-open-details]');
        var close = event.target.closest('[data-pc-detail-close]');
        var dueButton = event.target.closest('[data-pc-due-toggle]');
        var noteToggle = event.target.closest('[data-pc-note-toggle]');
        var noteCancel = event.target.closest('[data-pc-note-cancel]');
        var processingLink = event.target.closest('a[data-pc-processing]');

        if (stageButton && root.contains(stageButton)) {
            selectStage(stageButton);
            return;
        }

        if (statusButton && root.contains(statusButton)) {
            selectStatus(statusButton, true);
            return;
        }

        if (dueButton && root.contains(dueButton)) {
            dueOnly = !dueOnly;
            dueButton.setAttribute('aria-pressed', dueOnly ? 'true' : 'false');
            dueButton.setAttribute('aria-label', dueOnly ? 'Show all delivery dates' : 'Show cases due today');
            applyFilters(true);
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

    if (search) {
        search.addEventListener('input', function () {
            applyFilters(true);
        });
    }

    if (detailBackdrop) detailBackdrop.addEventListener('click', closeDetails);
    document.addEventListener('keydown', function (event) {
        var drawerOpen = mobileViewport.matches && root.classList.contains('pc-detail-open');

        if (event.key === 'Escape' && drawerOpen) {
            closeDetails();
            return;
        }

        if (event.key !== 'Tab' || !drawerOpen || !detailPanel) return;

        var focusable = Array.prototype.slice.call(detailPanel.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )).filter(function (element) {
            return !element.closest('[hidden]')
                && element.getAttribute('aria-hidden') !== 'true'
                && (element.offsetWidth || element.offsetHeight || element.getClientRects().length);
        });

        if (!focusable.length) {
            event.preventDefault();
            return;
        }

        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        var active = document.activeElement;

        if (event.shiftKey && (active === first || !detailPanel.contains(active))) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (active === last || !detailPanel.contains(active))) {
            event.preventDefault();
            first.focus();
        }
    });

    function handleViewportChange() {
        if (mobileViewport.matches) {
            closeDetails({ restoreFocus: false });
            return;
        }

        root.classList.remove('pc-detail-open');
        if (detailBackdrop) detailBackdrop.hidden = true;
        configureDesktopDetail();
        applyFilters(true);
    }

    if (typeof mobileViewport.addEventListener === 'function') {
        mobileViewport.addEventListener('change', handleViewportChange);
    } else if (typeof mobileViewport.addListener === 'function') {
        mobileViewport.addListener(handleViewportChange);
    }

    selectStatus(statusButtons.find(function (button) {
        return button.getAttribute('data-pc-status-toggle') === selectedStatus;
    }) || statusButtons[0], false);

    selectStage(stageButtons.find(function (button) {
        return button.getAttribute('data-stage') === selectedStage;
    }) || stageButtons[0]);
}());
