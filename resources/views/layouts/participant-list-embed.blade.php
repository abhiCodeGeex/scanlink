<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Participant List' }}</title>
    @filamentStyles
    @livewireStyles
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow-x: hidden !important;
            overflow-y: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            box-sizing: border-box !important;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
        }
        *, *::before, *::after { box-sizing: border-box !important; }
        .fi-body, .fi-simple-layout, .fi-simple-main, .fi-page,
        .fi-page-main, .fi-page-content, .fi-main, .fi-main-ctn,
        [wire\:id] {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            box-shadow: none !important;
            background: transparent !important;
            border: 0 !important;
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
    @filamentScripts
</body>
</html>
