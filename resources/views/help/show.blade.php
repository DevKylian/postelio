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


        </div>
    </div>
</x-app-layout>
