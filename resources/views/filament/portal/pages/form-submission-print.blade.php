<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form submission — {{ $profile->name }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 2rem; color: #222; }
        h1 { color: #008C00; margin: 0 0 .25rem; font-size: 1.35rem; }
        .meta { color: #666; font-size: .875rem; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { border: 1px solid #ddd; padding: .6rem .75rem; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; width: 35%; font-weight: 600; }
        .print-bar { margin-bottom: 1.5rem; }
        .print-btn { background: #008C00; color: #fff; border: 0; border-radius: 8px; padding: .55rem 1rem; font-weight: 600; cursor: pointer; }
        @media print {
            .print-bar { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button type="button" class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <h1>{{ $profile->form_title ?: $profile->name }}</h1>
    <p class="meta">
        Profile: {{ $profile->name }}<br>
        Session: {{ $sessionId }}<br>
        Submitted: {{ $submittedAt ? \Illuminate\Support\Carbon::parse($submittedAt)->format('d M Y H:i') : '—' }}
    </p>

    <table>
        <tbody>
            @foreach ($answers as $answer)
                <tr>
                    <th>{{ strip_tags($answer->question?->question_text ?: 'Question #'.$answer->question_id) }}</th>
                    <td>{{ $answer->question_answer ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
