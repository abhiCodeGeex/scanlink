<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code expired — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f5f5f5; color: #222; }
        .wrap { max-width: 480px; margin: 4rem auto; padding: 1.5rem; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        h1 { color: #b71c1c; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Code expired</h1>
        <p>{{ $profile->name }} is no longer active.</p>
        @if ($profile->expired_at)
            <p>Expired on {{ $profile->expired_at->format('d M Y') }}.</p>
        @endif
    </div>
</div>
</body>
</html>
