<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('ui.dom')[$pageSlug ?? ''] ?? ($pageSlug ?? 'Print') }}</title>
    @brandStyles
    @stack('css')
    <style>
        html,
        body {
            margin: 0;
            min-height: 100%;
            background: #ffffff;
            font-family: "Cairo", "Segoe UI", Arial, sans-serif;
        }

        .statement-toolbar {
            display: none !important;
        }
    </style>
</head>
<body class="print-document">
    @yield('content')
    @stack('js')
</body>
</html>
