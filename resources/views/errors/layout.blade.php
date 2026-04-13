<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Postelio</title>

    <!-- App Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        :root {
            --midnight: #0f172a;
            --copper: #f59e0b; /* Using amber-500 matching your brand */
            --slate-400: #94a3b8;
            --slate-500: #64748b;
        }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            background: #ffffff;
            color: var(--midnight);
            overflow: hidden;
        }
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .pointer-events-none { pointer-events: none; }
        .overflow-hidden { overflow: hidden; }
        .z-10 { z-index: 10; }
        .w-full { width: 100%; }
        .max-w-lg { max-w: 32rem; }
        .text-center { text-align: center; }
        .p-6 { padding: 1.5rem; }
        .mb-12 { margin-bottom: 3rem; }
        .mb-10 { margin-bottom: 2.5rem; }
        .mt-16 { margin-top: 4rem; }
        .text-xs { font-size: 0.75rem; }
        .font-medium { font-weight: 500; }
        .font-bold { font-weight: 700; }
        .uppercase { text-transform: uppercase; }
        .tracking-wide { letter-spacing: 0.025em; }
        .tracking-tight { letter-spacing: -0.025em; }
        .opacity-60 { opacity: 0.6; }
        .opacity-80 { opacity: 0.8; }
        
        /* Blur decorations */
        .bg-copper-5 { background-color: rgba(245, 158, 11, 0.05); }
        .bg-amber-5 { background-color: rgba(245, 158, 11, 0.05); }
        .blur-120 { filter: blur(120px); }
        .rounded-full { border-radius: 9999px; }

        /* Animations */
        .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Simple Logo Replacement (Inlined) */
        .logo-circle {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #fbbf24, #d97706);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        .logo-text {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--midnight);
            margin-left: 12px;
            letter-spacing: -0.03em;
        }

        /* Support for child view utility classes */
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .max-w-md { max-width: 28rem; margin: 0 auto; }
        .mt-4 { margin-top: 1rem; }
        .text-slate-500 { color: var(--slate-500); }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
        .w-20 { width: 5rem; }
        .h-20 { height: 5rem; }
        .bg-copper-10 { background-color: rgba(245, 158, 11, 0.1); }
        .rounded-3xl { border-radius: 1.5rem; }
        .text-copper { color: var(--copper); }
        .mt-12 { margin-top: 3rem; }
        .w-48 { width: 12rem; }
        .h-1 { height: 0.25rem; }
        .bg-slate-100 { background-color: #f1f5f9; }
        .mt-3 { margin-top: 0.75rem; }
    </style>
</head>
<body class="min-h-screen">
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6">
        
        <!-- Background Decorations -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute rounded-full bg-copper-5 blur-120" style="top: -10%; right: -10%; width: 600px; height: 600px;"></div>
            <div class="absolute rounded-full bg-amber-5 blur-120" style="bottom: -10%; left: -10%; width: 600px; height: 600px;"></div>
        </div>

        <div class="relative z-10 w-full max-w-lg text-center animate-fade-in">
            <!-- Logo (Independent) -->
            <div class="mb-12 flex items-center justify-center">
                <div class="logo-circle">
                    <svg style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="4" y1="8" x2="4" y2="16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="8" y1="5" x2="8" y2="19" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="12" y1="2" x2="12" y2="22" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="16" y1="6" x2="16" y2="18" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="20" y1="9" x2="20" y2="15" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="logo-text">Postelio</span>
            </div>

            <!-- Content Slot -->
            <div class="space-y-6">
                @yield('content')
            </div>

            <!-- Footer -->
            <p class="mt-16 text-xs text-slate-400 font-medium tracking-wide uppercase opacity-60">
                &copy; {{ date('Y') }} Postelio.
            </p>
        </div>
    </div>
</body>
</html>
