<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Postelio - Mode Hors-ligne</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        midnight: '#0F172A',
                        copper: '#B45309',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-6 text-center">
    <div class="max-w-md w-full space-y-8 animate-in fade-in zoom-in duration-500">
        {{-- Icon --}}
        <div class="w-24 h-24 mx-auto bg-white rounded-3xl shadow-xl shadow-gray-200/50 flex items-center justify-center relative">
            <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 border-4 border-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-3.536 5 5 0 015-5c1.38 0 2.63.56 3.536 1.464M9.172 9.172L7.5 7.5M6.75 12a5.25 5.25 0 01.223-1.5m0 3a5.25 5.25 0 01-.223-1.5m0 0l-1.5-1.5" />
                </svg>
            </div>
            <svg class="w-12 h-12 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
            </svg>
        </div>

        {{-- Text --}}
        <div class="space-y-3">
            <h1 class="text-3xl font-bold text-midnight tracking-tight">Vous êtes déconnecté</h1>
            <p class="text-gray-500 leading-relaxed">
                Pas de panique ! Postelio continue de fonctionner. Vous pouvez toujours préparer vos posts, ils seront synchronisés dès que vous retrouverez une connexion.
            </p>
        </div>

        {{-- Actions --}}
        <div class="grid gap-3">
            <button onclick="window.location.reload()" class="w-full bg-midnight text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-slate-800 transition-all active:scale-[0.98]">
                Réessayer la connexion
            </button>
            <a href="/" class="w-full bg-white text-midnight font-semibold py-4 rounded-2xl border border-gray-100 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à l'accueil
            </a>
        </div>

        {{-- Status info --}}
        <div class="pt-8 flex items-center justify-center gap-2 text-xs text-gray-400 font-medium">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            Mode Hors-ligne activé
        </div>
    </div>

    <script>
        // Check connectivity repeatedly
        window.addEventListener('online', () => {
            window.location.href = '/';
        });
    </script>
</body>
</html>
