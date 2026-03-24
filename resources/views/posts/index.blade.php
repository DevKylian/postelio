<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Mes Créations
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $posts->count() }} {{ $posts->count() > 1 ? 'visuels générés' : 'visuel généré' }}
                </p>
            </div>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-xl !gap-2 !py-2.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau post
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @livewire('post-list')
        </div>
    </div>

    <script>
        async function downloadAllSlides(urls, caption) {
            const cleanCaption = caption.normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/[^a-z0-9]/gi, '_')
                .toLowerCase()
                .substring(0, 30);

            for (let i = 0; i < urls.length; i++) {
                const link = document.createElement('a');
                link.href = urls[i];
                link.download = `${cleanCaption}_slide_${i + 1}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
    </script>
</x-app-layout>
