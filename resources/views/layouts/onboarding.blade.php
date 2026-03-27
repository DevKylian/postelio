<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Bienvenue sur Postelio</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/postelio-logo-gradient.svg') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);">
        {{ $slot }}
    </body>
</html>
