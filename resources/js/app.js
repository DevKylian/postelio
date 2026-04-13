import './bootstrap';
import './onboarding-tour';
import iro from '@jaames/iro';
window.iro = iro;

/**
 * City autocomplete Alpine.js component.
 * Defined here (not inline in Blade) so it is always available as a global,
 * even when Livewire re-renders the page without re-executing inline scripts.
 *
 * Uses the French Government address API (api-adresse.data.gouv.fr) for city
 * suggestions, then geo.api.gouv.fr to fetch nearby communes for AI city
 * name recognition.
 */
window.cityAutocomplete = function cityAutocomplete(initialCity, wireCity, wirePostal, wireContext, wireNearby) {
    return {
        query:            initialCity || '',
        results:          [],
        open:             false,
        loading:          false,
        loadingNearby:    false,
        selected:         !!initialCity,
        highlightedIndex: -1,

        async search() {
            this.selected = false;
            const q = this.query.trim();
            if (q.length < 2) {
                this.results = [];
                this.open    = false;
                return;
            }

            this.loading = true;
            this.open    = false;

            try {
                const url = 'https://api-adresse.data.gouv.fr/search/?q='
                    + encodeURIComponent(q)
                    + '&type=municipality&limit=8&autocomplete=1';

                const response = await fetch(url);
                const data     = await response.json();

                const seen = new Set();
                this.results = (data.features || []).reduce((acc, feature) => {
                    const props = feature.properties;
                    const key   = (props.city || props.name) + '|' + props.postcode;
                    if (!seen.has(key)) {
                        seen.add(key);
                        acc.push({
                            city:     props.city || props.name,
                            postcode: props.postcode,
                            context:  props.context || '',
                            lat:      feature.geometry.coordinates[1],
                            lng:      feature.geometry.coordinates[0],
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

        async selectResult(result) {
            this.query            = result.city;
            this.results          = [];
            this.open             = false;
            this.selected         = false;
            this.highlightedIndex = -1;

            this.$wire.set(wireCity,    result.city);
            this.$wire.set(wirePostal,  result.postcode);
            this.$wire.set(wireContext, result.context);

            if (result.lat && result.lng) {
                this.loadingNearby = true;
                try {
                    const geoUrl = 'https://geo.api.gouv.fr/communes?lat='
                        + result.lat + '&lon=' + result.lng
                        + '&distance=35000&fields=nom,codesPostaux&boost=population&limit=60&type=commune-actuelle';

                    const geoResp  = await fetch(geoUrl);
                    const communes = await geoResp.json();

                    const nearbyCityNames = (communes || [])
                        .filter(c => c.nom !== result.city)
                        .map(c => {
                            const cp = (c.codesPostaux || [])[0] || '';
                            return cp ? c.nom + ' (' + cp + ')' : c.nom;
                        });

                    this.$wire.set(wireNearby, nearbyCityNames);
                } catch (e) {
                    this.$wire.set(wireNearby, []);
                } finally {
                    this.loadingNearby = false;
                }
            }

            this.selected = true;
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
            this.open             = false;
            this.highlightedIndex = -1;
        },

        handleBlur() {
            setTimeout(() => this.close(), 200);
        },
    };
};

/**
 * Premium Spectrum Color Picker Alpine Component.
 * Integrates with iro.js for a high-end, cross-device experience.
 */
window.spectrumColorPicker = function(initialColor) {
    return {
        internalColor: initialColor || '#F59E0B',
        picker: null,
        
        initPicker(el) {
            if (this.picker || !el) return;

            this.picker = new window.iro.ColorPicker(el, {
                width: 220,
                color: this.internalColor || '#F59E0B',
                borderWidth: 2,
                borderColor: "#fff",
                layout: [
                    { 
                        component: window.iro.ui.Wheel,
                        options: { wheelAngle: 0, wheelDirection: 'anticlockwise' }
                    },
                    { 
                        component: window.iro.ui.Slider,
                        options: { sliderType: 'value' }
                    },
                ]
            });

            this.picker.on('color:change', (color) => {
                this.internalColor = color.hexString;
            });

            this.$watch('internalColor', (value) => {
                if (!value) return;
                const hex = String(value);
                if (this.picker && this.picker.color.hexString.toLowerCase() !== hex.toLowerCase()) {
                    this.picker.color.set(hex);
                }
            });
        }
    };
};

// Unregister any previously installed service workers
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(registrations => {
        registrations.forEach(reg => reg.unregister());
    });
}
