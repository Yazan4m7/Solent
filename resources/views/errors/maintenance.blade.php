<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    @include('components.i18n-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance | {{ config('branding.defaults.name', 'Solent') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --border: rgba(0, 0, 0, 0.08);
            --text: #0f172a;
            --muted: #64748b;
            --accent: #6366f1;
            --gold: #dbc373;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Cairo, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .wrapper {
            align-items: center;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
            max-width: 620px;
            padding: 34px 24px;
            position: relative;
            text-align: center;
            width: 100%;
        }

        .card::before {
            background: var(--accent);
            border-radius: 14px 0 0 14px;
            bottom: 0;
            content: "";
            left: 0;
            position: absolute;
            top: 0;
            width: 4px;
        }

        .title {
            font-size: clamp(24px, 7vw, 34px);
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 12px;
        }

        .msg {
            color: var(--muted);
            font-size: clamp(16px, 4vw, 20px);
            line-height: 1.55;
            margin: 0 auto 24px;
            max-width: 480px;
        }

        @media (max-width: 430px) {
            .wrapper {
                align-items: flex-start;
                padding: 16px;
            }

            .card {
                margin-top: 12vh;
                padding: 30px 18px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <main class="card" role="main" aria-labelledby="maintenance-title">
        <h1 class="title" id="maintenance-title">{{ config('branding.defaults.name', 'Solent') }} is under maintenance</h1>
        <p class="msg">
            We are updating the system right now. Please check back shortly.
            @if(!empty($retryAfter))
                Try again in about {{ $retryAfter }} seconds.
            @endif
        </p>
    </main>
</div>
</body>
</html>
