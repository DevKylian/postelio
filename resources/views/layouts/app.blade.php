<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Postelio') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Umami Analytics -->
    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif

    <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-midnight h-screen overflow-hidden bg-slate-50 relative selection:bg-copper/20 selection:text-copper" x-data="{ sidebarOpen: false }">
        <!-- Global Ambient Background for Glassmorphism -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-orange-500/15 blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-amber-500/15 blur-[120px]"></div>
            <div class="absolute -bottom-[20%] left-[20%] w-[50%] h-[50%] rounded-full bg-copper/15 blur-[120px]"></div>
        </div>

        <div class="relative z-10 flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <livewire:layout.navigation />

            <!-- Main Content -->
            <div class="flex-1 min-w-0 flex flex-col overflow-hidden">

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white/60 backdrop-blur-xl border-b border-white/80 shadow-sm sticky top-0 z-20 shrink-0">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-4 py-4">
                                <!-- Mobile Toggle -->
                                <button @click="sidebarOpen = true"
                                        class="lg:hidden p-3 -ml-2 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-midnight transition-colors focus:outline-none focus:ring-2 focus:ring-copper/30">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>

                                <div class="flex-1 min-w-0">
                                    {{ $header }}
                                </div>
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

        @stack('scripts')
    </body>
</html>
