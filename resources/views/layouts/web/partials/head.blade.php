<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        @if($siteSettings && $siteSettings->favicon)
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
            <link rel="apple-touch-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
        @else
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <!-- Styles / Scripts -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[200] focus:bg-white focus:text-black focus:px-4 focus:py-2 focus:rounded">
        Skip to main content
    </a>