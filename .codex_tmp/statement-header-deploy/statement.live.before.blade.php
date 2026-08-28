@extends(($printMode ?? false) ? 'layouts.print' : 'layouts.app', ['pageSlug' => 'Statement Of Account'])

@section('content')
    @php
        $currencyLabel = $currencyLabel ?? (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
        $statementLogo = asset($brandingMarkPath ?? config('branding.defaults.mark_path'));
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
    @endphp

    <style>
        .statement-page {
            --statement-primary: {{ config('branding.defaults.primary_color') }};
            --statement-accent: #6366f1;
            --statement-ink: #0f172a;
            --statement-muted: #64748b;
            --statement-border: #e2e8f0;
            --statement-surface: #ffffff;
            --statement-soft: #f8fafc;
            max-width: 1440px;
            margin: 0 auto;
            padding: 18px 0 36px;
            color: var(--statement-ink);
            text-align: start;
        }

        .statement-page,
        .statement-page * {
            box-sizing: border-box;
        }

        .statement-document {
            overflow: hidden;
            background: var(--statement-surface);
            border: 1px solid var(--statement-border);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .statement-header {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 24px;
            padding: 24px 28px;
            background:
                radial-gradient(circle at 92% 10%, rgba(99, 102, 241, 0.28), transparent 32%),
                linear-gradient(135deg, #0f172a 0%, #172033 100%);
        }

        .statement-brand {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 18px;
        }

        .statement-brand__logo {
            width: auto;
            max-width: 190px;
            height: 52px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .statement-brand__copy {
            min-width: 0;
            padding-inline-start: 18px;
            border-inline-start: 1px solid rgba(255, 255, 255, 0.18);
        }

        .statement-brand__copy strong,
        .statement-brand__copy span {
            display: block;
        }

        .statement-brand__copy strong {
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
        }

        .statement-brand__copy span {
            margin-top: 3px;
            color: rgba(255, 255, 255, 0.66);
            font-size: 12px;
        }

        .statement-heading {
            text-align: end;
        }

        .statement-heading h1,
        body.white-content .statement-heading h1 {
            margin: 0;
            color: #ffffff !important;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .statement-heading p {
            display: inline-block;
            margin: 5px 0 0;
            color: rgba(255, 255, 255, 0.68);
            direction: ltr;
            font-size: 12px;
            unicode-bidi: embed;
        }

        .statement-toolbar {
            display: grid;
            grid-template-columns: minmax(160px, 1fr) minmax(160px, 1fr) auto;
            align-items: end;
            gap: 14px;
            margin-bottom: 16px;
            padding: 18px;
            background: var(--statement-surface);
            border: 1px solid var(--statement-border);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .statement-field {
            display: grid;
            gap: 6px;
        }

        .statement-field label {
            margin: 0;
            color: var(--statement-muted) !important;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .statement-field .form-control {
            min-height: 44px;
            padding: 9px 12px;
            color: var(--statement-ink);
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            box-shadow: none;
        }

        .statement-field .form-control:focus {
            border-color: var(--statement-accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.14);
        }

        .statement-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .statement-action {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0 !important;
            padding: 9px 15px;
            border: 1px solid transparent;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
        }

        .statement-action--filter {
            color: #ffffff !important;
            background: var(--statement-accent) !important;
            border-color: var(--statement-accent) !important;
        }

        .statement-action--all {
            color: #334155 !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        .statement-action--print {
            color: #ffffff !important;
            background: #0f766e !important;
            border-color: #0f766e !important;
        }

        .statement-action--print i {
            color: #ffffff !important;
        }

        .statement-action:hover,
        .statement-action:focus {
            transform: translateY(-1px);
            filter: brightness(0.96);
        }

        .statement-content {
            padding: 24px 28px 28px;
        }

        .statement-overview {
            display: grid;
            grid-template-columns: minmax(220px, 0.72fr) minmax(0, 1.7fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .statement-recipient,
        .statement-summary {
            min-width: 0;
            padding: 18px;
            background: var(--statement-soft);
            border: 1px solid var(--statement-border);
            border-radius: 13px;
        }

        .statement-eyebrow {
            display: block;
            margin-bottom: 7px;
            color: var(--statement-accent);
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .statement-recipient h2 {
            margin: 0;
            color: var(--statement-ink);
            font-size: 20px;
            font-weight: 800;
            line-height: 1.3;
        }

        .statement-recipient p {
            margin: 8px 0 0;
            color: var(--statement-muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .statement-summary {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .statement-metric {
            min-width: 0;
            padding: 13px;
            background: #ffffff;
            border: 1px solid var(--statement-border);
            border-radius: 10px;
        }

        .statement-metric span,
        .statement-metric strong {
            display: block;
        }

        .statement-metric span {
            min-height: 32px;
            color: var(--statement-muted);
            font-size: 10px;
            font-weight: 800;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .statement-metric strong {
            margin-top: 6px;
            color: var(--statement-ink);
            font-size: 15px;
            font-weight: 900;
            line-height: 1.25;
            word-break: break-word;
        }

        .statement-metric--balance {
            color: #ffffff;
            background: var(--statement-primary);
            border-color: var(--statement-primary);
        }

        .statement-metric--balance span,
        .statement-metric--balance strong {
            color: #ffffff;
        }

        .statement-table-section {
            overflow: hidden;
            border: 1px solid var(--statement-border);
            border-radius: 13px;
        }

        .statement-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 15px 18px;
            background: #ffffff;
            border-bottom: 1px solid var(--statement-border);
        }

        .statement-table-heading h2 {
            margin: 0;
            color: var(--statement-ink);
            font-size: 16px;
            font-weight: 800;
        }

        .statement-table-heading span {
            color: var(--statement-muted);
            font-size: 11px;
        }

        .statement-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .statement-table {
            width: 100%;
            min-width: 760px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .statement-table thead {
            background: var(--statement-primary);
        }

        .statement-table th {
            padding: 11px 14px !important;
            color: #ffffff !important;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.05em;
            text-align: start;
            text-transform: uppercase;
            border: 0;
        }

        .statement-table td {
            padding: 12px 14px;
            color: #334155;
            font-size: 12px;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
        }

        .statement-table tbody tr:first-child td {
            border-top: 0;
        }

        .statement-table tbody tr:nth-child(even) {
            background: #fbfcfe;
        }

        .statement-table .statement-amount {
            text-align: end;
            white-space: nowrap;
        }

        .statement-table td.statement-amount {
            direction: ltr;
            unicode-bidi: embed;
        }

        .statement-date,
        .statement-value {
            direction: ltr;
            unicode-bidi: embed;
        }

        .statement-table .statement-balance {
            color: var(--statement-ink);
            font-weight: 900;
        }

        .statement-empty {
            padding: 30px !important;
            color: var(--statement-muted) !important;
            text-align: center;
        }

        .statement-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 18px;
            background: var(--statement-soft);
            border-top: 1px solid var(--statement-border);
        }

        .statement-footer__note {
            color: var(--statement-muted);
            font-size: 11px;
        }

        .statement-footer__balance {
            display: flex;
            align-items: baseline;
            gap: 12px;
            color: var(--statement-ink);
        }

        .statement-footer__balance span {
            font-size: 12px;
            font-weight: 800;
        }

        .statement-footer__balance strong {
            font-size: 19px;
            font-weight: 900;
        }

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
                padding-top: 8px;
            }

            .statement-header,
            .statement-overview,
            .statement-toolbar {
                grid-template-columns: 1fr;
            }

            .statement-header {
                gap: 18px;
                padding: 21px 18px;
            }

            .statement-heading {
                text-align: start;
            }

            .statement-brand__logo {
                max-width: 155px;
                height: 44px;
            }

            .statement-brand__copy {
                display: none;
            }

            .statement-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .statement-action {
                width: 100%;
                padding-inline: 9px;
            }

            .statement-content {
                padding: 18px 14px 20px;
            }

            .statement-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .statement-metric--balance {
                grid-column: 1 / -1;
            }

            .statement-footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 420px) {
            .statement-actions {
                grid-template-columns: 1fr 1fr;
            }

            .statement-action--print {
                grid-column: 1 / -1;
            }
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body,
            body.white-content,
            .main-panel,
            .main-panel > .content {
                background: #ffffff !important;
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

            body.white-content .wrapper {
                display: block !important;
                min-height: 0 !important;
                width: 100% !important;
            }

            body.white-content .wrapper > .main-panel,
            .main-panel {
                float: none !important;
                margin: 0 !important;
                max-width: none !important;
                min-height: 0 !important;
                padding: 0 !important;
                transform: none !important;
                width: 100% !important;
            }

            body.white-content .wrapper > .main-panel > .content,
            .main-panel > .content {
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .statement-page {
                max-width: none;
                padding: 0;
            }

            .statement-document {
                overflow: visible;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .statement-header {
                padding: 14px 16px;
            }

            .statement-content {
                padding: 12px 0 0;
            }

            .statement-overview {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-bottom: 10px;
            }

            .statement-recipient,
            .statement-summary {
                padding: 12px;
            }

            .statement-summary {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }

            .statement-metric,
            .statement-metric--balance {
                grid-column: auto;
                padding: 9px;
            }

            .statement-table-wrap {
                overflow: visible;
            }

            .statement-table {
                min-width: 0;
            }

            .statement-table th,
            .statement-table td {
                padding: 6px 7px !important;
                font-size: 8.5px;
            }

            .statement-table tr {
                break-inside: avoid;
            }

            .statement-table thead {
                display: table-header-group;
            }

            .statement-table-section {
                overflow: visible;
            }

            .statement-footer {
                break-inside: avoid;
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
            <header class="statement-header">
                <div class="statement-brand">
                    <img class="statement-brand__logo" src="{{ $statementLogo }}" alt="{{ $statementBrand }} {{ $statementText('logo') }}">
                    <div class="statement-brand__copy">
                        <strong>{{ $statementBrand }}</strong>
                        <span>{{ app()->isLocale('ar') ? $statementText('Precision dental labs, refined.') : config('branding.defaults.copy.tagline', $statementText('Precision dental labs, refined.')) }}</span>
                    </div>
                </div>

                <div class="statement-heading">
                    <h1>{{ $statementText('Account Statement') }}</h1>
                    <p>{{ substr($from, 0, 10) }} — {{ substr($to, 0, 10) }}</p>
                </div>
            </header>

            <div class="statement-content">
                <section class="statement-overview" aria-label="{{ $statementText('Account overview') }}">
                    <div class="statement-recipient">
                        <span class="statement-eyebrow">{{ $statementText('Prepared for') }}</span>
                        <h2>{{ $statementText('Doctor prefix') }} {{ $client->name }}</h2>
                        <p>{{ $statementText('A complete record of invoices, payments, discounts, and the resulting account balance.') }}</p>
                    </div>

                    <div class="statement-summary">
                        <div class="statement-metric">
                            <span>{{ $statementText('Opening balance') }}</span>
                            <strong class="statement-value">{{ number_format((float) $openingBalance, 2) }} {{ $currencyLabel }}</strong>
                        </div>
                        <div class="statement-metric">
                            <span>{{ $statementText('Invoices') }}</span>
                            <strong class="statement-value">{{ number_format((float) $invoicesAmount, 2) }} {{ $currencyLabel }}</strong>
                        </div>
                        <div class="statement-metric">
                            <span>{{ $statementText('Payments') }}</span>
                            <strong class="statement-value">{{ number_format((float) $amountPaid, 2) }} {{ $currencyLabel }}</strong>
                        </div>
                        <div class="statement-metric">
                            <span>{{ $statementText('Discounts') }}</span>
                            <strong class="statement-value">{{ number_format((float) $discounts, 2) }} {{ $currencyLabel }}</strong>
                        </div>
                        <div class="statement-metric statement-metric--balance">
                            <span>{{ $statementText('Balance due') }}</span>
                            <strong class="statement-value">{{ number_format((float) $balanceDue, 2) }} {{ $currencyLabel }}</strong>
                        </div>
                    </div>
                </section>

                <section class="statement-table-section" aria-labelledby="statement-transactions-title">
                    <div class="statement-table-heading">
                        <h2 id="statement-transactions-title">{{ $statementText('Transaction history') }}</h2>
                        <span>{{ str_replace(':count', $transactions->count(), $statementText('Transactions count')) }}</span>
                    </div>

                    <div class="statement-table-wrap">
                        <table class="statement-table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ $statementText('Date') }}</th>
                                    <th scope="col">{{ $statementText('Transaction') }}</th>
                                    <th scope="col">{{ $statementText('Description') }}</th>
                                    <th class="statement-amount" scope="col">{{ $statementText('Payment') }}</th>
                                    <th class="statement-amount" scope="col">{{ $statementText('Amount') }}</th>
                                    <th class="statement-amount" scope="col">{{ $statementText('Balance') }}</th>
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

                                        $transactionType = isset($transaction->case_id)
                                            ? (isset($transaction->case) ? $statementText('Invoice') : $statementText('Discount'))
                                            : $statementText('Payment');
                                        $description = isset($transaction->case_id)
                                            ? (isset($transaction->case)
                                                ? ($transaction->rejection_invoice == 1
                                                    ? $transaction->case->patient_name . ' / مرتجع'
                                                    : str_replace('/ تعديل', '', $transaction->case->patient_name))
                                                : $transaction->discount_title)
                                            : $transaction->notes;
                                    @endphp
                                    <tr>
                                        <td class="statement-date">{{ substr($transaction->created_at, 0, 10) }}</td>
                                        <td>{{ $transactionType }}</td>
                                        <td>{{ $description }}</td>
                                        <td class="statement-amount">
                                            {{ isset($transaction->case_id) ? '—' : number_format((float) $transaction->amount, 2) }}
                                        </td>
                                        <td class="statement-amount">
                                            {{ isset($transaction->case_id) ? number_format((float) $transaction->amount, 2) : '—' }}
                                            {{ isset($transaction->discount) ? '*' : '' }}
                                        </td>
                                        <td class="statement-amount statement-balance">
                                            {{ number_format((float) $runningBalance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="statement-empty" colspan="6">{{ $statementText('No transactions were recorded for this period.') }}</td>
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
                            <span>{{ $statementText('Closing balance') }}</span>
                            <strong class="statement-value">{{ number_format((float) $runningBalance, 2) }} {{ $currencyLabel }}</strong>
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
