<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <a href="{{ route('help.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-copper transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au centre d'aide
                </a>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    {{ $questionItem['title'] }}
                </h1>
                <p class="text-copper text-sm font-medium mt-1">
                    {{ $categoryName }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">
            <div class="glass-panel p-6 sm:p-10 rounded-2xl relative overflow-hidden">
                {{-- Decorative background --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-copper/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>

                <div class="prose prose-slate prose-copper max-w-none relative z-10 prose-img:rounded-xl prose-img:shadow-sm prose-a:text-copper prose-a:no-underline hover:prose-a:underline prose-headings:text-midnight prose-p:text-gray-600 prose-li:text-gray-600 marker:text-copper prose-strong:text-midnight">
                    {!! $questionItem['content'] !!}
                </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-white/40 border border-gray-100 rounded-2xl shadow-sm backdrop-blur-sm">
                <div>
                    <h3 class="font-semibold text-midnight text-sm">Cet article vous a-t-il été utile ?</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Vos retours nous aident à améliorer notre centre d'aide.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-green-500 hover:border-green-200 hover:bg-green-50 transition-all shadow-sm" title="Oui">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                    </button>
                    <button class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm" title="Non">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
