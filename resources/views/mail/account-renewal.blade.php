<!DOCTYPE html>
<html>
<head>
    <title>ScanLink account renew notification.</title>
</head>
<body>
    <p>Hi there!</p>
    <p>As usual, ScanLink is always happy to help!</p>
    <p>Your account has been re-newed by admin, your account will expire on {{ $user->expire_at?->format('Y-m-d H:i:s') }}.</p>
    <p>Best regards,</p>
    <p>ScanLink Solutions</p>
</body>
</html>
