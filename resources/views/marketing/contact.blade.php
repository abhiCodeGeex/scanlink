<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; }
        h1 { color: #008C00; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input, textarea { width: 100%; padding: .5rem; margin-top: .25rem; box-sizing: border-box; }
        button { margin-top: 1rem; background: #008C00; color: #fff; border: 0; padding: .6rem 1rem; border-radius: 6px; }
        .ok { background: #e8f5e9; padding: .75rem; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>Contact ScanLink</h1>
    <p>Email: <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>

    @if (session('contact_submitted'))
        <p class="ok">Thanks — we received your message.</p>
    @endif

    <form method="post" action="{{ route('marketing.contact.submit') }}">
        @csrf
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Message</label>
        <textarea name="message" rows="5" required></textarea>
        <button type="submit">Send</button>
    </form>
    <p><a href="{{ route('marketing.home') }}">Back</a></p>
</body>
</html>
