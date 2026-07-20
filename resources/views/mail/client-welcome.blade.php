<!DOCTYPE html>
<html>
<head>
    <title>ScanLink client account created</title>
</head>
<body>
    <p>Hi {{ $client->contact_person ?: $client->client_name }},</p>
    <p>Your ScanLink client account has been created.</p>
    <p>
        <strong>Portal URL:</strong> {{ $portalUrl }}<br>
        <strong>Login email:</strong> {{ $client->email }}<br>
        <strong>Password:</strong> {{ $plainPassword }}
    </p>
    <p>Please sign in and change your password after your first login.</p>
    <p>Best regards,</p>
    <p>ScanLink Solutions</p>
</body>
</html>
