import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                midnight: '#1E293B',
                chalk: '#F8FAFC',
                copper: '#F59E0B',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0,0,0,0.04), 0 1px 2px -1px rgba(0,0,0,0.04)',
                'card-hover': '0 10px 30px -5px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
            transitionTimingFunction: {
                'spring': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
            },
        },
    },

    plugins: [forms],
};
