<!doctype html>
<html lang="en"><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sample data unavailable</title></head>
<body>
    <main style="max-width:36rem;margin:10vh auto;padding:24px;font-family:system-ui">
        <h1>Sample data is unavailable</h1>
        <p>Your real data is safe. You can switch back and continue working.</p>
        <form method="POST" action="{{ route('tenant-demo.update') }}">
            @csrf
            <input type="hidden" name="enabled" value="0">
            <button type="submit" style="padding:12px 20px">Return to real data</button>
        </form>
    </main>
</body></html>
