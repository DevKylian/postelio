<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-sm text-midnight uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-copper focus:ring-offset-2 disabled:opacity-25 transition-all duration-200 ease-in-out hover:scale-[1.02] active:scale-95']) }}>
    {{ $slot }}
</button>
