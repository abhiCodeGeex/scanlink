<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — ScanLink</title>
    <style>
        :root {
            --sl-green: #008c00;
            --sl-green-dark: #007000;
            --sl-ink: #1f2933;
            --sl-muted: #5f6b7a;
            --sl-line: #d8dee6;
            --sl-soft: #f3f5f7;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            color: var(--sl-ink);
            background:
                radial-gradient(circle at top right, rgba(0, 140, 0, 0.08), transparent 42%),
                linear-gradient(180deg, #fafbfc 0%, #f0f3f6 100%);
        }

        .wrap {
            max-width: 420px;
            margin: 0 auto;
            padding: 2.25rem 1.1rem 1.5rem;
        }

        .login-shell {
            position: relative;
            padding-top: 1.15rem;
        }

        .login-tab {
            position: absolute;
            top: 0;
            left: 1rem;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            height: 1.7rem;
            padding: 0 .7rem;
            border: 1px solid var(--sl-line);
            border-bottom: 0;
            border-radius: 8px 8px 0 0;
            background: #fff;
            color: var(--sl-muted);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: lowercase;
        }

        .card {
            background: #fff;
            border: 1px solid var(--sl-line);
            border-radius: 14px;
            padding: 1.55rem 1.25rem 1.35rem;
            box-shadow: 0 10px 28px rgba(31, 41, 51, 0.08);
        }

        .card-title {
            margin: 0 0 .35rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--sl-green);
        }

        .card-copy {
            margin: 0 0 1.1rem;
            color: var(--sl-muted);
            font-size: .92rem;
            line-height: 1.4;
        }

        label {
            display: block;
            margin: 0 0 .4rem;
            font-size: .9rem;
            font-weight: 600;
            color: var(--sl-ink);
        }

        input[type="password"] {
            width: 100%;
            height: 2.6rem;
            padding: 0 .85rem;
            border: 1px solid var(--sl-line);
            border-radius: 8px;
            background: #fff;
            color: var(--sl-ink);
            font-size: 1rem;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        input[type="password"]:focus {
            border-color: var(--sl-green);
            box-shadow: 0 0 0 3px rgba(0, 140, 0, 0.15);
        }

        .error {
            margin: .55rem 0 0;
            color: #b42318;
            font-size: .85rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 7.5rem;
            margin-top: 1rem;
            padding: .7rem 1.25rem;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(180deg, #19a319 0%, var(--sl-green) 100%);
            color: #fff;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(0, 140, 0, 0.22);
        }

        .btn:hover {
            background: linear-gradient(180deg, var(--sl-green) 0%, var(--sl-green-dark) 100%);
        }

        .mobile-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: .9rem;
            padding: 0 .15rem;
        }

        .mobile-footer img {
            max-height: 26px;
            width: auto;
            opacity: .88;
        }

        @if (! empty($portalPreview))
        body {
            min-height: 0;
            background: #fff;
        }

        .wrap {
            max-width: 100%;
            padding: .7rem .55rem .4rem;
        }

        .login-shell { padding-top: 1rem; }

        .login-tab {
            left: .75rem;
            height: 1.45rem;
            font-size: .68rem;
        }

        .card {
            border-radius: 10px;
            padding: 1.15rem .9rem 1rem;
            box-shadow: 0 4px 14px rgba(31, 41, 51, 0.07);
        }

        .card-title { font-size: 1.05rem; }
        .card-copy { font-size: .84rem; margin-bottom: .85rem; }
        input[type="password"] { height: 2.35rem; font-size: .95rem; }
        .btn {
            min-width: 6.5rem;
            padding: .6rem 1rem;
            font-size: .88rem;
            box-shadow: none;
        }
        .mobile-footer img { max-height: 22px; }
        @endif
    </style>
</head>
<body @class(['portal-preview' => ! empty($portalPreview)])>
@php
    $heading = trim((string) ($profile->name ?: $profile->code_profile_name ?: $profile->form_title));
@endphp
<div class="wrap">
    <div class="login-shell">
        <div class="login-tab">login</div>
        <div class="card">
            @if ($heading !== '')
                <h1 class="card-title">{{ $heading }}</h1>
            @endif
            <p class="card-copy">This profile is password protected. Enter the password to continue.</p>

            <form method="post" action="{{ route('scan.unlock', [$clientUrl, $profile->id]) }}">
                @csrf
                @if (! empty($portalPreview))
                    <input type="hidden" name="ask_for_location" value="no">
                    <input type="hidden" name="portal_preview" value="1">
                @endif

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autofocus autocomplete="current-password">

                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button class="btn" type="submit">Login</button>
            </form>
        </div>
    </div>

    <footer class="mobile-footer">
        <img src="{{ asset('images/PoweredbyScanLink.png') }}" alt="Powered by SCANLINK">
    </footer>
</div>
</body>
</html>
