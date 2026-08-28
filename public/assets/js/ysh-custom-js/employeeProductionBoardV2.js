(function () {
    'use strict';

    var root = document.querySelector('[data-epb-root]');

    if (!root || root.getAttribute('data-epb-initialized') === 'true' || !window.fetch) {
        return;
    }

    root.setAttribute('data-epb-initialized', 'true');

    var boardFragment = root.querySelector('[data-epb-board-fragment]');
    var backdrop = root.querySelector('[data-epb-sheet-backdrop]');
    var sheet = backdrop ? backdrop.querySelector('[data-epb-sheet]') : null;
    var sheetSurface = backdrop ? backdrop.querySelector('[data-epb-sheet-content]') : null;
    var toast = root.querySelector('[data-epb-toast]');
    var initialSheetHtml = sheetSurface ? sheetSurface.innerHTML : '';
    var selectedStage = root.getAttribute('data-epb-selected-stage') || 'all';
    var inFlightMutations = new Set();
    var detailRequest = null;
    var detailRequestSerial = 0;
    var currentDetails = null;
    var lastFocusedElement = null;
    var toastTimer = null;

    if (backdrop && backdrop.parentNode !== document.body) {
        document.body.appendChild(backdrop);
    }

    if (toast && toast.parentNode !== document.body) {
        document.body.appendChild(toast);
    }

    function message(name, fallback) {
        var value = root.getAttribute('data-' + name);
        return value && value.trim() !== '' ? value : fallback;
    }

    function asElement(target) {
        return target && target.nodeType === 1 ? target : target && target.parentElement;
    }

    function showToast(text, tone) {
        if (!toast || !text) {
            return;
        }

        window.clearTimeout(toastTimer);
        toast.textContent = String(text);
        toast.setAttribute('data-epb-tone', tone || 'status');
        toast.hidden = false;
        toastTimer = window.setTimeout(function () {
            toast.hidden = true;
        }, tone === 'error' ? 6500 : 4200);
    }

    function parseServerResponse(response) {
        return response.text().then(function (text) {
            var contentType = response.headers.get('content-type') || '';
            var payload = null;
            var trimmed = text.trim();

            if (contentType.indexOf('application/json') !== -1 || trimmed.charAt(0) === '{') {
                try {
                    payload = trimmed === '' ? {} : JSON.parse(trimmed);
                } catch (error) {
                    payload = null;
                }
            }

            return {
                response: response,
                payload: payload,
                text: text
            };
        });
    }

    function firstValidationError(payload) {
        if (!payload) {
            return '';
        }

        if (payload.errors && typeof payload.errors === 'object') {
            var keys = Object.keys(payload.errors);

            for (var index = 0; index < keys.length; index += 1) {
                var value = payload.errors[keys[index]];

                if (Array.isArray(value) && value.length) {
                    return String(value[0]);
                }

                if (typeof value === 'string' && value.trim() !== '') {
                    return value;
                }
            }
        }

        return typeof payload.message === 'string' ? payload.message : '';
    }

    function setFormError(form, text) {
        var error = form ? form.querySelector('[data-epb-form-error]') : null;

        if (!error) {
            return;
        }

        error.textContent = text || '';
        error.hidden = !text;
    }

    function generateIdempotencyKey() {
        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
            return window.crypto.randomUUID();
        }

        var bytes = new Uint8Array(16);

        if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
            window.crypto.getRandomValues(bytes);
        } else {
            for (var index = 0; index < bytes.length; index += 1) {
                bytes[index] = Math.floor(Math.random() * 256);
            }
        }

        bytes[6] = (bytes[6] & 15) | 64;
        bytes[8] = (bytes[8] & 63) | 128;

        var hex = Array.prototype.map.call(bytes, function (byte) {
            return byte.toString(16).padStart(2, '0');
        }).join('');

        return [hex.slice(0, 8), hex.slice(8, 12), hex.slice(12, 16), hex.slice(16, 20), hex.slice(20)].join('-');
    }

    function idempotencyInput(form) {
        var input = form.querySelector('[data-epb-idempotency-key]');

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'idempotency_key';
            input.setAttribute('data-epb-idempotency-key', '');
            form.appendChild(input);
        }

        return input;
    }

    function ensureIdempotencyKey(form) {
        var input = idempotencyInput(form);

        if (!input.value) {
            input.value = generateIdempotencyKey();
        }

        return input.value;
    }

    function clearIdempotencyKey(form) {
        var input = form.querySelector('[data-epb-idempotency-key]');

        if (input) {
            input.value = '';
        }
    }

    function setControlBusy(control, busy) {
        if (busy) {
            if (!control.hasAttribute('data-epb-disabled-before-request')) {
                control.setAttribute('data-epb-disabled-before-request', control.disabled ? 'true' : 'false');
            }
            control.disabled = true;
            return;
        }

        if (control.hasAttribute('data-epb-disabled-before-request')) {
            control.disabled = control.getAttribute('data-epb-disabled-before-request') === 'true';
            control.removeAttribute('data-epb-disabled-before-request');
        }
    }

    function setGlobalBusy(busy, activeForm) {
        var selectors = [
            '[data-epb-stage-button]',
            '[data-epb-open-details]',
            '[data-epb-submit-button]',
            '[data-epb-note-input]',
            '[data-epb-sheet-close]'
        ].join(',');
        var controls = Array.prototype.slice.call(root.querySelectorAll(selectors));

        if (backdrop) {
            controls = controls.concat(Array.prototype.slice.call(backdrop.querySelectorAll(selectors)));
        }

        controls.forEach(function (control) {
            setControlBusy(control, busy);
        });

        root.setAttribute('aria-busy', busy ? 'true' : 'false');

        if (activeForm) {
            activeForm.setAttribute('aria-busy', busy ? 'true' : 'false');
        }
    }

    function setSubmitLabel(form, pending) {
        var button = form.querySelector('[data-epb-submit-button]');
        var label = button ? button.querySelector('[data-epb-button-label]') : null;

        if (!button || !label) {
            return;
        }

        if (pending) {
            if (!button.hasAttribute('data-epb-original-label')) {
                button.setAttribute('data-epb-original-label', label.textContent);
            }
            label.textContent = button.getAttribute('data-epb-pending-label') || label.textContent;
        } else if (button.hasAttribute('data-epb-original-label')) {
            label.textContent = button.getAttribute('data-epb-original-label');
            button.removeAttribute('data-epb-original-label');
        }
    }

    function showProcessingOverlay() {
        if (window.SolentProcessingOverlay && typeof window.SolentProcessingOverlay.show === 'function') {
            window.SolentProcessingOverlay.show(
                message('epb-processing-message', 'Processing your request...')
            );
            return;
        }

        if (typeof window.showLoadingIndicator === 'function') {
            window.showLoadingIndicator();
        }
    }

    function hideProcessingOverlay() {
        if (window.SolentProcessingOverlay && typeof window.SolentProcessingOverlay.hide === 'function') {
            window.SolentProcessingOverlay.hide();
            return;
        }

        if (typeof window.hideLoadingIndicator === 'function') {
            window.hideLoadingIndicator();
        }
    }

    function applyStageFilter(stage) {
        if (!boardFragment) {
            return;
        }

        var requested = String(stage || 'all');
        var buttons = Array.prototype.slice.call(boardFragment.querySelectorAll('[data-epb-stage-button]'));
        var exists = requested === 'all' || buttons.some(function (button) {
            return button.getAttribute('data-epb-stage') === requested;
        });

        selectedStage = exists ? requested : 'all';
        root.setAttribute('data-epb-selected-stage', selectedStage);

        buttons.forEach(function (button) {
            button.setAttribute(
                'aria-pressed',
                button.getAttribute('data-epb-stage') === selectedStage ? 'true' : 'false'
            );
        });

        ['active', 'queue'].forEach(function (group) {
            var grid = boardFragment.querySelector('[data-epb-grid="' + group + '"]');
            var count = 0;

            if (grid) {
                Array.prototype.forEach.call(grid.querySelectorAll('[data-epb-case-card]'), function (card) {
                    var visible = selectedStage === 'all' || card.getAttribute('data-epb-stage') === selectedStage;
                    card.hidden = !visible;
                    if (visible) {
                        count += 1;
                    }
                });

                var empty = grid.querySelector('[data-epb-empty-state="' + group + '"]');
                if (empty) {
                    empty.hidden = count !== 0;
                }
            }

            var counter = boardFragment.querySelector('[data-epb-visible-count="' + group + '"]');
            if (counter) {
                counter.textContent = String(count);
            }
        });
    }

    function fragmentFromHtml(html, selector) {
        var template = document.createElement('template');
        template.innerHTML = String(html || '').trim();
        return {
            template: template,
            selected: template.content.querySelector(selector)
        };
    }

    function replaceBoard(html) {
        if (!boardFragment || typeof html !== 'string' || html.trim() === '') {
            return false;
        }

        var parsed = fragmentFromHtml(html, '[data-epb-board-fragment]');

        if (parsed.selected) {
            boardFragment.replaceWith(parsed.selected);
            boardFragment = parsed.selected;
        } else {
            boardFragment.replaceChildren.apply(boardFragment, Array.prototype.slice.call(parsed.template.content.childNodes));
        }

        applyStageFilter(selectedStage);
        return true;
    }

    function replaceSheet(html) {
        if (!sheetSurface || typeof html !== 'string' || html.trim() === '') {
            return false;
        }

        var parsed = fragmentFromHtml(html, '[data-epb-sheet-content]');
        var source = parsed.selected || parsed.template.content;
        var nodes = Array.prototype.slice.call(source.childNodes);

        sheetSurface.replaceChildren.apply(sheetSurface, nodes);
        sheet.setAttribute('aria-busy', 'false');
        return true;
    }

    function focusableElements() {
        if (!sheet || !backdrop || backdrop.hidden) {
            return [];
        }

        return Array.prototype.filter.call(sheet.querySelectorAll([
            'a[href]',
            'button:not([disabled])',
            'textarea:not([disabled])',
            'input:not([disabled]):not([type="hidden"])',
            'select:not([disabled])',
            '[tabindex]:not([tabindex="-1"])'
        ].join(',')), function (element) {
            return element.getClientRects().length > 0 && element.getAttribute('aria-hidden') !== 'true';
        });
    }

    function focusSheet(preferNote) {
        window.requestAnimationFrame(function () {
            var target = preferNote && sheet ? sheet.querySelector('[data-epb-note-input]:not([disabled])') : null;
            var focusables = focusableElements();
            target = target || focusables[0] || sheet;

            if (target && typeof target.focus === 'function') {
                target.focus({ preventScroll: true });
            }
        });
    }

    function resetSheetToLoading() {
        if (!sheetSurface) {
            return;
        }

        sheetSurface.innerHTML = initialSheetHtml;
        sheet.setAttribute('aria-busy', 'true');
    }

    function showSheetError(text) {
        if (!sheetSurface) {
            return;
        }

        resetSheetToLoading();
        var body = sheetSurface.querySelector('.epb-sheet__body');
        var error = document.createElement('div');
        error.className = 'epb-sheet-error';
        error.setAttribute('role', 'alert');
        error.textContent = text;

        if (body) {
            body.className = 'epb-sheet__body';
            body.replaceChildren(error);
        }

        sheet.setAttribute('aria-busy', 'false');
        focusSheet(false);
    }

    function openSheet(trigger) {
        if (!backdrop || !sheet) {
            return;
        }

        lastFocusedElement = trigger || document.activeElement;
        backdrop.hidden = false;
        document.body.classList.add('epb-sheet-open');
        resetSheetToLoading();
        focusSheet(false);
    }

    function closeSheet() {
        if (!backdrop || backdrop.hidden) {
            return;
        }

        if (inFlightMutations.size > 0) {
            showToast(message('epb-busy-message', 'Another update is still being saved.'), 'status');
            return;
        }

        if (detailRequest) {
            detailRequest.abort();
            detailRequest = null;
        }

        detailRequestSerial += 1;
        backdrop.hidden = true;
        document.body.classList.remove('epb-sheet-open');

        if (lastFocusedElement && document.documentElement.contains(lastFocusedElement)) {
            lastFocusedElement.focus({ preventScroll: true });
        }
    }

    function detailUrl(trigger) {
        var direct = trigger.getAttribute('data-epb-details-url');

        if (direct) {
            return direct;
        }

        var template = root.getAttribute('data-epb-details-url-template') || '';
        var caseId = trigger.getAttribute('data-epb-case-id') || '';
        var stage = trigger.getAttribute('data-epb-stage') || '';

        return template
            .replace(/\{case_id\}|\{case\}|__CASE__/g, encodeURIComponent(caseId))
            .replace(/\{stage_id\}|\{stage\}|__STAGE__/g, encodeURIComponent(stage));
    }

    function loadDetails(trigger, options) {
        options = options || {};
        var url = options.url || detailUrl(trigger);
        var preferNote = options.preferNote === true || trigger.getAttribute('data-epb-focus-note') === 'true';

        if (!url) {
            showToast(message('epb-details-error-message', 'Case details could not be loaded.'), 'error');
            return;
        }

        if (!options.keepOpen) {
            openSheet(trigger);
        } else {
            resetSheetToLoading();
        }

        if (detailRequest) {
            detailRequest.abort();
        }

        var requestSerial = detailRequestSerial + 1;
        detailRequestSerial = requestSerial;
        detailRequest = typeof window.AbortController === 'function' ? new window.AbortController() : null;
        currentDetails = {
            url: url,
            caseId: trigger.getAttribute('data-epb-case-id') || '',
            stage: trigger.getAttribute('data-epb-stage') || ''
        };

        var requestOptions = {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json, text/html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (detailRequest) {
            requestOptions.signal = detailRequest.signal;
        }

        window.fetch(url, requestOptions)
            .then(parseServerResponse)
            .then(function (result) {
                if (requestSerial !== detailRequestSerial || backdrop.hidden) {
                    return;
                }

                if (result.response.status === 401 || result.response.status === 419) {
                    throw new Error(message('epb-session-message', 'Your session has expired. Reload the page and try again.'));
                }

                if (!result.response.ok) {
                    var plainServerError = !result.payload
                        && result.text.trim().length <= 500
                        && result.text.indexOf('<') === -1
                        ? result.text.trim()
                        : '';
                    throw new Error(firstValidationError(result.payload) || plainServerError || message('epb-details-error-message', 'Case details could not be loaded.'));
                }

                if (result.payload && result.payload.ok === false) {
                    throw new Error(firstValidationError(result.payload) || message('epb-details-error-message', 'Case details could not be loaded.'));
                }

                var html = result.payload
                    ? (typeof result.payload.sheet_html === 'string' ? result.payload.sheet_html : '')
                    : result.text;

                if (!html || /^\s*(?:<!doctype|<html)/i.test(html)) {
                    throw new Error(message('epb-details-error-message', 'Case details could not be loaded.'));
                }

                if (!replaceSheet(html)) {
                    throw new Error(message('epb-details-error-message', 'Case details could not be loaded.'));
                }

                focusSheet(preferNote);
            })
            .catch(function (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }

                if (requestSerial === detailRequestSerial && backdrop && !backdrop.hidden) {
                    showSheetError(error && error.message
                        ? error.message
                        : message('epb-details-error-message', 'Case details could not be loaded.'));
                }
            })
            .finally(function () {
                if (requestSerial === detailRequestSerial) {
                    detailRequest = null;
                }
            });
    }

    function focusUpdatedCase(caseId, stage) {
        if (!boardFragment || !caseId) {
            return;
        }

        var cards = boardFragment.querySelectorAll('[data-epb-case-card]');
        var card = null;

        for (var index = 0; index < cards.length; index += 1) {
            if (cards[index].getAttribute('data-epb-case-id') === String(caseId)
                && cards[index].getAttribute('data-epb-stage') === String(stage || '')) {
                card = cards[index];
                break;
            }
        }

        if (!card || card.hidden) {
            return;
        }

        var target = card.querySelector('[data-epb-open-details]:not([disabled]), [data-epb-submit-button]:not([disabled])');
        if (target) {
            target.focus({ preventScroll: true });
        }
    }

    function csrfToken(form) {
        var input = form.querySelector('input[name="_token"]');
        var meta = document.querySelector('meta[name="csrf-token"]');
        return input ? input.value : (meta ? meta.getAttribute('content') : '');
    }

    function mutationFailure(form, status, payload) {
        var text = firstValidationError(payload);

        if (!text && (status === 401 || status === 419)) {
            text = message('epb-session-message', 'Your session has expired. Reload the page and try again.');
        } else if (!text && status === 403) {
            text = message('epb-forbidden-message', 'This action is not available for this case.');
        } else if (!text) {
            text = message('epb-error-message', 'The update could not be saved. Please try again.');
        }

        setFormError(form, text);
        showToast(text, 'error');
    }

    async function submitMutation(form) {
        var mutationIdentity = [
            form.getAttribute('data-epb-mutation') || 'mutation',
            form.getAttribute('data-epb-case-id') || '',
            form.getAttribute('data-epb-stage') || ''
        ].join(':');

        if (inFlightMutations.has(mutationIdentity) || inFlightMutations.size > 0) {
            showToast(message('epb-busy-message', 'Another update is still being saved.'), 'status');
            return;
        }

        if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
            return;
        }

        var confirmation = form.getAttribute('data-epb-confirm');
        if (confirmation && !window.confirm(confirmation)) {
            return;
        }

        var key = ensureIdempotencyKey(form);
        var data = new FormData(form);
        data.set('idempotency_key', key);
        var token = csrfToken(form);
        var mutationType = form.getAttribute('data-epb-mutation') || '';
        var formCaseId = form.getAttribute('data-epb-case-id') || data.get('case_id') || '';
        var formStage = form.getAttribute('data-epb-stage') || data.get('stage') || '';

        setFormError(form, '');
        inFlightMutations.add(mutationIdentity);
        setSubmitLabel(form, true);
        setGlobalBusy(true, form);
        showProcessingOverlay();

        try {
            var response = await window.fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                body: data,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Idempotency-Key': key
                }
            });
            var payload = null;

            try {
                payload = await response.json();
            } catch (parseError) {
                payload = null;
            }

            var status = response.status;

            if (!payload) {
                mutationFailure(form, status, null);
                return;
            }

            if ((status === 409 || response.ok) && typeof payload.board_html === 'string') {
                replaceBoard(payload.board_html);
            }

            if ((status === 409 || response.ok) && typeof payload.sheet_html === 'string' && backdrop && !backdrop.hidden) {
                replaceSheet(payload.sheet_html);
            }

            var acceptedDuplicate = status === 409 && (payload.ok === true || typeof payload.board_html === 'string');

            if (!response.ok && !acceptedDuplicate) {
                if (status >= 400 && status < 500) {
                    clearIdempotencyKey(form);
                }
                mutationFailure(form, status, payload);
                return;
            }

            if (payload.ok === false && !acceptedDuplicate) {
                clearIdempotencyKey(form);
                mutationFailure(form, status, payload);
                return;
            }

            if (typeof payload.board_html !== 'string' || payload.board_html.trim() === '') {
                mutationFailure(form, status, {
                    message: payload.message || message('epb-error-message', 'The update was saved, but the board could not be refreshed. Reload the page.')
                });
                return;
            }

            clearIdempotencyKey(form);
            showToast(payload.message || 'Saved.', 'success');

            if (mutationType === 'note') {
                if (typeof payload.sheet_html === 'string' && payload.sheet_html.trim() !== '') {
                    focusSheet(true);
                } else if (currentDetails && currentDetails.url && backdrop && !backdrop.hidden) {
                    loadDetails(form, {
                        url: currentDetails.url,
                        preferNote: true,
                        keepOpen: true
                    });
                }
            } else {
                window.requestAnimationFrame(function () {
                    focusUpdatedCase(payload.case_id || formCaseId, payload.stage || formStage);
                });
            }
        } catch (error) {
            mutationFailure(form, 0, null);
        } finally {
            inFlightMutations.delete(mutationIdentity);
            setSubmitLabel(form, false);
            setGlobalBusy(false, form);
            hideProcessingOverlay();
        }
    }

    root.addEventListener('click', function (event) {
        var target = asElement(event.target);
        var stageButton = target ? target.closest('[data-epb-stage-button]') : null;
        var detailsButton = target ? target.closest('[data-epb-open-details]') : null;

        if (stageButton && root.contains(stageButton)) {
            event.preventDefault();
            applyStageFilter(stageButton.getAttribute('data-epb-stage'));
            return;
        }

        if (detailsButton && root.contains(detailsButton) && !detailsButton.disabled) {
            event.preventDefault();
            loadDetails(detailsButton);
        }
    });

    root.addEventListener('submit', function (event) {
        var form = asElement(event.target);

        if (form && form.matches('[data-epb-mutation]')) {
            event.preventDefault();
            submitMutation(form);
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', function (event) {
            var target = asElement(event.target);
            var closeButton = target ? target.closest('[data-epb-sheet-close]') : null;

            if (closeButton || event.target === backdrop) {
                event.preventDefault();
                closeSheet();
            }
        });

        backdrop.addEventListener('submit', function (event) {
            var form = asElement(event.target);

            if (form && form.matches('[data-epb-mutation]')) {
                event.preventDefault();
                submitMutation(form);
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (!backdrop || backdrop.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeSheet();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        var focusables = focusableElements();

        if (!focusables.length) {
            event.preventDefault();
            sheet.focus();
            return;
        }

        var first = focusables[0];
        var last = focusables[focusables.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    applyStageFilter(selectedStage);
}());
