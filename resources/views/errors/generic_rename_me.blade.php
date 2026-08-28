<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    @include('components.i18n-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Cairo, Arial, sans-serif;
            background: #f5f7fb;
            color: #212529;
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 32px 28px;
            text-align: center;
        }
        .status {
            font-size: 42px;
            font-weight: 700;
            color: #cc0000;
            margin: 0 0 8px;
        }
        .title {
            margin: 0 0 12px;
            font-size: 22px;
            font-weight: 700;
        }
        .msg {
            margin: 0 0 24px;
            line-height: 1.5;
            color: #495057;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .dev-hint {
            margin: -8px 0 22px;
            padding: 10px 12px;
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            background: #f8fafc;
            color: #52606d;
            font-size: 13px;
            line-height: 1.45;
            text-align: left;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #6c757d;
            color: #fff;
            border-radius: 6px;
            padding: 10px 18px;
        }
        .btn.primary {
            background: #0d6efd;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <p class="status">{{ $statusCode ?? 500 }}</p>
        <h1 class="title">Something went wrong</h1>
        <p class="msg">The page cannot be displayed right now. Please try again, or return to the home page.</p>
        @if(!empty($developerMessage))
            <p class="dev-hint"><strong>What went wrong:</strong> {{ $developerMessage }}</p>
        @endif
        <div class="actions">
            <a class="btn primary" href="{{ url('/') }}">Home</a>
            <a class="btn" href="javascript:history.back()">Go Back</a>
        </div>
    </div>
</div>
</body>
</html>
