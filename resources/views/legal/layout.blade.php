<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Légal') — Postelio</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Umami Analytics -->
    @if(config('services.umami.id'))
        <script async src="{{ config('services.umami.url') }}/script.js" data-website-id="{{ config('services.umami.id') }}"></script>
    @endif
</head>
<body class="font-sans antialiased text-midnight bg-chalk selection:bg-copper/20 selection:text-copper min-h-screen flex flex-col">
    
    <header class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200/50 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="transition-opacity hover:opacity-80">
                <x-logo size="md" />
            </a>
            <a href="/" class="text-sm font-medium text-slate-500 hover:text-midnight transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour au site
            </a>
        </div>
    </header>

    <main class="flex-grow w-full py-12 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] p-8 sm:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                <div class="mb-10 pb-8 border-b border-slate-100 text-center">
                    <h1 class="text-3xl sm:text-4xl font-display font-bold text-midnight tracking-tight">@yield('title')</h1>
                    <p class="text-slate-400 mt-4 text-sm font-medium uppercase tracking-widest">Dernière mise à jour : {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                </div>
                
                <div class="prose prose-slate prose-copper max-w-none prose-headings:font-display prose-headings:font-semibold prose-a:text-copper prose-a:no-underline hover:prose-a:underline prose-p:leading-relaxed prose-li:leading-relaxed truncate-words">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-auto border-t border-slate-200/50 bg-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400 font-light">
            &copy; {{ date('Y') }} Postelio. Outil certifié conforme RGPD.
        </div>
    </footer>

</body>
</html>
