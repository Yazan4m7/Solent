(function () {
    'use strict';

    const body = document.getElementById('stock-lines-body');
    const template = document.getElementById('stock-line-template');
    const addButton = document.getElementById('add-stock-line');

    if (!body || !template || !addButton) return;

    function renumber() {
        body.querySelectorAll('.stock-line-row').forEach((row, index) => {
            row.querySelectorAll('[data-name]').forEach((field) => {
                field.name = `lines[${index}][${field.dataset.name}]`;
            });
        });
    }

    function addLine() {
        body.appendChild(template.content.cloneNode(true));
        renumber();
    }

    addButton.addEventListener('click', addLine);

    body.addEventListener('click', function (event) {
        const button = event.target.closest('.stock-remove-line');
        if (!button) return;

        button.closest('.stock-line-row').remove();
        if (!body.children.length) addLine();
        renumber();
    });

    addLine();
})();
