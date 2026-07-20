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
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    50: 'rgb(var(--color-primary-50) / <alpha-value>)',
                    100: 'rgb(var(--color-primary-100) / <alpha-value>)',
                    200: 'rgb(var(--color-primary-200) / <alpha-value>)',
                    300: 'rgb(var(--color-primary-300) / <alpha-value>)',
                    400: 'rgb(var(--color-primary-400) / <alpha-value>)',
                    500: 'rgb(var(--color-primary-500) / <alpha-value>)',
                    600: 'rgb(var(--color-primary-600) / <alpha-value>)',
                    700: 'rgb(var(--color-primary-700) / <alpha-value>)',
                    800: 'rgb(var(--color-primary-800) / <alpha-value>)',
                    900: 'rgb(var(--color-primary-900) / <alpha-value>)',
                    DEFAULT: 'rgb(var(--color-primary-600) / <alpha-value>)',
                },
                'primary-container': {
                    DEFAULT: 'rgb(var(--color-primary-100) / <alpha-value>)',
                },
                surface: {
                    50: 'rgb(var(--color-surface-50) / <alpha-value>)',
                    100: 'rgb(var(--color-surface-100) / <alpha-value>)',
                    150: 'rgb(var(--color-surface-150) / <alpha-value>)',
                    200: 'rgb(var(--color-surface-200) / <alpha-value>)',
                    300: 'rgb(var(--color-surface-300) / <alpha-value>)',
                    400: 'rgb(var(--color-surface-400) / <alpha-value>)',
                    500: 'rgb(var(--color-surface-500) / <alpha-value>)',
                    600: 'rgb(var(--color-surface-600) / <alpha-value>)',
                    700: 'rgb(var(--color-surface-700) / <alpha-value>)',
                    800: 'rgb(var(--color-surface-800) / <alpha-value>)',
                    900: 'rgb(var(--color-surface-900) / <alpha-value>)',
                    DEFAULT: 'rgb(var(--color-surface-50) / <alpha-value>)',
                },
                success: {
                    DEFAULT: 'rgb(var(--color-success) / <alpha-value>)',
                },
                warning: {
                    DEFAULT: 'rgb(var(--color-warning) / <alpha-value>)',
                },
                error: {
                    DEFAULT: 'rgb(var(--color-error) / <alpha-value>)',
                },
                info: {
                    DEFAULT: 'rgb(var(--color-info) / <alpha-value>)',
                },
                'on-primary': '#FFFFFF',
                'on-primary-container': 'rgb(var(--color-primary-900) / <alpha-value>)',
                'on-surface': 'rgb(var(--color-surface-900) / <alpha-value>)',
            },
            borderRadius: {
                sm: '0.25rem',
                DEFAULT: '0.375rem',
                lg: '0.5rem',
                xl: '0.75rem',
                '2xl': '1rem',
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'soft-md': '0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04)',
                'soft-lg': '0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04)',
                'soft-xl': '0 20px 25px -5px rgb(0 0 0 / 0.08), 0 8px 10px -6px rgb(0 0 0 / 0.04)',
            },
            animation: {
                'kds-urgent': 'kds-urgent-pulse 2s infinite',
                'slide-in': 'slide-in 300ms ease-out',
                'slide-up': 'slide-up 300ms ease-out',
                'fade-in': 'fade-in 200ms ease-out',
            },
            keyframes: {
                'kds-urgent-pulse': {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgb(220 38 38 / 0.4)' },
                    '50%': { boxShadow: '0 0 0 8px rgb(220 38 38 / 0)' },
                },
                'slide-in': {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'slide-up': {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
