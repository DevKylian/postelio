<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-chalk text-midnight" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen overflow-x-hidden">
            <!-- Sidebar -->
            <livewire:layout.navigation />

            <!-- Main Content -->
            <div class="flex-1 min-w-0 flex flex-col">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white/80 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-20">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center gap-4">
                            <!-- Mobile Toggle -->
                            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl bg-chalk text-midnight hover:bg-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="flex-1">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
