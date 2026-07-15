<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ScanLink — QR code management</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f8faf8; color: #222; }
        header { background: #008C00; color: #fff; padding: 1.5rem 2rem; }
        main { max-width: 960px; margin: 0 auto; padding: 2rem; }
        a { color: #008C00; }
        .links a { margin-right: 1rem; }
    </style>
</head>
<body>
<header>
    <h1>ScanLink</h1>
    <p>QR code profiles, analytics, and form builder for industry compliance.</p>
</header>
<main>
    <p class="links">
        <a href="{{ route('marketing.pricing') }}">Pricing</a>
        <a href="{{ route('marketing.faq') }}">FAQ</a>
        <a href="{{ route('marketing.how-to') }}">How to</a>
        <a href="{{ route('marketing.contact') }}">Contact</a>
        <a href="/portal/login">Client login</a>
    </p>
</main>
</body>
</html>
