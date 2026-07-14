<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password required — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f5f5f5; color: #222; }
        .wrap { max-width: 420px; margin: 4rem auto; padding: 1.5rem; }
        .card { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        h1 { color: #008C00; margin-top: 0; font-size: 1.25rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input { width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn { margin-top: 1rem; background: #008C00; color: #fff; padding: .6rem 1rem; border-radius: 8px; border: 0; cursor: pointer; width: 100%; }
        .error { color: #b71c1c; margin-top: .5rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>{{ $profile->name }}</h1>
        <p>This profile is password protected.</p>
        <form method="post" action="{{ route('scan.unlock', [$clientUrl, $profile->id]) }}">
            @csrf
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autofocus>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
            <button class="btn" type="submit">Unlock</button>
        </form>
    </div>
</div>
</body>
</html>
