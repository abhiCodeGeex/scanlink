<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->name }} — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f5f5f5; color: #222; }
        .wrap { max-width: 640px; margin: 0 auto; padding: 1.5rem; }
        .card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        h1 { color: #008C00; margin-top: 0; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input, textarea { width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .notice { background: #e8f5e9; color: #1b5e20; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
        .visitor-form { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        @if (session('form_submitted'))
            <div class="notice">Thank you — your form was submitted.</div>
        @endif

        <h1>{{ $profile->name }}</h1>

        @if ($profile->description)
            <p>{{ $profile->description }}</p>
        @endif

        @if ($profile->address)
            <p><strong>Address:</strong> {{ $profile->address }}</p>
        @endif

        @if ($needsVisitorInfo)
            <div class="visitor-form">
                <h2>Visitor information</h2>
                <form method="post" action="{{ route('scan.visitor', [$clientUrl, $profile->id]) }}">
                    @csrf
                    @if ($profile->data_collection_name)
                        <label>Name</label>
                        <input type="text" name="user_name" required>
                    @endif
                    @if ($profile->data_collection_email)
                        <label>Email</label>
                        <input type="email" name="user_email" required>
                    @endif
                    @if ($profile->data_collection_mobile)
                        <label>Mobile</label>
                        <input type="text" name="user_mobile">
                    @endif
                    <p style="margin-top:1rem;"><button class="btn" type="submit">Continue</button></p>
                </form>
            </div>
        @endif

        @if ($profile->form_active && $questions->isNotEmpty() && ! $needsVisitorInfo)
            <form method="post" action="{{ route('scan.form', [$clientUrl, $profile->id]) }}" style="margin-top:1.5rem;">
                @csrf
                <h2>Form</h2>
                @foreach ($questions as $question)
                    <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                    <textarea name="answers[{{ $question->question_id }}]" rows="2" @if($question->is_mandatory) required @endif></textarea>
                @endforeach
                <p style="margin-top:1rem;"><button class="btn" type="submit">Submit</button></p>
            </form>
        @endif
    </div>
</div>
</body>
</html>
