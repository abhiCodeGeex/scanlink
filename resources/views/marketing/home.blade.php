<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ScanLink</title>
    <link rel="icon" href="{{ asset('images/scanlink-logo.png') }}">
    <style>
        :root {
            --sl-green: #008C00;
            --sl-green-dark: #006600;
            --sl-ink: #2b2b2b;
            --sl-muted: #5a5a5a;
            --sl-bg: #f3f5f2;
            --sl-card: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--sl-ink);
            background:
                radial-gradient(circle at top left, rgba(0, 140, 0, 0.12), transparent 40%),
                linear-gradient(180deg, #eef3ea 0%, var(--sl-bg) 45%, #e7ece4 100%);
            min-height: 100vh;
        }
        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 3rem;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }
        header img {
            height: 52px;
            width: auto;
        }
        .brand-mark {
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sl-muted);
        }
        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.9fr;
            gap: 1.75rem;
            align-items: start;
        }
        @media (max-width: 800px) {
            .hero { grid-template-columns: 1fr; }
        }
        .intro h1 {
            margin: 0 0 0.75rem;
            font-size: clamp(2rem, 4vw, 2.75rem);
            line-height: 1.15;
            color: var(--sl-green-dark);
        }
        .intro p {
            margin: 0;
            font-size: 1.05rem;
            line-height: 1.55;
            color: var(--sl-muted);
            max-width: 34rem;
        }
        .login-card {
            background: var(--sl-card);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 60, 0, 0.08);
        }
        .login-card h2 {
            margin: 0 0 1rem;
            font-size: 1.25rem;
            color: var(--sl-green);
        }
        label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
            color: var(--sl-ink);
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.8rem;
            border: 1px solid #c9d0c6;
            border-radius: 8px;
            margin-bottom: 0.9rem;
            font: inherit;
        }
        input:focus {
            outline: 2px solid rgba(0, 140, 0, 0.35);
            border-color: var(--sl-green);
        }
        button {
            width: 100%;
            border: 0;
            border-radius: 8px;
            background: var(--sl-green);
            color: #fff;
            font: inherit;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.8rem 1rem;
            cursor: pointer;
        }
        button:hover { background: var(--sl-green-dark); }
        .error {
            background: #fff4f4;
            color: #a40000;
            border: 1px solid #f0c2c2;
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
            margin-bottom: 0.9rem;
            font-size: 0.92rem;
        }
        footer {
            margin-top: 2.5rem;
            color: var(--sl-muted);
            font-size: 0.85rem;
        }
        .admin-link {
            color: var(--sl-muted);
            text-decoration: none;
            font-size: 0.85rem;
        }
        .admin-link:hover { color: var(--sl-green); }
        .register-link {
            display: inline-block;
            margin-top: 0.9rem;
            color: var(--sl-green);
            font-size: 0.95rem;
            text-decoration: none;
            font-weight: 700;
        }
        .register-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <div>
                <img src="{{ asset('images/scanlink-logo.png') }}" alt="ScanLink">
                <div class="brand-mark">Client portal</div>
            </div>
            <a class="admin-link" href="{{ url('/admin') }}">Admin</a>
        </header>

        <section class="hero">
            <div class="intro">
                <h1>ScanLink</h1>
                <p>
                    Sign in to manage your account profile. Create, connect, and measure
                    mobile experiences for your organisation.
                </p>
            </div>

            <div class="login-card">
                <h2>Login</h2>

                @foreach ($errors->all() as $message)
                    <div class="error">{{ $message }}</div>
                @endforeach

                <form method="post" action="{{ route('marketing.portal-login') }}">
                    @csrf
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >

                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >

                    <button type="submit">Login</button>
                </form>

                <a class="register-link" href="{{ url('/portal/register') }}">Register a new account</a>
            </div>
        </section>

        <footer>
            An Australian innovation · Powered by ScanLink Technologies
        </footer>
    </div>
</body>
</html>
