<!DOCTYPE html>
<html>
<head>
    <title>Welcome to ScanLink</title>
</head>
<body style="font-family:calibri, Arial, Helvetica, sans-serif;">
    <p>
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="logo" style="max-height: 60px;">
        </a>
    </p>
    <hr>
    <p>Hi {{ $firstName }} {{ $lastName }}</p>
    <p>Thank you for registering with ScanLink.</p>
    <p>
        To complete your account registration please
        <a href="{{ $loginUrl }}">click here</a>
    </p>
    <p>
        If you have any questions or require assistance you can contact us at anytime at
        <a href="mailto:admin@scanlink.net.au">admin@scanlink.net.au</a>
        or call us during business hours on Monday to Friday on 0417557640.
    </p>
    <br>
    <p>Sincerely,</p>
    <p>Customer Support Team</p>
    <p><a href="{{ url('/') }}">ScanLink</a></p>
    <p style="text-align:center;font-size:11px;">
        ScanLink is a division of NWIT Services ABN 12 838 792 695
    </p>
</body>
</html>
