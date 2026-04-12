{{--
    City autocomplete component using the French Government's address API (api-adresse.data.gouv.fr).

    Props:
      - wireCity    : Livewire property name for the city name   (default: 'city')
      - wirePostal  : Livewire property name for the postal code (default: 'postal_code')
      - wireContext : Livewire property name for the geo context (default: 'city_context')
      - currentCity : Current city value to display in the input
      - inputClass  : Optional extra CSS classes for the input
      - placeholder : Input placeholder text
      - required    : Whether the field is required (boolean)
--}}
@props([
    'wireCity'    => 'city',
    'wirePostal'  => 'postal_code',
    'wireContext' => 'city_context',
    'currentCity' => '',
    'inputClass'  => '',
    'placeholder' => 'Ex : Lannion',
    'required'    => false,
])

<div
    x-data="cityAutocomplete(@js($currentCity), @js($wireCity), @js($wirePostal), @js($wireContext))"
    class="relative"
>
    {{-- Search input --}}
    <div class="relative">
        <input
            x-model="query"
            x-on:input.debounce.400ms="search()"
            x-on:keydown.arrow-down.prevent="highlight(1)"
            x-on:keydown.arrow-up.prevent="highlight(-1)"
            x-on:keydown.enter.prevent="selectHighlighted()"
            x-on:keydown.escape="close()"
            x-on:focus="query.length >= 2 && results.length && (open = true)"
            x-on:blur="handleBlur()"
            type="text"
            autocomplete="off"
            @if($required) required @endif
            placeholder="{{ $placeholder }}"
            class="{{ $inputClass }}"
        />
        {{-- Spinner --}}
        <div x-show="loading" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>
        {{-- Check mark when a city is selected --}}
        <div x-show="selected && !loading" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    {{-- Dropdown --}}
    <ul
        x-show="open && results.length > 0"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
        role="listbox"
    >
        <template x-for="(result, index) in results" :key="index">
            <li
                x-on:mousedown.prevent="selectResult(result)"
                x-on:mouseover="highlightedIndex = index"
                :class="highlightedIndex === index ? 'bg-amber-50' : 'hover:bg-gray-50'"
                class="flex items-center justify-between px-4 py-2.5 cursor-pointer transition-colors duration-100 border-b border-gray-100 last:border-0"
                role="option"
            >
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-800 truncate" x-text="result.city"></span>
                </div>
                <span class="text-xs font-mono text-gray-400 flex-shrink-0 ml-2" x-text="result.postcode"></span>
            </li>
        </template>
    </ul>

    {{-- No results hint --}}
    <div
        x-show="open && results.length === 0 && query.length >= 2 && !loading"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl px-4 py-3"
    >
        <p class="text-sm text-gray-400 text-center">Aucune ville trouvée pour "{{ '<span x-text=\'query\'></span>' }}"</p>
    </div>
</div>

<script>
function cityAutocomplete(initialCity, wireCity, wirePostal, wireContext) {
    return {
        query: initialCity || '',
        results: [],
        open: false,
        loading: false,
        selected: !!initialCity,
        highlightedIndex: -1,

        async search() {
            this.selected = false;
            const q = this.query.trim();
            if (q.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }

            this.loading = true;
            this.open = false;

            try {
                const url = 'https://api-adresse.data.gouv.fr/search/?q='
                    + encodeURIComponent(q)
                    + '&type=municipality&limit=8&autocomplete=1';

                const response = await fetch(url);
                const data = await response.json();

                // Deduplicate by city+postcode pair (API sometimes returns duplicates)
                const seen = new Set();
                this.results = (data.features || []).reduce((acc, feature) => {
                    const props = feature.properties;
                    const key = props.city + '|' + props.postcode;
                    if (!seen.has(key)) {
                        seen.add(key);
                        acc.push({
                            city:     props.city || props.name,
                            postcode: props.postcode,
                            context:  props.context || '', // "22, Côtes-d'Armor, Bretagne"
                        });
                    }
                    return acc;
                }, []);

                this.highlightedIndex = -1;
                this.open = this.results.length > 0;
            } catch (e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        selectResult(result) {
            this.query     = result.city;
            this.results   = [];
            this.open      = false;
            this.selected  = true;
            this.highlightedIndex = -1;

            // Update Livewire component properties
            $wire.set(wireCity,    result.city);
            $wire.set(wirePostal,  result.postcode);
            $wire.set(wireContext, result.context);
        },

        highlight(direction) {
            if (!this.open || this.results.length === 0) return;
            this.highlightedIndex = Math.max(
                0,
                Math.min(this.results.length - 1, this.highlightedIndex + direction)
            );
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.results[this.highlightedIndex]) {
                this.selectResult(this.results[this.highlightedIndex]);
            }
        },

        close() {
            this.open = false;
            this.highlightedIndex = -1;
        },

        handleBlur() {
            // Small delay so mousedown on a result fires before blur hides dropdown
            setTimeout(() => this.close(), 200);
        },
    };
}
</script>
