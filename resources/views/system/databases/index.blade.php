@extends('layouts.app', ['pageSlug' => 'Databases', 'platformAdminPage' => true])

@section('content')
    @php
        $formatBytes = static function (?int $bytes): string {
            if ($bytes === null) {
                return 'Unavailable';
            }

            if ($bytes < 1024) {
                return $bytes . ' B';
            }

            $units = ['KB', 'MB', 'GB', 'TB'];
            $value = $bytes;
            foreach ($units as $unit) {
                $value /= 1024;
                if ($value < 1024 || $unit === 'TB') {
                    return number_format($value, $value >= 10 ? 1 : 2) . ' ' . $unit;
                }
            }

            return number_format($value, 2) . ' TB';
        };
    @endphp

    <style>
        .platform-databases {
            --db-accent: #6366f1;
            --db-accent-soft: #eef2ff;
            --db-border: #e2e8f0;
            --db-muted: #64748b;
            --db-text: #0f172a;
            color: var(--db-text);
            margin: 0 auto;
            max-width: 1500px;
            padding: 16px 12px 32px;
        }

        .platform-databases__hero {
            align-items: flex-start;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 20px;
        }

        .platform-databases__eyebrow {
            color: var(--db-accent);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            margin-bottom: 7px;
            text-transform: uppercase;
        }

        .platform-databases h1 {
            color: var(--db-text);
            font-size: clamp(25px, 3vw, 36px);
            font-weight: 800;
            letter-spacing: -.035em;
            line-height: 1.1;
            margin: 0;
        }

        .platform-databases__intro {
            color: var(--db-muted);
            font-size: 13px;
            line-height: 1.65;
            margin: 8px 0 0;
            max-width: 660px;
        }

        .platform-databases__phpmyadmin {
            align-items: center;
            background: var(--db-text);
            border: 1px solid var(--db-text);
            border-radius: 13px;
            color: #fff !important;
            display: inline-flex;
            flex: 0 0 auto;
            font-size: 12px;
            font-weight: 800;
            gap: 9px;
            min-height: 44px;
            padding: 10px 15px;
            text-decoration: none !important;
            transition: transform .16s ease, box-shadow .16s ease;
        }

        .platform-databases__phpmyadmin:hover,
        .platform-databases__phpmyadmin:focus {
            box-shadow: 0 12px 24px rgba(15, 23, 42, .16);
            transform: translateY(-1px);
        }

        .platform-databases__phpmyadmin svg {
            fill: none;
            height: 17px;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
            width: 17px;
        }

        .database-summary {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 18px;
        }

        .database-summary__item {
            background: #fff;
            border: 1px solid var(--db-border);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .035);
            min-width: 0;
            padding: 16px 18px;
        }

        .database-summary__label {
            color: var(--db-muted);
            display: block;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .database-summary__value {
            color: var(--db-text);
            display: block;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -.025em;
            line-height: 1;
        }

        .platform-databases__notice {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 13px;
            color: #9a3412;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 16px;
            padding: 11px 14px;
        }

        .database-panel {
            background: #fff;
            border: 1px solid var(--db-border);
            border-radius: 18px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        .database-panel__heading {
            align-items: center;
            border-bottom: 1px solid var(--db-border);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 17px 20px;
        }

        .database-panel__heading h2 {
            color: var(--db-text);
            font-size: 15px;
            font-weight: 800;
            margin: 0;
        }

        .database-panel__heading p {
            color: var(--db-muted);
            font-size: 11px;
            line-height: 1.5;
            margin: 4px 0 0;
        }

        .database-panel__count {
            background: var(--db-accent-soft);
            border-radius: 999px;
            color: #4338ca;
            flex: 0 0 auto;
            font-size: 10px;
            font-weight: 800;
            padding: 7px 10px;
        }

        .database-table-wrap {
            overflow-x: auto;
        }

        .database-table {
            border-collapse: collapse;
            margin: 0;
            min-width: 780px;
            width: 100%;
        }

        .database-table th {
            background: #f8fafc;
            border-bottom: 1px solid var(--db-border);
            color: var(--db-muted);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .065em;
            padding: 11px 18px;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .database-table td {
            border-bottom: 1px solid #edf2f7;
            color: #334155;
            font-size: 12px;
            padding: 15px 18px;
            text-align: left;
            vertical-align: middle;
        }

        .database-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .database-table tbody tr:hover {
            background: #fbfdff;
        }

        .database-owner {
            color: var(--db-text);
            display: block;
            font-size: 12px;
            font-weight: 800;
        }

        .database-owner-detail,
        .database-domain {
            color: var(--db-muted);
            display: block;
            font-size: 10px;
            margin-top: 3px;
        }

        .database-name {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #334155;
            display: inline-block;
            font-family: var(--font-family-monospace);
            font-size: 11px;
            font-weight: 700;
            max-width: 260px;
            overflow: hidden;
            padding: 5px 8px;
            text-overflow: ellipsis;
            vertical-align: middle;
            white-space: nowrap;
        }

        .database-value {
            color: var(--db-text);
            font-weight: 800;
            white-space: nowrap;
        }

        .database-status {
            align-items: center;
            background: #ecfdf5;
            border-radius: 999px;
            color: #047857;
            display: inline-flex;
            font-size: 10px;
            font-weight: 800;
            gap: 6px;
            padding: 6px 9px;
            white-space: nowrap;
        }

        .database-status::before {
            background: currentColor;
            border-radius: 50%;
            content: '';
            height: 6px;
            width: 6px;
        }

        .database-status--missing {
            background: #fff7ed;
            color: #c2410c;
        }

        .database-updated {
            color: var(--db-text);
            display: block;
            font-weight: 700;
            white-space: nowrap;
        }

        .database-updated-note {
            color: var(--db-muted);
            display: block;
            font-size: 9px;
            margin-top: 3px;
            white-space: nowrap;
        }

        .database-empty {
            color: var(--db-muted);
            padding: 34px 20px !important;
            text-align: center !important;
        }

        .database-panel__footnote {
            background: #f8fafc;
            border-top: 1px solid var(--db-border);
            color: var(--db-muted);
            font-size: 10px;
            line-height: 1.6;
            margin: 0;
            padding: 12px 20px;
        }

        @media (max-width: 767.98px) {
            .platform-databases {
                padding: 10px 4px 28px;
            }

            .platform-databases__hero {
                display: block;
            }

            .platform-databases__phpmyadmin {
                justify-content: center;
                margin-top: 15px;
                width: 100%;
            }

            .database-summary {
                grid-template-columns: 1fr 1fr;
            }

            .database-summary__item:first-child {
                grid-column: 1 / -1;
            }

            .database-summary__item {
                padding: 14px;
            }

            .database-summary__value {
                font-size: 20px;
            }

            .database-panel__heading {
                align-items: flex-start;
                padding: 15px;
            }

            .database-table-wrap {
                overflow: visible;
            }

            .database-table,
            .database-table tbody,
            .database-table tr,
            .database-table td {
                display: block;
                min-width: 0;
                width: 100%;
            }

            .database-table thead {
                display: none;
            }

            .database-table tbody {
                padding: 5px 12px 12px;
            }

            .database-table tbody tr {
                border: 1px solid var(--db-border);
                border-radius: 14px;
                margin-top: 9px;
                overflow: hidden;
            }

            .database-table td {
                align-items: flex-start;
                border-bottom: 1px solid #edf2f7;
                display: grid;
                gap: 12px;
                grid-template-columns: minmax(92px, .72fr) minmax(0, 1.28fr);
                padding: 11px 12px;
                text-align: right;
            }

            .database-table td::before {
                color: var(--db-muted);
                content: attr(data-label);
                font-size: 9px;
                font-weight: 800;
                letter-spacing: .055em;
                padding-top: 3px;
                text-align: left;
                text-transform: uppercase;
            }

            .database-table td:last-child {
                border-bottom: 0;
            }

            .database-table td > * {
                justify-self: end;
                max-width: 100%;
            }

            .database-name {
                max-width: 100%;
            }

            .database-owner-detail,
            .database-domain,
            .database-updated-note {
                white-space: normal;
            }

            .database-panel__footnote {
                padding: 12px 15px;
            }
        }

        @media (max-width: 380px) {
            .database-summary {
                grid-template-columns: 1fr;
            }

            .database-summary__item:first-child {
                grid-column: auto;
            }
        }
    </style>

    <main class="platform-databases">
        <header class="platform-databases__hero">
            <div>
                <div class="platform-databases__eyebrow">Platform administration</div>
                <h1>Databases</h1>
                <p class="platform-databases__intro">
                    Storage overview for the platform registry and registered tenant databases. Server and credential details stay hidden.
                </p>
            </div>
            <a class="platform-databases__phpmyadmin" href="{{ $phpMyAdminUrl }}" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5h5v5M13 11l6-6M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>
                Open phpMyAdmin
            </a>
        </header>

        <section class="database-summary" aria-label="Database totals">
            <article class="database-summary__item">
                <span class="database-summary__label">Managed databases</span>
                <span class="database-summary__value">{{ number_format($summary['database_count']) }}</span>
            </article>
            <article class="database-summary__item">
                <span class="database-summary__label">Combined size</span>
                <span class="database-summary__value">{{ $formatBytes($summary['total_size_bytes']) }}</span>
            </article>
            <article class="database-summary__item">
                <span class="database-summary__label">Tables</span>
                <span class="database-summary__value">{{ $summary['table_count'] === null ? '—' : number_format($summary['table_count']) }}</span>
            </article>
        </section>

        @if($metadataError)
            <div class="platform-databases__notice" role="status">{{ $metadataError }}</div>
        @endif

        <section class="database-panel">
            <div class="database-panel__heading">
                <div>
                    <h2>Managed database inventory</h2>
                    <p>Only databases registered to this application are included.</p>
                </div>
                <span class="database-panel__count">{{ $summary['measured_count'] }} available</span>
            </div>

            <div class="database-table-wrap">
                <table class="database-table">
                    <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Database</th>
                            <th>Status</th>
                            <th>Size</th>
                            <th>Tables</th>
                            <th>Last table update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($databaseRows as $database)
                            <tr>
                                <td data-label="Owner">
                                    <span>
                                        <span class="database-owner">{{ $database['owner_name'] }}</span>
                                        <span class="database-owner-detail">{{ $database['owner_detail'] }}</span>
                                        @if($database['domain'])
                                            <span class="database-domain">{{ $database['domain'] }}</span>
                                        @endif
                                    </span>
                                </td>
                                <td data-label="Database"><code class="database-name" title="{{ $database['database_name'] }}">{{ $database['database_name'] }}</code></td>
                                <td data-label="Status">
                                    <span class="database-status{{ $database['exists'] ? '' : ' database-status--missing' }}">
                                        {{ $database['exists'] ? 'Available' : 'Unavailable' }}
                                    </span>
                                </td>
                                <td data-label="Size"><span class="database-value">{{ $formatBytes($database['size_bytes']) }}</span></td>
                                <td data-label="Tables"><span class="database-value">{{ $database['table_count'] === null ? '—' : number_format($database['table_count']) }}</span></td>
                                <td data-label="Last table update">
                                    <span>
                                        <span class="database-updated">{{ $database['last_updated_at'] ? $database['last_updated_at']->format('Y-m-d H:i') : 'Not reported' }}</span>
                                        <span class="database-updated-note">MySQL metadata</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="database-empty" colspan="6">No managed databases are registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="database-panel__footnote">
                Database sizes include table data and indexes. “Last table update” depends on what the MySQL storage engine reports and can be unavailable even when a database is active.
            </p>
        </section>
    </main>
@endsection
