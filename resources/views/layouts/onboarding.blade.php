<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Bienvenue sur Postelio</title>

        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#F59E0B">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Postelio">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
        <link rel="apple-touch-icon" href="/images/pwa/icon-192.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Umami Analytics -->
        @if(config('services.umami.id'))
            <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
        @endif
    </head>
    <body class="font-sans antialiased min-h-screen" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);">
        {{ $slot }}
    </body>
</html>
