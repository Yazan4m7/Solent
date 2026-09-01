/* Keep an old tab from submitting sample IDs against the real database after a switch. */
(function () {
    'use strict';
    var meta = document.querySelector('meta[name="data-context"]');
    if (!meta) return;
    var token = meta.content;
    function localUrl(value) {
        try {
            var url = new URL(value, location.href);
            return url.origin === location.origin ? url : null;
        } catch (_) { return null; }
    }
    function stampForm(form) {
        if (!localUrl(form.action)) return;
        var input = form.querySelector('input[name="_data_context"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_data_context';
            form.appendChild(input);
        }
        input.value = token;
    }
    document.addEventListener('submit', function (event) { stampForm(event.target); }, true);
    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href]');
        if (!link || link.hasAttribute('download')) return;
        var url = localUrl(link.href);
        if (!url || url.protocol !== 'http:' && url.protocol !== 'https:') return;
        if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
        url.searchParams.set('_data_context', token);
        link.href = url.href;
    }, true);
    // Cover existing jQuery, raw XHR and fetch-based forms without changing their response handling.
    var open = XMLHttpRequest.prototype.open;
    var send = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function (method, url) {
        this.solentSameOrigin = !!localUrl(url);
        return open.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function () {
        if (this.solentSameOrigin) this.setRequestHeader('X-Data-Context', token);
        return send.apply(this, arguments);
    };
    if (window.fetch) {
        var fetch = window.fetch;
        window.fetch = function (input, options) {
            var url = input instanceof Request ? input.url : input;
            if (!localUrl(url)) return fetch.apply(this, arguments);
            options = Object.assign({}, options);
            options.headers = new Headers(options.headers || (input instanceof Request ? input.headers : undefined));
            options.headers.set('X-Data-Context', token);
            return fetch.call(this, input, options);
        };
    }
    document.addEventListener('DOMContentLoaded', function () {
        Array.prototype.forEach.call(document.forms, stampForm);
        new MutationObserver(function (changes) {
            changes.forEach(function (change) {
                Array.prototype.forEach.call(change.addedNodes, function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.matches('form')) stampForm(node);
                    node.querySelectorAll('form').forEach(stampForm);
                });
            });
        }).observe(document.body, {childList: true, subtree: true});
    });
}());
