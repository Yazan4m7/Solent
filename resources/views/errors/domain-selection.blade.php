<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    @include('components.i18n-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Domain Not Configured</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --panel: #111827;
            --text: #f8fafc;
            --muted: #94a3b8;
            --line: #334155;
            --accent: #38bdf8;
            --card: #1e293b;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: Cairo, Arial, sans-serif;
            background: radial-gradient(1200px 600px at 10% 10%, rgba(56, 189, 248, 0.12), transparent), var(--bg);
            color: var(--text);
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .panel {
            width: min(860px, 100%);
            background: linear-gradient(160deg, rgba(30, 41, 59, 0.9), rgba(17, 24, 39, 0.96));
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.35);
        }

        .eyebrow {
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 0 0 10px 0;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
        }

        .message {
            margin: 12px 0 0 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.6;
        }

        .host {
            margin: 10px 0 0 0;
            font-size: 13px;
            color: #cbd5e1;
        }

        .host strong {
            color: #ffffff;
        }

        .options {
            margin-top: 22px;
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .option {
            text-decoration: none;
            color: inherit;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.15s ease, border-color 0.15s ease, background 0.15s ease;
        }

        .option:hover {
            transform: translateY(-2px);
            border-color: #64748b;
            background: #273449;
        }

        .option-country {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
        }

        .option-domain {
            margin: 4px 0 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .flag {
            position: relative;
            width: 46px;
            height: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            flex: 0 0 auto;
            background: linear-gradient(
                to bottom,
                #111111 0%,
                #111111 33.33%,
                #ffffff 33.33%,
                #ffffff 66.66%,
                #007a3d 66.66%,
                #007a3d 100%
            );
        }

        .flag::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 0;
            border-top: 15px solid transparent;
            border-bottom: 15px solid transparent;
            border-right: 18px solid #ce1126;
        }

        .flag.flag-jo::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 12px;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #ffffff;
        }

        .footer-note {
            margin: 18px 0 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .empty-state {
            margin-top: 22px;
            padding: 16px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
@php
    $reasonMessages = [
        'missing_host' => 'Country domain was not detected in this request.',
        'unmapped_host' => 'This host is not mapped to an active tenant.',
        'invalid_context' => 'Domain branch configuration is incomplete.',
        'database_unavailable' => 'The branch database could not be reached for this domain.',
        'demo_database_mismatch' => 'The isolated demo database is not configured correctly.',
    ];
    $reasonMessage = $reasonMessages[$reason] ?? 'Unable to determine the branch context for this request.';
@endphp
<main class="page">
    <section class="panel" aria-label="Tenant domain status">
        <p class="eyebrow">Tenant Required</p>
        <h1>Tenant Domain Not Configured</h1>
        <p class="message">{{ $reasonMessage }}</p>
        <p class="host">Requested host: <strong>{{ $requestedHost !== '' ? $requestedHost : 'unknown' }}</strong></p>

        @if (count($domainOptions) > 0)
            <div class="options">
                @foreach ($domainOptions as $option)
                    @php
                        $code = strtolower((string) ($option['country_code'] ?? ''));
                        $flagClass = $code === 'jo' ? 'flag-jo' : ($code === 'ps' ? 'flag-ps' : '');
                    @endphp
                    <a class="option" href="{{ $option['selection_url'] ?? $option['url'] }}">
                        <span class="flag {{ $flagClass }}" aria-hidden="true"></span>
                        <span>
                            <p class="option-country">{{ $option['country_name'] }} ({{ strtoupper($option['country_code']) }})</p>
                            <p class="option-domain">{{ $option['host'] }}</p>
                        </span>
                    </a>
                @endforeach
            </div>

            <p class="footer-note">Use one of the configured tenant domains above to continue. Your choice is remembered for future visits.</p>
        @else
            <p class="empty-state">Create or activate a tenant for this host from platform administration, then clear the application cache.</p>
        @endif
    </section>
</main>
</body>
</html>
