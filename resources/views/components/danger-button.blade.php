<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-red-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-red-500/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
