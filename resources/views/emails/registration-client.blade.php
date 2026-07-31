@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<x-emails.layout title="Welcome to ScanLink">
<p>Hi {{ $firstName }} {{ $lastName }}</p>
<p>Thank you for registering with ScanLink.</p>
<p>To complete your account registration please <a href="{{ $base }}?open_login_box=1">click here</a></p>
<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:admin@scanlink.net.au">admin@scanlink.net.au</a> or call us during business hours on Monday to Friday on 0417557640.</p>
<br/>
<p>Sincerely,</p>
<p>Customer Support Team</p>
</x-emails.layout>
