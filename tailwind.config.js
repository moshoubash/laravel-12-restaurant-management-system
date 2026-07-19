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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                arabic: ['Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'rgb(var(--color-primary-rgb, 232 89 12) / <alpha-value>)',
                'primary-container': 'rgb(var(--color-primary-container-rgb, 255 237 213) / <alpha-value>)',
                'on-surface': 'rgb(var(--color-on-surface-rgb, 30 30 30) / <alpha-value>)',
                'surface-container-lowest': 'rgb(var(--color-surface-container-lowest-rgb, 255 255 255) / <alpha-value>)',
                'surface-container-low': 'rgb(var(--color-surface-container-low-rgb, 250 250 249) / <alpha-value>)',
                'surface-container': 'rgb(var(--color-surface-container-rgb, 245 245 244) / <alpha-value>)',
                'surface-container-high': 'rgb(var(--color-surface-container-high-rgb, 231 229 228) / <alpha-value>)',
                'surface-container-highest': 'rgb(var(--color-surface-container-highest-rgb, 214 211 209) / <alpha-value>)',
                secondary: 'rgb(var(--color-secondary-rgb, 120 113 108) / <alpha-value>)',
                error: 'rgb(var(--color-error-rgb, 220 38 38) / <alpha-value>)',
                'on-primary-container': 'rgb(var(--color-on-primary-container-rgb, 87 33 0) / <alpha-value>)',
            },
            borderRadius: {
                DEFAULT: '0.375rem',
                lg: '0.5rem',
                full: '0.75rem',
            },
        },
    },

    plugins: [forms],
};
