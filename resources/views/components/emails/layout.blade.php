@props(['title' => 'ScanLink', 'division' => 'NWIT Services', 'font' => 'calibri', 'logo' => null])
@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<html>
<head>
<title>{{ $title }}</title>
</head>
<body style="font-family:{{ $font }};">
<p><a href="{{ $base }}"><img src="{{ $logo ?: $base.'images/email-logo.png' }}" alt="logo" style="max-height:90px;max-width:260px;height:auto;" /></a></p>
<hr/>
{{ $slot }}
<p><a href="{{ $base }}">ScanLink</a></p>
<p style="text-align:center;font-size:11px;">ScanLink is a division of {{ $division }} ABN 12 838 792 695</p>
</body>
</html>
