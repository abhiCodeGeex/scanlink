<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $profile->code_profile_name ?: $profile->name ?: 'ScanLink' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; background: #fff; color: #555755; font-size: 14px; line-height: 22px; }
        .wrap { max-width: 640px; margin: 0 auto; padding: 1.5rem; text-align: center; }
        .logo img { max-width: 100%; max-height: 140px; margin: 1rem auto; display: inline-block; }
        .popup-msg { margin: 1rem 0; }
        form { text-align: left; margin: 1rem auto 0; max-width: 360px; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input { width: 100%; padding: .6rem; margin-top: .25rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .7rem 1.75rem; border: 0; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 1.25rem; }
    </style>
</head>
<body>
<div class="wrap">
    @if ($profile->logos->isNotEmpty())
        <div class="logo">
            @foreach ($profile->logos as $logo)
                @if ($u = $publicMediaUrl($logo->logo_name))<img src="{{ $u }}" alt="Logo">@endif
            @endforeach
        </div>
    @endif

    <div class="popup-msg">{{ $profile->data_collection_content ?: 'Register your details to continue.' }}</div>

    <form method="post" action="{{ route('scan.visitor', [$clientUrl, $profile->id]) }}">
        @csrf
        {{-- Legacy: required only in compulsory mode (set_up_compulsory); mobile must be 10 digits. --}}
        @if ($profile->data_collection_name)
            <label>Name</label>
            <input type="text" name="name" {{ $profile->set_up_compulsory ? 'required' : '' }}>
        @endif
        @if ($profile->data_collection_surname)
            <label>Surname</label>
            <input type="text" name="surname" {{ $profile->set_up_compulsory ? 'required' : '' }}>
        @endif
        @if ($profile->data_collection_email)
            <label>Email</label>
            <input type="email" name="email" maxlength="255" {{ $profile->set_up_compulsory ? 'required' : '' }}>
        @endif
        @if ($profile->data_collection_mobile)
            <label>Mobile</label>
            <input type="text" name="mobile" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Enter a valid 10 digit mobile phone number" {{ $profile->set_up_compulsory ? 'required' : '' }}>
        @endif
        <div style="text-align:center;"><button type="submit" class="btn">Proceed</button></div>
    </form>
</div>
</body>
</html>
