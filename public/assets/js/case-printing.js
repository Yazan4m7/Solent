(function () {
    'use strict';

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function labelData() {
        return window.casePrintData || {};
    }

    function jobRows(jobs) {
        if (!Array.isArray(jobs) || jobs.length === 0) {
            return '<tr><td colspan="4">No jobs recorded</td></tr>';
        }

        return jobs.map(function (job) {
            return '<tr>' +
                '<td>' + escapeHtml(job.jobType) + '</td>' +
                '<td>' + escapeHtml(job.material) + '</td>' +
                '<td>' + escapeHtml(job.color || '-') + '</td>' +
                '<td>' + escapeHtml(job.quantity) + '</td>' +
                '</tr>';
        }).join('');
    }

    function printDocument(title, body, compact) {
        var printWindow = window.open('', 'solent-case-label', compact ? 'width=420,height=320' : 'width=800,height=600');

        if (!printWindow) {
            window.alert('Your browser blocked the print window. Please allow pop-ups and try again.');
            return;
        }

        var root = document.documentElement;
        var direction = root.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr';
        var language = root.getAttribute('lang') || 'en';

        printWindow.document.open();
        printWindow.document.write('<!doctype html><html lang="' + escapeHtml(language) + '" dir="' + direction + '"><head><meta charset="utf-8"><title>' + escapeHtml(title) + '</title><style>' +
            '@page { margin: 6mm; }' +
            '* { box-sizing: border-box; }' +
            'body { margin: 0; color: #111; font: 12px/1.35 Arial, sans-serif; direction: ' + direction + '; }' +
            '.label { border: 1px solid #111; padding: 10px; width: 100%; }' +
            '.case-id { font-size: 20px; font-weight: 700; letter-spacing: .4px; margin-bottom: 8px; }' +
            '.meta { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 12px; margin-bottom: 8px; }' +
            '.meta strong { display: block; font-size: 10px; text-transform: uppercase; color: #555; }' +
            'table { width: 100%; border-collapse: collapse; margin-top: 8px; }' +
            'th, td { border: 1px solid #222; padding: 4px; text-align: start; vertical-align: top; }' +
            'th { background: #eee; font-size: 10px; }' +
            '.mini .label { min-height: 48mm; padding: 8px; }' +
            '.mini .case-id { font-size: 18px; margin-bottom: 5px; }' +
            '.mini .meta { display: block; margin: 0; }' +
            '.mini .meta > div { margin-top: 3px; }' +
            '.mini .jobs { margin-top: 7px; font-size: 11px; font-weight: 700; }' +
            '@media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }' +
            '</style></head><body class="' + (compact ? 'mini' : '') + '">' + body + '</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.onafterprint = function () { printWindow.close(); };
        window.setTimeout(function () { printWindow.print(); }, 150);
    }

    window.PrintLabel = function () {
        var data = labelData();
        var body = '<section class="label">' +
            '<div class="case-id">' + escapeHtml(data.caseId) + '</div>' +
            '<div class="meta">' +
            '<div><strong>Doctor</strong>' + escapeHtml(data.doctor) + '</div>' +
            '<div><strong>Patient</strong>' + escapeHtml(data.patient) + '</div>' +
            '<div><strong>Delivery date</strong>' + escapeHtml(data.deliveryDate) + '</div>' +
            '<div><strong>Delivery time</strong>' + escapeHtml(data.deliveryTime) + '</div>' +
            '</div>' +
            '<table><thead><tr><th>Job type</th><th>Material</th><th>Shade</th><th>Units</th></tr></thead><tbody>' +
            jobRows(data.jobs) +
            '</tbody></table>' +
            '</section>';

        printDocument('Case ' + (data.caseId || ''), body, false);
    };

    window.PrintMinimizedLabel = function () {
        var data = labelData();
        var jobs = Array.isArray(data.jobs) ? data.jobs : [];
        var summary = jobs.map(function (job) {
            return [job.jobType, job.material, job.quantity ? '(' + job.quantity + ')' : ''].filter(Boolean).join(' ');
        }).join(' · ');
        var body = '<section class="label">' +
            '<div class="case-id">' + escapeHtml(data.caseId) + '</div>' +
            '<div class="meta">' +
            '<div><strong>Doctor</strong>' + escapeHtml(data.doctor) + '</div>' +
            '<div><strong>Patient</strong>' + escapeHtml(data.patient) + '</div>' +
            '<div><strong>Delivery</strong>' + escapeHtml([data.deliveryDate, data.deliveryTime].filter(Boolean).join(' ')) + '</div>' +
            '</div>' +
            '<div class="jobs">' + escapeHtml(summary) + '</div>' +
            '</section>';

        printDocument('Mini case label ' + (data.caseId || ''), body, true);
    };
}());
