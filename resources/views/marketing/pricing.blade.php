<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        h1 { color: #008C00; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: .75rem; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Code pricing</h1>
    @if ($tiers->isEmpty())
        <p>Contact us for current pricing.</p>
    @else
        <table>
            <thead>
                <tr><th>Quantity</th><th>Price per code (AUD)</th></tr>
            </thead>
            <tbody>
                @foreach ($tiers as $tier)
                    <tr>
                        <td>{{ $tier->code_min_qty }} – {{ $tier->code_max_qty }}</td>
                        <td>${{ number_format((float) $tier->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <p><a href="{{ route('marketing.home') }}">Back</a></p>
</body>
</html>
