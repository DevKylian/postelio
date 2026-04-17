@if ($paginator->hasPages())
<nav class="flex flex-col items-center gap-3 py-4" aria-label="{{ __('Pagination Navigation') }}">

    <div class="flex items-center gap-1 flex-wrap justify-center">
        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 rounded-xl border border-gray-100 text-gray-300 text-sm select-none">&lsaquo;</span>
        @else
            <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev"
                class="px-3 py-2 rounded-xl border border-gray-200 text-gray-500 text-sm hover:border-copper hover:text-copper transition-colors">
                &lsaquo;
            </button>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-2 text-gray-300 text-sm select-none">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 rounded-xl bg-midnight text-white text-sm font-bold select-none">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                            class="px-3 py-2 rounded-xl border border-gray-200 text-gray-500 text-sm hover:border-copper hover:text-copper transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next"
                class="px-3 py-2 rounded-xl border border-gray-200 text-gray-500 text-sm hover:border-copper hover:text-copper transition-colors">
                &rsaquo;
            </button>
        @else
            <span class="px-3 py-2 rounded-xl border border-gray-100 text-gray-300 text-sm select-none">&rsaquo;</span>
        @endif
    </div>

    @if ($paginator->firstItem())
    <p class="text-xs text-gray-400">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }}
    </p>
    @endif

</nav>
@endif
