<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->application ?: 'URL Link' }} — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f5f5f5; color: #222; }
        .wrap { max-width: 640px; margin: 0 auto; padding: 1rem; }
        .card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); text-align: center; }
        h1 { color: #008C00; margin-top: 0; font-size: 1.25rem; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .65rem 1.1rem; border-radius: 8px; text-decoration: none; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <p style="font-size:.95rem;font-weight:700;color:#008C00;margin:0 0 .15rem;">URL Link</p>
        <h1>{{ $profile->application ?: $profile->code_profile_name ?: 'Destination link' }}</h1>

        @if ($profile->description)
            <p>{{ $profile->description }}</p>
        @endif

        @if ($profile->url)
            <a class="btn" href="{{ $profile->url }}" target="_blank" rel="noopener">Open destination</a>
            <p style="font-size:.85rem;color:#666;word-break:break-all;margin-top:1rem;">{{ $profile->url }}</p>
        @else
            <p style="color:#666;">Enter a destination URL in the editor to preview the link behaviour.</p>
        @endif
    </div>
</div>
</body>
</html>
