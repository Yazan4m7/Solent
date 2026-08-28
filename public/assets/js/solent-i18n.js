(function () {
    'use strict';

    var config = window.SolentI18n || {};
    if (config.locale !== 'ar') {
        return;
    }

    var messages = config.messages || {};
    var blockedTags = { SCRIPT: true, STYLE: true, CODE: true, PRE: true, TEXTAREA: true, OPTION: true };
    var attributes = ['placeholder', 'title', 'aria-label', 'data-original-title'];

    function translate(value) {
        if (typeof value !== 'string') {
            return value;
        }

        var trimmed = value.trim();
        if (!trimmed) {
            return value;
        }

        if (Object.prototype.hasOwnProperty.call(messages, trimmed)) {
            return value.replace(trimmed, messages[trimmed]);
        }

        var counted = trimmed.match(/^(\d[\d,]*)\s+(active cases|waiting cases|total cases|cases)$/i);
        if (counted) {
            var patternKey = ':count ' + counted[2].toLowerCase();
            if (Object.prototype.hasOwnProperty.call(messages, patternKey)) {
                return value.replace(trimmed, messages[patternKey].replace(':count', counted[1]));
            }
        }

        return value;
    }

    function isExcluded(element) {
        return !element || blockedTags[element.tagName] || element.closest('[translate="no"], .notranslate, [data-i18n-ignore]');
    }

    function translateElement(element) {
        if (isExcluded(element)) {
            return;
        }

        attributes.forEach(function (attribute) {
            if (element.hasAttribute(attribute)) {
                var original = element.getAttribute(attribute);
                var translated = translate(original);
                if (translated !== original) {
                    element.setAttribute(attribute, translated);
                }
            }
        });

        if (element.tagName === 'INPUT' && ['button', 'submit', 'reset'].indexOf(element.type) !== -1) {
            element.value = translate(element.value);
        }
    }

    function translateTree(root) {
        if (!root) {
            return;
        }

        if (root.nodeType === Node.ELEMENT_NODE) {
            translateElement(root);
        }

        var walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT);
        var node;
        while ((node = walker.nextNode())) {
            if (node.nodeType === Node.ELEMENT_NODE) {
                translateElement(node);
                continue;
            }

            var parent = node.parentElement;
            if (!isExcluded(parent)) {
                node.nodeValue = translate(node.nodeValue);
            }
        }
    }

    function configureDataTables() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.dataTable || !config.dataTables) {
            return;
        }

        jQuery.extend(true, jQuery.fn.dataTable.defaults, {
            language: config.dataTables
        });
    }

    function boot() {
        configureDataTables();
        translateTree(document.body);

        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        translateTree(node);
                    } else if (node.nodeType === Node.TEXT_NODE && !isExcluded(node.parentElement)) {
                        node.nodeValue = translate(node.nodeValue);
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
}());
