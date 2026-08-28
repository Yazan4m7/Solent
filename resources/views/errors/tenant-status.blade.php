<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tenantContext->status === 'disabled' ? 'Workspace disabled' : 'Workspace unavailable' }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            display: grid;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            place-items: center;
            color: #0f172a;
            background: #ffffff;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        main { max-width: 560px; }
        h1 {
            margin: 0 0 14px;
            font-size: clamp(28px, 6vw, 44px);
            line-height: 1.1;
        }
        p {
            margin: 0;
            color: #64748b;
            font-size: 17px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<main>
    @if($tenantContext->status === 'disabled')
        <h1>This workspace is disabled</h1>
        <p>Please contact the Korvion team to restore access.</p>
    @else
        <h1>This workspace is unavailable</h1>
        <p>Please contact the Korvion team for assistance.</p>
    @endif
</main>
</body>
</html>
