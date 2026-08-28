<style>
    body.white-content .report-filters {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-inline-start: 4px solid var(--accent) !important;
        border-radius: 16px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        margin: 0 0 16px;
        padding: 20px;
    }

    body.white-content .report-filter-grid {
        align-items: start;
        background: transparent !important;
        display: grid !important;
        gap: 16px 20px;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
    }

    body.white-content .report-filter {
        min-width: 0;
    }

    body.white-content .report-filter label {
        color: var(--text-2) !important;
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin: 0 0 7px;
    }

    body.white-content .report-filter label.report-filter-label {
        align-items: center;
        display: flex;
        gap: 7px;
        justify-content: flex-start;
    }

    body.white-content .report-filter-label i {
        color: var(--accent);
        flex: 0 0 auto;
        font-size: 13px;
        line-height: 1;
    }

    body.white-content .report-filter .kt-subheader__search,
    body.white-content .report-filter .dropdown,
    body.white-content .report-filter .bootstrap-select,
    body.white-content .report-filter .form-control,
    body.white-content .report-filter .bootstrap-select > .dropdown-toggle {
        min-width: 0 !important;
        width: 100% !important;
    }

    body.white-content .report-filter .form-control,
    body.white-content .report-filter .bootstrap-select > .dropdown-toggle {
        height: 44px !important;
        min-height: 44px !important;
    }

    body.white-content .report-filter .bootstrap-select > .dropdown-toggle {
        align-items: center;
        display: flex;
        padding: 0 14px !important;
    }

    body.white-content .report-filter .filter-option {
        align-items: center;
        display: flex !important;
        height: 100% !important;
        line-height: 1.2 !important;
        min-width: 0;
        top: 0 !important;
    }

    body.white-content .report-filter .filter-option-inner,
    body.white-content .report-filter .filter-option-inner-inner {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    body.white-content .report-filter .filter-option-inner {
        align-items: center;
        display: flex;
        height: 100%;
        width: 100%;
    }

    body.white-content .report-filter .filter-option-inner-inner {
        line-height: 1.2;
    }

    body.white-content .report-switch {
        cursor: pointer;
        display: block;
        margin: 0;
        max-width: 220px;
        width: 100%;
    }

    body.white-content .report-switch input {
        height: 1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        width: 1px;
    }

    body.white-content .report-switch__track {
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: 9px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        min-height: 44px;
        padding: 3px;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    body.white-content .report-switch__option {
        align-items: center;
        border-radius: 7px;
        color: var(--text-2);
        display: flex;
        font-size: 11px;
        font-weight: 800;
        justify-content: center;
        min-width: 0;
        padding: 7px 9px;
        text-align: center;
        text-transform: uppercase;
        transition: background-color 160ms ease, color 160ms ease, box-shadow 160ms ease;
    }

    body.white-content .report-switch input:not(:checked) + .report-switch__track .report-switch__option--unchecked,
    body.white-content .report-switch input:checked + .report-switch__track .report-switch__option--checked {
        background: var(--accent);
        box-shadow: 0 2px 7px rgba(79, 70, 229, 0.2);
        color: #ffffff;
    }

    body.white-content .report-switch input:focus-visible + .report-switch__track {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.22);
    }

    body.white-content .report-filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    body.white-content .report-filter-actions .btn {
        border-radius: 10px !important;
        font-weight: 700;
        margin: 0 !important;
        min-height: 42px;
        padding: 9px 18px;
    }

    body.white-content .report-results {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 16px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        margin: 0;
        overflow: hidden;
        padding: 20px !important;
    }

    body.white-content .report-applied-range {
        align-items: center;
        background: var(--accent-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-2);
        display: flex;
        flex-wrap: wrap;
        font-size: 12px;
        font-weight: 700;
        gap: 6px;
        margin: 0 0 14px;
        padding: 9px 12px;
        width: fit-content;
    }

    body.white-content .report-applied-range i {
        color: var(--accent);
    }

    body.white-content .report-applied-range bdi {
        color: var(--text-1);
        direction: ltr;
        font-weight: 800;
        unicode-bidi: isolate;
    }

    body.white-content .report-results > .row,
    body.white-content .report-results > .col-lg-12,
    body.white-content .report-results > .col-sm-12,
    body.white-content .report-results .col-lg-12,
    body.white-content .report-results .col-sm-12 {
        margin-left: 0 !important;
        margin-right: 0 !important;
        max-width: 100%;
        min-width: 0;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100%;
    }

    body.white-content .report-results .header-title {
        color: var(--text-2);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        margin: 0 0 4px;
        text-transform: uppercase;
    }

    body.white-content .report-results .header-title + h2 {
        color: var(--text-1);
        font-size: clamp(24px, 6vw, 34px);
        letter-spacing: -0.04em;
        margin: 0 0 18px;
    }

    body.white-content .report-table-scroll {
        -webkit-overflow-scrolling: touch;
        border-radius: 14px;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        padding-bottom: 2px;
        width: 100%;
    }

    body.white-content table.report-table {
        border: 1px solid var(--border) !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        border-radius: 14px !important;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.1);
        margin: 0 !important;
        min-width: 720px;
        overflow: hidden;
        width: 100% !important;
    }

    body.white-content table.report-table th,
    body.white-content table.report-table td {
        line-height: 1.4 !important;
        text-align: center;
        white-space: nowrap;
    }

    body.white-content table.report-table thead th {
        background: var(--surface-raised) !important;
        border: 1px solid var(--border) !important;
        color: var(--text-1) !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: 0.02em;
        padding: 14px 16px !important;
        text-transform: uppercase;
        vertical-align: middle !important;
    }

    body.white-content table.report-table thead tr:first-child th:first-child {
        border-top-left-radius: 13px;
    }

    body.white-content table.report-table thead tr:first-child th:last-child {
        border-top-right-radius: 13px;
    }

    body.white-content table.report-table thead th:first-child,
    body.white-content table.report-table tbody td:first-child {
        text-align: start;
    }

    body.white-content table.report-table .subHeaderRow > th {
        background: var(--surface-raised) !important;
        border: 1px solid var(--border) !important;
        color: var(--text-1) !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        padding: 14px 16px !important;
        text-align: center;
        text-transform: none;
    }

    body.white-content table.report-table .subHeaderRow > th:first-child {
        background: var(--text-1) !important;
        color: var(--surface) !important;
    }

    body.white-content table.report-table .tableHeaderRow > td,
    body.white-content table.report-table .tableHeaderRow > th {
        background: var(--surface-raised) !important;
        border: 1px solid var(--border) !important;
        color: var(--text-1) !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        padding: 13px 16px !important;
        text-transform: none;
    }

    body.white-content table.report-table tbody td {
        border: 1px solid var(--border) !important;
        color: var(--text-1) !important;
        font-size: 14px !important;
        padding: 13px 16px !important;
        vertical-align: middle !important;
    }

    body.white-content table.report-table tbody td:not(:first-child) {
        text-align: center;
    }

    body.white-content table.report-table tbody tr {
        background: #ffffff !important;
    }

    body.white-content table.report-table tbody tr:hover td {
        background: #f5f7ff !important;
    }

    body.white-content table.report-table tbody .dataRow:nth-child(even) td {
        background: var(--surface-raised) !important;
    }

    body.white-content table.report-table tfoot th,
    body.white-content table.report-table tfoot td {
        background: var(--accent-bg) !important;
        border: 1px solid var(--border) !important;
        color: var(--text-1) !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        padding: 13px 16px !important;
        text-align: center;
        white-space: nowrap;
    }

    body.white-content table.report-table .doctorName {
        font-weight: 800 !important;
    }

    body.white-content table.report-table .totalsCol,
    body.white-content table.report-table .totalsRow {
        background: var(--accent-bg) !important;
        color: var(--text-1) !important;
        font-weight: 700 !important;
        text-align: center;
    }

    body.white-content table.report-table .dataTables_empty {
        color: var(--text-2) !important;
        padding: 26px 16px !important;
        text-align: center !important;
    }

    body.white-content .report-results .dataTables_wrapper {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        margin-bottom: 24px;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        padding: 0 !important;
        width: 100%;
    }

    body.white-content .report-results .report-datatable-toolbar {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px 12px;
        justify-content: space-between;
        margin: 0 0 12px;
    }

    body.white-content .report-results .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    body.white-content .report-results .dt-buttons .dt-button,
    body.white-content .report-results .dt-buttons .btn {
        background: var(--accent) !important;
        border: 1px solid var(--accent) !important;
        border-radius: 9px !important;
        box-shadow: 0 7px 16px rgba(79, 70, 229, 0.16) !important;
        color: #ffffff !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        margin: 0 !important;
        min-height: 38px;
        padding: 11px 14px !important;
        text-transform: none;
    }

    body.white-content .report-results .dt-buttons .dt-button:hover,
    body.white-content .report-results .dt-buttons .btn:hover,
    body.white-content .report-results .dt-buttons .dt-button:focus,
    body.white-content .report-results .dt-buttons .btn:focus {
        background: var(--accent-strong, var(--accent)) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    body.white-content .report-results .dataTables_filter {
        float: none;
        margin: 0;
    }

    body.white-content .report-results .dataTables_filter label {
        display: block;
        margin: 0;
    }

    body.white-content .report-results .dataTables_filter input {
        width: min(280px, 100%) !important;
        height: 40px !important;
        margin: 0 !important;
        padding: 8px 12px;
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 10px !important;
        color: var(--text-1) !important;
    }

    body.white-content .report-results .dataTables_length {
        color: var(--text-2) !important;
        float: none;
        font-size: 12px;
        font-weight: 700;
        margin: 0;
    }

    body.white-content .report-results .dataTables_length label {
        align-items: center;
        display: flex;
        gap: 8px;
        margin: 0;
    }

    body.white-content .report-results .dataTables_length select {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 9px;
        color: var(--text-1) !important;
        min-height: 38px;
        padding: 6px 10px;
    }

    body.white-content .report-results .report-datatable-foot {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
        padding-top: 12px;
    }

    body.white-content .report-results .dataTables_info {
        color: var(--text-2) !important;
        font-size: 12px;
        font-weight: 600;
        padding: 0 !important;
    }

    body.white-content .report-results .dataTables_paginate {
        float: none;
        margin: 0;
        padding: 0;
    }

    body.white-content .report-results .dataTables_paginate .paginate_button {
        border: 1px solid transparent !important;
        border-radius: 9px !important;
        color: var(--text-2) !important;
        font-weight: 800;
        margin: 0 2px !important;
        min-width: 34px;
        padding: 7px 10px !important;
    }

    body.white-content .report-results .dataTables_paginate .paginate_button.current,
    body.white-content .report-results .dataTables_paginate .paginate_button.current:hover {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #ffffff !important;
    }

    body.white-content .report-results .dataTables_paginate .paginate_button:hover {
        background: var(--accent-bg) !important;
        border-color: var(--accent-bg) !important;
        color: var(--accent) !important;
    }

    @media (max-width: 575.98px) {
        body.white-content .report-filters,
        body.white-content .report-results {
            padding: 14px !important;
        }

        body.white-content .report-filter-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        body.white-content .report-filter-actions .btn {
            flex: 1 1 0;
        }

        body.white-content table.report-table {
            min-width: 640px;
        }

        body.white-content .report-results .report-datatable-toolbar,
        body.white-content .report-results .dt-buttons,
        body.white-content .report-results .dataTables_filter,
        body.white-content .report-results .dataTables_filter label,
        body.white-content .report-results .dataTables_filter input {
            width: 100%;
        }

        body.white-content .report-results .dt-buttons .dt-button,
        body.white-content .report-results .dt-buttons .btn {
            flex: 1 1 auto;
        }

        body.white-content .report-results .report-datatable-foot {
            align-items: stretch;
            flex-direction: column;
        }
    }

    /* Shared visual shell for the five reports exposed in the Solent sidebar. */
    body.white-content .solent-report-page {
        display: grid;
        gap: 18px;
        margin: 0 auto;
        max-width: 1500px;
        min-width: 0;
        width: 100%;
    }

    body.white-content .solent-report-page .report-page-header {
        background: var(--surface);
        border: 1px solid var(--border);
        border-inline-start: 4px solid var(--accent);
        border-radius: 18px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        min-width: 0;
        overflow: hidden;
        padding: 20px;
    }

    body.white-content .solent-report-page .report-page-heading,
    body.white-content .solent-report-page .report-page-heading-copy,
    body.white-content .solent-report-page .report-section-heading-copy {
        min-width: 0;
    }

    body.white-content .solent-report-page .report-page-heading {
        align-items: center;
        display: flex;
        gap: 14px;
    }

    body.white-content .solent-report-page .report-page-icon,
    body.white-content .solent-report-page .report-section-icon,
    body.white-content .solent-report-page .report-summary-icon {
        align-items: center;
        background: var(--accent-bg);
        border: 1px solid color-mix(in srgb, var(--accent) 22%, transparent);
        color: var(--accent);
        display: inline-flex;
        flex: 0 0 auto;
        justify-content: center;
    }

    body.white-content .solent-report-page .report-page-icon {
        border-radius: 14px;
        font-size: 20px;
        height: 50px;
        width: 50px;
    }

    body.white-content .solent-report-page .report-page-eyebrow {
        color: var(--accent);
        display: block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.09em;
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    body.white-content .solent-report-page .report-page-heading h1 {
        color: var(--text-1);
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -0.035em;
        line-height: 1.15;
        margin: 0;
    }

    body.white-content .solent-report-page .report-page-heading p {
        color: var(--text-2);
        font-size: 13px;
        line-height: 1.55;
        margin: 5px 0 0;
        max-width: 780px;
    }

    body.white-content .solent-report-page .report-page-navigation {
        display: flex;
        gap: 8px;
        margin-top: 18px;
        max-width: 100%;
        overflow-x: auto;
        padding: 1px 1px 4px;
        scrollbar-width: thin;
    }

    body.white-content .solent-report-page .report-page-navigation-link {
        align-items: center;
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-2) !important;
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 700;
        gap: 7px;
        min-height: 40px;
        padding: 9px 12px;
        text-decoration: none !important;
        transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease, transform 160ms ease;
        white-space: nowrap;
    }

    body.white-content .solent-report-page .report-page-navigation-link:hover,
    body.white-content .solent-report-page .report-page-navigation-link:focus-visible {
        background: var(--accent-bg);
        border-color: color-mix(in srgb, var(--accent) 32%, var(--border));
        color: var(--accent) !important;
        transform: translateY(-1px);
    }

    body.white-content .solent-report-page .report-page-navigation-link.is-active {
        background: var(--accent);
        border-color: var(--accent);
        color: #ffffff !important;
        box-shadow: 0 8px 18px color-mix(in srgb, var(--accent) 22%, transparent);
    }

    body.white-content .solent-report-page .report-filters,
    body.white-content .solent-report-page .report-results {
        margin: 0 !important;
    }

    body.white-content .solent-report-page .report-section-heading {
        align-items: center;
        border-bottom: 1px solid var(--border);
        display: flex;
        gap: 14px;
        justify-content: space-between;
        margin: 0 0 18px;
        padding: 0 0 15px;
    }

    body.white-content .solent-report-page .report-section-heading-copy {
        align-items: center;
        display: flex;
        gap: 11px;
    }

    body.white-content .solent-report-page .report-section-icon {
        border-radius: 10px;
        font-size: 14px;
        height: 38px;
        width: 38px;
    }

    body.white-content .solent-report-page .report-section-heading h2 {
        color: var(--text-1);
        font-size: 16px;
        font-weight: 800;
        line-height: 1.25;
        margin: 0;
    }

    body.white-content .solent-report-page .report-section-heading p {
        color: var(--text-2);
        font-size: 12px;
        line-height: 1.45;
        margin: 3px 0 0;
    }

    body.white-content .solent-report-page .report-result-count {
        align-items: baseline;
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: 999px;
        color: var(--text-2);
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 11px;
        font-weight: 700;
        gap: 4px;
        padding: 7px 11px;
        white-space: nowrap;
    }

    body.white-content .solent-report-page .report-result-count strong {
        color: var(--text-1);
        font-size: 13px;
    }

    body.white-content .solent-report-page .report-filter-actions .btn {
        align-items: center;
        display: inline-flex;
        gap: 8px;
        justify-content: center;
    }

    body.white-content .solent-report-page .report-filter-actions .btn-primary {
        background: var(--accent) !important;
        border: 1px solid var(--accent) !important;
        box-shadow: 0 8px 18px color-mix(in srgb, var(--accent) 20%, transparent) !important;
        color: #ffffff !important;
    }

    body.white-content .solent-report-page .report-filter-actions .btn-secondary {
        background: var(--surface-raised) !important;
        border: 1px solid var(--border) !important;
        box-shadow: none !important;
        color: var(--text-1) !important;
    }

    body.white-content .solent-report-page .report-summary-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
        margin: 0 0 16px;
    }

    body.white-content .solent-report-page .report-summary-card {
        align-items: center;
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: 13px;
        display: flex;
        gap: 11px;
        min-width: 0;
        padding: 13px 14px;
    }

    body.white-content .solent-report-page .report-summary-icon {
        border-radius: 10px;
        font-size: 14px;
        height: 38px;
        width: 38px;
    }

    body.white-content .solent-report-page .report-summary-label {
        color: var(--text-2);
        display: block;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.25;
        margin-bottom: 3px;
    }

    body.white-content .solent-report-page .report-summary-value {
        color: var(--text-1);
        display: block;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.1;
    }

    body.white-content .solent-report-page table.report-table.materials-usage-table thead th,
    body.white-content .solent-report-page table.report-table thead th {
        background: var(--surface-raised) !important;
        border-color: var(--border) !important;
        color: var(--text-1) !important;
    }

    body.white-content .solent-report-page table.report-table tbody td:first-child,
    body.white-content .solent-report-page table.report-table thead th:first-child {
        text-align: start;
    }

    body.white-content .solent-report-page .report-results .dataTables_wrapper {
        overflow: visible;
    }

    body.white-content .solent-report-page .report-results .dt-buttons .dt-button,
    body.white-content .solent-report-page .report-results .dt-buttons .btn {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        box-shadow: none !important;
        color: var(--text-1) !important;
    }

    body.white-content .solent-report-page .report-results .dt-buttons .dt-button:hover,
    body.white-content .solent-report-page .report-results .dt-buttons .btn:hover,
    body.white-content .solent-report-page .report-results .dt-buttons .dt-button:focus,
    body.white-content .solent-report-page .report-results .dt-buttons .btn:focus {
        background: var(--accent-bg) !important;
        border-color: color-mix(in srgb, var(--accent) 30%, var(--border)) !important;
        color: var(--accent) !important;
    }

    body.white-content .solent-report-page .materials-usage-table tbody tr {
        cursor: pointer;
    }

    @media (max-width: 767.98px) {
        body.white-content .solent-report-page {
            gap: 14px;
        }

        body.white-content .solent-report-page .report-page-header,
        body.white-content .solent-report-page .report-filters,
        body.white-content .solent-report-page .report-results {
            border-radius: 14px;
            padding: 15px !important;
        }

        body.white-content .solent-report-page .report-page-navigation {
            margin-inline: -15px;
            padding-inline: 15px;
        }

        body.white-content .solent-report-page .report-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
        }
    }

    @media (max-width: 575.98px) {
        body.white-content .solent-report-page .report-page-heading {
            align-items: flex-start;
        }

        body.white-content .solent-report-page .report-page-icon {
            border-radius: 12px;
            height: 44px;
            width: 44px;
        }

        body.white-content .solent-report-page .report-page-heading h1 {
            font-size: 22px;
        }

        body.white-content .solent-report-page .report-filter-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        body.white-content .solent-report-page .report-filter-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        body.white-content .solent-report-page .report-filter-actions .btn:only-child {
            grid-column: 1 / -1;
        }

        body.white-content .solent-report-page .report-section-heading {
            align-items: flex-start;
        }

        body.white-content .solent-report-page .report-result-count {
            font-size: 0;
        }

        body.white-content .solent-report-page .report-result-count strong {
            font-size: 13px;
        }

        body.white-content .solent-report-page .report-summary-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media print {
        body.white-content .solent-report-page .report-page-header,
        body.white-content .solent-report-page .report-filters,
        body.white-content .solent-report-page .report-results .report-datatable-toolbar,
        body.white-content .solent-report-page .report-results .report-datatable-foot {
            display: none !important;
        }

        body.white-content .solent-report-page .report-results {
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>

@push('js')
    <script>
        $(function () {
            window.setTimeout(function () {
                if ($.fn.selectpicker) {
                    $('.report-filters .selectpicker').selectpicker('refresh');
                }
                if (!$.fn.DataTable) {
                    return;
                }

                function normalizeReportTable(table) {
                    var $table = $(table);
                    var $bodyHeader = $table.find('tbody > tr.tableHeaderRow').first();

                    if (!$bodyHeader.length) {
                        return;
                    }

                    var $thead = $table.children('thead');
                    if (!$thead.length) {
                        $thead = $('<thead></thead>').prependTo($table);
                    }

                    $thead.append($bodyHeader);
                }

                $('table.report-table').not('#datatable').each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        return;
                    }

                    normalizeReportTable(this);

                    $(this).DataTable({
                        autoWidth: false,
                        buttons: window.solentDataTableButtons ? window.solentDataTableButtons(false) : [],
                        info: false,
                        lengthChange: false,
                        ordering: false,
                        paging: false,
                        searching: true,
                        dom: "<'report-datatable-toolbar'Bf>rt",
                        language: Object.assign({}, window.SolentI18n?.dataTables || {}, {
                            search: '',
                            searchPlaceholder: @json(trans('ui.dom')['Search report...'] ?? 'Search report...')
                        })
                    });
                });
            }, 0);
        });
    </script>
@endpush
