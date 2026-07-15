<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How to — ScanLink tutorials</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; line-height: 1.6; }
        h1 { color: #008C00; }
        ul { padding-left: 1.25rem; }
        li { margin-bottom: 0.75rem; }
        a { color: #008C00; }
    </style>
</head>
<body>
    <h1>How to use ScanLink</h1>
    <p>Video tutorials from the legacy ScanLink help menu. Each title opens the YouTube tutorial in a new tab.</p>
    <ul>
        @foreach ($tutorials as $tutorial)
            <li>
                <a href="{{ $tutorial['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $tutorial['title'] }}">
                    {{ $tutorial['title'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <p><a href="{{ route('marketing.home') }}">Back to home</a></p>
</body>
</html>
