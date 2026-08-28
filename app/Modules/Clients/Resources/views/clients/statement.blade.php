@extends(($printMode ?? false) ? 'layouts.print' : 'layouts.app', ['pageSlug' => 'Statement Of Account'])

@section('content')
    @php
        $currencyLabel = $currencyLabel ?? (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
        $statementLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path'));
        $statementBrand = $brandingName ?? config('branding.defaults.name', 'Solent');
        $invoicesAmount = $transactions
            ? $transactions->whereNotNull('case_id')->whereNull('discount_title')->sum('amount')
            : 0;
        $discounts = $transactions
            ? $transactions->whereNotNull('case_id')->whereNotNull('discount_title')->sum('amount')
            : 0;
        $amountPaid = $transactions ? $transactions->whereNull('case_id')->sum('amount') : 0;
        $openingBalance = $openingBalance ?? 0;
        $balanceDue = $invoicesAmount - $amountPaid + $openingBalance + $discounts;
        $runningBalance = $openingBalance;
        $discountExists = false;
        $statementUi = trans('ui.dom');
        $statementText = static function (string $key) use ($statementUi): string {
            return (string) ($statementUi[$key] ?? $key);
        };
        $statementPrintUrl = route('client-statement-admin', [
            'id' => $client->id,
            'from' => substr($from, 0, 10),
            'to' => substr($to, 0, 10),
            'print' => 1,
        ]);

        $clientRawName = trim((string) $client->name);
        $doctorPrefix = $statementText('Doctor prefix');
        if (preg_match('/^(د[\.\s\/]|Dr[\.\s\/]|Doctor\s)/iu', $clientRawName)) {
            $clientDisplayName = $clientRawName;
        } else {
            $clientDisplayName = $doctorPrefix . ' ' . $clientRawName;
        }
    @endphp

    <style>
        .statement-page {
            --st-primary: var(--brand-primary, #c89b3c);
            --st-primary-hover: #b3852c;
            --st-primary-light: #fdfaf3;
            --st-primary-border: #ebd9b5;
            --st-dark: #0f172a;
            --st-dark-muted: #1e293b;
            --st-ink: #1e293b;
            --st-muted: #64748b;
            --st-muted-light: #94a3b8;
            --st-border: #e2e8f0;
            --st-border-subtle: #f1f5f9;
            --st-surface: #ffffff;
            --st-soft: #f8fafc;
            --st-badge-invoice: #e2e8f0;
            --st-badge-invoice-text: #1e293b;
            --st-badge-payment: #d1fae5;
            --st-badge-payment-text: #065f46;
            --st-badge-discount: #fef3c7;
            --st-badge-discount-text: #92400e;

            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0 40px;
            color: var(--st-ink);
            text-align: start;
            font-family: inherit;
        }

        .statement-page,
        .statement-page * {
            box-sizing: border-box;
        }

        /* Interactive Toolbar (Screen Only) */
        .statement-toolbar {
            display: grid;
            grid-template-columns: minmax(160px, 1fr) minmax(160px, 1fr) auto;
            align-items: end;
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px 20px;
            background: #ffffff;
            border: 1px solid var(--st-border);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .statement-field {
            display: grid;
            gap: 6px;
        }

        .statement-field label {
            margin: 0;
            color: var(--st-muted) !important;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .statement-field .form-control {
            min-height: 42px;
            padding: 8px 12px;
            color: var(--st-dark);
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .statement-field .form-control:focus {
            border-color: var(--st-primary);
            box-shadow: 0 0 0 3px rgba(200, 155, 60, 0.15);
        }

        .statement-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .statement-action {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0 !important;
            padding: 8px 18px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
        }

        .statement-action--filter {
            color: #ffffff !important;
            background: var(--st-dark) !important;
            border-color: var(--st-dark) !important;
        }

        .statement-action--filter:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
            transform: translateY(-1px);
        }

        .statement-action--all {
            color: #334155 !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        .statement-action--all:hover {
            background: var(--st-soft) !important;
            border-color: #94a3b8 !important;
        }

        .statement-action--print {
            color: #ffffff !important;
            background: var(--st-primary) !important;
            border-color: var(--st-primary) !important;
            box-shadow: 0 2px 8px rgba(200, 155, 60, 0.25);
        }

        .statement-action--print:hover {
            background: var(--st-primary-hover) !important;
            border-color: var(--st-primary-hover) !important;
            transform: translateY(-1px);
        }

        .statement-action--print i {
            color: #ffffff !important;
        }

        /* Document Container */
        .statement-document {
            position: relative;
            background: var(--st-surface);
            border: 1px solid var(--st-border);
            border-radius: 14px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        /* Refined Top Accent Bar */
        .statement-accent-stripe {
            height: 4px;
            background: linear-gradient(90deg, var(--st-primary) 0%, #e6c77a 50%, var(--st-dark) 100%);
            width: 100%;
        }

        /* Header Section */
        .statement-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 28px 36px 24px;
            background: #ffffff;
            border-bottom: 1px solid var(--st-border);
        }

        .statement-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .statement-brand__logo {
            width: auto;
            max-width: 140px;
            max-height: 44px;
            height: auto;
            object-fit: contain;
        }

        .statement-brand__divider {
            width: 1px;
            height: 34px;
            background: var(--st-border);
        }

        .statement-brand__copy strong {
            display: block;
            color: var(--st-dark);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .statement-brand__copy span {
            display: block;
            margin-top: 2px;
            color: var(--st-muted);
            font-size: 11px;
            font-weight: 500;
        }

        .statement-heading {
            text-align: end;
        }

        .statement-heading h1 {
            margin: 0;
            color: var(--st-dark) !important;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .statement-heading__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            padding: 4px 12px;
            background: var(--st-soft);
            border: 1px solid var(--st-border);
            border-radius: 20px;
            color: var(--st-slate-700);
            font-size: 12px;
            font-weight: 600;
            direction: ltr;
            unicode-bidi: embed;
        }

        .statement-heading__badge i {
            color: var(--st-primary);
            font-size: 11px;
        }

        /* Body Content Area */
        .statement-content {
            padding: 28px 36px 36px;
        }

        /* Recipient & Period Card */
        .statement-recipient-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 22px;
            margin-bottom: 24px;
            background: var(--st-soft);
            border: 1px solid var(--st-border);
            border-radius: 10px;
            position: relative;
        }

        .statement-recipient-card::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            inset-inline-start: 0;
            width: 4px;
            background: var(--st-primary);
            border-start-start-radius: 10px;
            border-end-start-radius: 10px;
        }

        .statement-recipient-info {
            min-width: 0;
        }

        .statement-recipient-eyebrow {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
            color: var(--st-primary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .statement-recipient-name {
            margin: 0;
            color: var(--st-dark);
            font-size: 20px;
            font-weight: 800;
            line-height: 1.3;
        }

        .statement-recipient-sub {
            margin: 4px 0 0;
            color: var(--st-muted);
            font-size: 12px;
            line-height: 1.4;
        }

        .statement-recipient-meta {
            text-align: end;
            flex-shrink: 0;
        }

        .statement-recipient-meta__item {
            color: var(--st-muted);
            font-size: 11px;
            line-height: 1.5;
        }

        .statement-recipient-meta__item strong {
            color: var(--st-dark);
            font-weight: 700;
        }

        /* Financial Summary Cards Grid */
        .statement-summary {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }

        .statement-metric {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            padding: 14px 16px;
            background: #ffffff;
            border: 1px solid var(--st-border);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            transition: border-color 0.15s ease;
        }

        .statement-metric__label {
            color: var(--st-muted);
            font-size: 11px;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .statement-metric__value {
            color: var(--st-dark);
            font-size: 16px;
            font-weight: 800;
            line-height: 1.2;
            direction: ltr;
            unicode-bidi: embed;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .statement-metric__unit {
            font-size: 11px;
            font-weight: 600;
            color: var(--st-muted);
            margin-inline-start: 3px;
        }

        /* Outstanding Balance Card (High-End Contrast) */
        .statement-metric--balance {
            background: var(--st-dark);
            border-color: var(--st-dark);
            color: #ffffff;
            position: relative;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .statement-metric--balance .statement-metric__label {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .statement-metric--balance .statement-metric__label::after {
            content: "";
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--st-primary);
        }

        .statement-metric--balance .statement-metric__value {
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
        }

        .statement-metric--balance .statement-metric__unit {
            color: var(--st-primary);
            font-weight: 700;
        }

        /* Transactions Section */
        .statement-table-section {
            border: 1px solid var(--st-border);
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }

        .statement-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 20px;
            background: #ffffff;
            border-bottom: 1px solid var(--st-border);
        }

        .statement-table-heading h2 {
            margin: 0;
            color: var(--st-dark);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .statement-table-heading__count {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            background: var(--st-soft);
            border: 1px solid var(--st-border);
            border-radius: 12px;
            color: var(--st-muted);
            font-size: 11px;
            font-weight: 700;
        }

        .statement-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .statement-table {
            width: 100%;
            min-width: 720px;
            margin: 0;
            border-collapse: collapse;
        }

        /* Clean, refined slate table header */
        .statement-table thead {
            background: var(--st-dark);
        }

        .statement-table th {
            padding: 10px 16px !important;
            color: #f8fafc !important;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-align: start;
            text-transform: uppercase;
            border: 0;
            white-space: nowrap;
        }

        .statement-table td {
            padding: 12px 16px;
            color: #334155;
            font-size: 13px;
            vertical-align: middle;
            border-top: 1px solid var(--st-border-subtle);
        }

        .statement-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .statement-table tbody tr:hover {
            background: #f1f5f9;
        }

        /* Column Alignments & Badges */
        .statement-table .statement-col-amount {
            text-align: end;
            white-space: nowrap;
            direction: ltr;
            unicode-bidi: embed;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }

        .statement-table .statement-col-date {
            white-space: nowrap;
            direction: ltr;
            unicode-bidi: embed;
            font-variant-numeric: tabular-nums;
            color: var(--st-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .statement-table .statement-col-balance {
            color: var(--st-dark);
            font-weight: 800;
        }

        .statement-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .statement-page .statement-badge.statement-badge--invoice {
            background: var(--st-badge-invoice);
            color: var(--st-badge-invoice-text) !important;
            border: 1px solid #cbd5e1 !important;
        }

        .statement-page .statement-badge.statement-badge--payment {
            background: var(--st-badge-payment);
            color: var(--st-badge-payment-text) !important;
            border: 1px solid #6ee7b7 !important;
        }

        .statement-page .statement-badge.statement-badge--discount {
            background: var(--st-badge-discount);
            color: var(--st-badge-discount-text) !important;
            border: 1px solid #fcd34d !important;
        }

        .statement-empty {
            padding: 42px 20px !important;
            text-align: center;
            color: var(--st-muted-light) !important;
        }

        .statement-empty i {
            display: block;
            font-size: 28px;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .statement-empty span {
            font-size: 13px;
            font-weight: 600;
            color: var(--st-muted);
        }

        /* Statement Footer */
        .statement-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 20px;
            background: var(--st-soft);
            border-top: 1px solid var(--st-border);
        }

        .statement-footer__note {
            color: var(--st-muted);
            font-size: 11px;
            font-weight: 500;
        }

        .statement-footer__balance {
            display: flex;
            align-items: baseline;
            gap: 10px;
            color: var(--st-dark);
        }

        .statement-footer__balance-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--st-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .statement-footer__balance-amount {
            font-size: 18px;
            font-weight: 900;
            color: var(--st-dark);
            direction: ltr;
            unicode-bidi: embed;
            font-variant-numeric: tabular-nums;
        }

        .statement-footer__balance-amount span {
            font-size: 12px;
            font-weight: 700;
            color: var(--st-primary);
            margin-inline-start: 4px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1199.98px) {
            .statement-summary {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .statement-metric--balance {
                grid-column: span 2;
            }
        }

        @media (max-width: 767.98px) {
            .statement-page {
                padding-top: 10px;
            }

            .statement-header,
            .statement-recipient-card,
            .statement-toolbar {
                flex-direction: column;
                align-items: stretch;
                grid-template-columns: 1fr;
            }

            .statement-heading,
            .statement-recipient-meta {
                text-align: start;
            }

            .statement-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .statement-action {
                width: 100%;
                padding-inline: 8px;
            }

            .statement-content {
                padding: 20px 16px;
            }

            .statement-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .statement-metric--balance {
                grid-column: 1 / -1;
            }

            .statement-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
        }

        /* High-End Print Stylesheet */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 8mm 8mm 8mm;
            }

            html,
            body,
            body.white-content,
            .main-panel,
            .main-panel > .content,
            .print-document {
                background: #ffffff !important;
                color: #0f172a !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body.white-content .wrapper > .sidebar,
            body.white-content .solent-floating-topbar,
            body.white-content .solent-quick-nav,
            body.white-content .navbar,
            body.white-content .footer,
            .sidebar,
            .solent-floating-topbar,
            .solent-quick-nav,
            .navbar,
            .footer,
            .statement-toolbar {
                display: none !important;
            }

            body.white-content .wrapper,
            body.white-content .wrapper > .main-panel,
            .main-panel,
            .main-panel > .content {
                display: block !important;
                float: none !important;
                margin: 0 !important;
                max-width: none !important;
                min-height: 0 !important;
                padding: 0 !important;
                transform: none !important;
                width: 100% !important;
            }

            .statement-page {
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .statement-document {
                overflow: visible !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .statement-accent-stripe {
                height: 3px !important;
            }

            .statement-header {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                padding: 7px 0 8px !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }

            .statement-brand {
                gap: 10px !important;
            }

            .statement-brand__logo {
                max-width: 100px !important;
                max-height: 34px !important;
            }

            .statement-brand__divider {
                height: 28px !important;
            }

            .statement-brand__copy strong {
                font-size: 13px !important;
            }

            .statement-brand__copy span {
                margin-top: 1px !important;
                font-size: 9.5px !important;
            }

            .statement-heading {
                flex: 0 0 auto;
                text-align: end !important;
            }

            .statement-heading h1 {
                font-size: 17px !important;
            }

            .statement-heading__badge {
                gap: 4px !important;
                margin-top: 3px !important;
                padding: 3px 8px !important;
                font-size: 10px !important;
            }

            .statement-content {
                padding: 8px 0 0 !important;
            }

            .statement-recipient-card {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                padding: 8px 11px !important;
                margin-bottom: 10px !important;
                border: 1px solid #cbd5e1 !important;
                background: #f8fafc !important;
            }

            .statement-recipient-eyebrow {
                gap: 4px !important;
                margin-bottom: 1px !important;
                font-size: 8.5px !important;
            }

            .statement-recipient-name {
                font-size: 14px !important;
                line-height: 1.2 !important;
            }

            .statement-recipient-sub {
                margin-top: 2px !important;
                font-size: 9.5px !important;
                line-height: 1.3 !important;
            }

            .statement-recipient-meta {
                flex: 0 0 auto;
                text-align: end !important;
            }

            .statement-recipient-meta__item {
                font-size: 9.5px !important;
                line-height: 1.35 !important;
            }

            .statement-summary {
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                gap: 8px !important;
                margin-bottom: 16px !important;
            }

            .statement-metric {
                padding: 8px 10px !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .statement-metric__label {
                font-size: 9px !important;
                margin-bottom: 4px !important;
            }

            .statement-metric__value {
                font-size: 13px !important;
            }

            .statement-metric--balance {
                background: #0f172a !important;
                border-color: #0f172a !important;
                color: #ffffff !important;
            }

            .statement-metric--balance .statement-metric__label {
                color: #cbd5e1 !important;
            }

            .statement-metric--balance .statement-metric__value {
                color: #ffffff !important;
            }

            .statement-metric--balance .statement-metric__unit {
                color: #e6c77a !important;
            }

            .statement-table-section {
                border: 1px solid #cbd5e1 !important;
                border-radius: 6px !important;
                overflow: visible !important;
            }

            .statement-table-heading {
                padding: 8px 12px !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }

            .statement-table-wrap {
                overflow: visible !important;
            }

            .statement-table {
                min-width: 0 !important;
            }

            .statement-table thead {
                display: table-header-group !important;
                background: #0f172a !important;
            }

            .statement-table th {
                padding: 6px 10px !important;
                font-size: 9.5px !important;
                color: #ffffff !important;
                background: #0f172a !important;
            }

            .statement-table td {
                padding: 6px 10px !important;
                font-size: 9.5px !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            .statement-table tr {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .statement-badge {
                padding: 1px 5px !important;
                font-size: 8.5px !important;
            }

            .statement-footer {
                padding: 10px 12px !important;
                background: #f8fafc !important;
                border-top: 1px solid #cbd5e1 !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .statement-footer__balance-amount {
                font-size: 15px !important;
            }
        }
    </style>

    <main class="statement-page">
        <form class="statement-toolbar" method="GET" action="{{ route('client-statement-admin', $client->id) }}">
            <input type="hidden" name="id" value="{{ $client->id }}">

            <div class="statement-field">
                <label class="solent-filter-label" for="statement-from"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>{{ $statementText('From date') }}</span></label>
                <input class="form-control" id="statement-from" type="date" name="from" value="{{ $from }}">
            </div>

            <div class="statement-field">
                <label class="solent-filter-label" for="statement-to"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>{{ $statementText('To date') }}</span></label>
                <input class="form-control" id="statement-to" type="date" name="to" value="{{ $to }}">
            </div>

            <div class="statement-actions">
                <button class="btn statement-action statement-action--filter" type="submit">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    {{ $statementText('Apply') }}
                </button>
                <button class="btn statement-action statement-action--all" type="button"
                    onclick="window.location='{{ route('client-statement-admin', ['id' => $client->id, 'allTime' => 1]) }}'">
                    {{ $statementText('All time') }}
                </button>
                <a class="btn statement-action statement-action--print" href="{{ $statementPrintUrl }}" target="_blank"
                    rel="noopener" aria-label="{{ $statementText('Print account statement') }}">
                    <i class="fa-solid fa-print" aria-hidden="true"></i>
                    {{ $statementText('Print') }}
                </a>
            </div>
        </form>

        <article class="statement-document">
            <div class="statement-accent-stripe"></div>

            <header class="statement-header">
                <div class="statement-brand">
                    <img class="statement-brand__logo" src="{{ $statementLogo }}" alt="{{ $statementBrand }} {{ $statementText('logo') }}">
                    <div class="statement-brand__divider"></div>
                    <div class="statement-brand__copy">
                        <strong>{{ $statementBrand }}</strong>
                        <span>{{ app()->isLocale('ar') ? 'مختبرات صناعة وتجميل الأسنان' : config('branding.defaults.copy.tagline', 'Precision dental labs, refined.') }}</span>
                    </div>
                </div>

                <div class="statement-heading">
                    <h1>{{ $statementText('Account Statement') }}</h1>
                    <div class="statement-heading__badge">
                        <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
                        <span>{{ substr($from, 0, 10) }} — {{ substr($to, 0, 10) }}</span>
                    </div>
                </div>
            </header>

            <div class="statement-content">
                <section class="statement-recipient-card" aria-label="{{ $statementText('Account overview') }}">
                    <div class="statement-recipient-info">
                        <div class="statement-recipient-eyebrow">
                            <i class="fa-solid fa-user-doctor" aria-hidden="true"></i>
                            <span>{{ $statementText('Prepared for') }}</span>
                        </div>
                        <h2 class="statement-recipient-name">{{ $clientDisplayName }}</h2>
                        <p class="statement-recipient-sub">{{ $statementText('A complete record of invoices, payments, discounts, and the resulting account balance.') }}</p>
                    </div>

                    <div class="statement-recipient-meta">
                        <div class="statement-recipient-meta__item">
                            {{ $statementText('Doctor ID') }}: <strong>#{{ $client->id }}</strong>
                        </div>
                        @if (!empty($client->phone))
                            <div class="statement-recipient-meta__item">
                                <span style="direction: ltr; display: inline-block;">{{ $client->phone }}</span>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="statement-summary" aria-label="{{ $statementText('Account overview') }}">
                    <div class="statement-metric">
                        <span class="statement-metric__label">{{ $statementText('Opening balance') }}</span>
                        <div class="statement-metric__value">
                            {{ number_format((float) $openingBalance, 2) }}
                            <span class="statement-metric__unit">{{ $currencyLabel }}</span>
                        </div>
                    </div>
                    <div class="statement-metric">
                        <span class="statement-metric__label">{{ $statementText('Invoices') }} (+)</span>
                        <div class="statement-metric__value">
                            {{ number_format((float) $invoicesAmount, 2) }}
                            <span class="statement-metric__unit">{{ $currencyLabel }}</span>
                        </div>
                    </div>
                    <div class="statement-metric">
                        <span class="statement-metric__label">{{ $statementText('Payments') }} (&minus;)</span>
                        <div class="statement-metric__value">
                            {{ number_format((float) $amountPaid, 2) }}
                            <span class="statement-metric__unit">{{ $currencyLabel }}</span>
                        </div>
                    </div>
                    <div class="statement-metric">
                        <span class="statement-metric__label">{{ $statementText('Discounts') }} (&minus;)</span>
                        <div class="statement-metric__value">
                            {{ number_format((float) $discounts, 2) }}
                            <span class="statement-metric__unit">{{ $currencyLabel }}</span>
                        </div>
                    </div>
                    <div class="statement-metric statement-metric--balance">
                        <span class="statement-metric__label">{{ $statementText('Balance due') }}</span>
                        <div class="statement-metric__value">
                            {{ number_format((float) $balanceDue, 2) }}
                            <span class="statement-metric__unit">{{ $currencyLabel }}</span>
                        </div>
                    </div>
                </section>

                <section class="statement-table-section" aria-labelledby="statement-transactions-title">
                    <div class="statement-table-heading">
                        <h2 id="statement-transactions-title">{{ $statementText('Transaction history') }}</h2>
                        <span class="statement-table-heading__count">
                            {{ str_replace(':count', $transactions->count(), $statementText('Transactions count')) }}
                        </span>
                    </div>

                    <div class="statement-table-wrap">
                        <table class="statement-table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 14%;">{{ $statementText('Date') }}</th>
                                    <th scope="col" style="width: 13%;">{{ $statementText('Transaction') }}</th>
                                    <th scope="col" style="width: 33%;">{{ $statementText('Description') }}</th>
                                    <th class="statement-col-amount" scope="col" style="width: 13%;">{{ $statementText('Payment') }}</th>
                                    <th class="statement-col-amount" scope="col" style="width: 13%;">{{ $statementText('Amount') }}</th>
                                    <th class="statement-col-amount" scope="col" style="width: 14%;">{{ $statementText('Balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    @php
                                        if (isset($transaction->case_id)) {
                                            $runningBalance += $transaction->amount;
                                        } else {
                                            $runningBalance -= $transaction->amount;
                                        }

                                        if (isset($transaction->discount)) {
                                            $discountExists = true;
                                        }

                                        $isInvoice = isset($transaction->case_id) && isset($transaction->case);
                                        $isDiscount = isset($transaction->case_id) && !isset($transaction->case);
                                        $isPayment = !isset($transaction->case_id);

                                        $transactionType = $isInvoice
                                            ? $statementText('Invoice')
                                            : ($isDiscount ? $statementText('Discount') : $statementText('Payment'));

                                        $description = isset($transaction->case_id)
                                            ? (isset($transaction->case)
                                                ? ($transaction->rejection_invoice == 1
                                                    ? $transaction->case->patient_name . ' / مرتجع'
                                                    : str_replace('/ تعديل', '', $transaction->case->patient_name))
                                                : $transaction->discount_title)
                                            : $transaction->notes;

                                        $badgeClass = $isInvoice
                                            ? 'statement-badge--invoice'
                                            : ($isDiscount ? 'statement-badge--discount' : 'statement-badge--payment');
                                    @endphp
                                    <tr>
                                        <td class="statement-col-date">{{ substr($transaction->created_at, 0, 10) }}</td>
                                        <td>
                                            <span class="statement-badge {{ $badgeClass }}">{{ $transactionType }}</span>
                                        </td>
                                        <td>{{ $description }}</td>
                                        <td class="statement-col-amount" style="color: #059669;">
                                            {{ isset($transaction->case_id) ? '—' : number_format((float) $transaction->amount, 2) }}
                                        </td>
                                        <td class="statement-col-amount">
                                            {{ isset($transaction->case_id) ? number_format((float) $transaction->amount, 2) : '—' }}
                                            {{ isset($transaction->discount) ? '*' : '' }}
                                        </td>
                                        <td class="statement-col-amount statement-col-balance">
                                            {{ number_format((float) $runningBalance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="statement-empty" colspan="6">
                                            <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                                            <span>{{ $statementText('No transactions were recorded for this period.') }}</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <footer class="statement-footer">
                        <div class="statement-footer__note">
                            {{ $discountExists ? $statementText('* Discount applied to one or more transactions.') : $statementText('Generated from the account ledger for the selected period.') }}
                        </div>
                        <div class="statement-footer__balance">
                            <span class="statement-footer__balance-label">{{ $statementText('Closing balance') }}:</span>
                            <div class="statement-footer__balance-amount">
                                {{ number_format((float) $runningBalance, 2) }}
                                <span>{{ $currencyLabel }}</span>
                            </div>
                        </div>
                    </footer>
                </section>
            </div>
        </article>
    </main>
@endsection

@push('js')
    @if ($printMode ?? false)
    <script>
        window.addEventListener('load', function () {
            window.print();
        });

        window.addEventListener('afterprint', function () {
            window.close();
        });
    </script>
    @endif
@endpush
