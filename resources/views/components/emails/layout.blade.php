@props(['title' => 'ScanLink', 'division' => 'NWIT Services', 'font' => 'calibri'])
@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<html>
<head>
<title>{{ $title }}</title>
</head>
<body style="font-family:{{ $font }};">
<p><a href="{{ $base }}"><img src="{{ $base }}images/email-logo.png" alt="logo" /></a></p>
<hr/>
{{ $slot }}
<p><a href="{{ $base }}">ScanLink</a></p>
<p style="text-align:center;font-size:11px;">ScanLink is a division of {{ $division }} ABN 12 838 792 695</p>
</body>
</html>
