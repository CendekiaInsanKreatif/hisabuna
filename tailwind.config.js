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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary brand colors (Emerald-based)
                primary: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981', // Main emerald
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                // Secondary colors for different actions
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
                // Success colors
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                // Warning colors
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                // Danger colors
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                // Info colors
                info: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
            },
            boxShadow: {
                'custom-strong': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'emerald': '0 4px 14px 0 rgba(16, 185, 129, 0.25)',
                'emerald-lg': '0 10px 25px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.05)',
                'blue': '0 4px 14px 0 rgba(59, 130, 246, 0.25)',
                'blue-lg': '0 10px 25px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.05)',
                'pink': '0 4px 14px 0 rgba(236, 72, 153, 0.25)',
                'pink-lg': '0 10px 25px -3px rgba(236, 72, 153, 0.3), 0 4px 6px -2px rgba(236, 72, 153, 0.05)',
                'purple': '0 4px 14px 0 rgba(147, 51, 234, 0.25)',
                'purple-lg': '0 10px 25px -3px rgba(147, 51, 234, 0.3), 0 4px 6px -2px rgba(147, 51, 234, 0.05)',
                'soft': '0 2px 4px 0 rgba(0, 0, 0, 0.05)',
                'medium': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'strong': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'xl-soft': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            fontSize: {
                'xs': ['0.75rem', { lineHeight: '1rem' }],
                'sm': ['0.875rem', { lineHeight: '1.25rem' }],
                'base': ['1rem', { lineHeight: '1.5rem' }],
                'lg': ['1.125rem', { lineHeight: '1.75rem' }],
                'xl': ['1.25rem', { lineHeight: '1.75rem' }],
                '2xl': ['1.5rem', { lineHeight: '2rem' }],
                '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
                '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'fade-out': 'fadeOut 0.5s ease-in-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'slide-out': 'slideOut 0.3s ease-out',
                'bounce-subtle': 'bounceSubtle 0.6s ease-in-out',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                'scale-in': 'scaleIn 0.2s ease-out',
                'scale-out': 'scaleOut 0.2s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeOut: {
                    '0%': { opacity: '1' },
                    '100%': { opacity: '0' },
                },
                slideIn: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideOut: {
                    '0%': { transform: 'translateY(0)', opacity: '1' },
                    '100%': { transform: 'translateY(-10px)', opacity: '0' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.8' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                scaleOut: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '100%': { transform: 'scale(0.95)', opacity: '0' },
                },
            },
            backdropBlur: {
                'xs': '2px',
            },
            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
            },
            transitionTimingFunction: {
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [
        forms,
        function({ addComponents }) {
            addComponents({
                // Base button styles
                '.btn': {
                    '@apply inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-soft hover:shadow-medium': {},
                },

                // Button sizes
                '.btn-xs': {
                    '@apply px-2 py-1 text-xs gap-1': {},
                },
                '.btn-sm': {
                    '@apply px-3 py-1.5 text-sm gap-1.5': {},
                },
                '.btn-md': {
                    '@apply px-4 py-2.5 text-sm gap-2': {},
                },
                '.btn-lg': {
                    '@apply px-6 py-3 text-base gap-2': {},
                },
                '.btn-xl': {
                    '@apply px-8 py-4 text-lg gap-3': {},
                },

                // Primary button variants
                '.btn-primary': {
                    '@apply btn bg-primary-600 text-white border border-primary-600 hover:bg-primary-700 hover:border-primary-700 focus:ring-primary-500 active:bg-primary-800': {},
                },
                '.btn-primary-outline': {
                    '@apply btn bg-transparent text-primary-600 border border-primary-600 hover:bg-primary-50 hover:text-primary-700 focus:ring-primary-500 active:bg-primary-100': {},
                },
                '.btn-primary-ghost': {
                    '@apply btn bg-transparent text-primary-600 border-transparent hover:bg-primary-50 hover:text-primary-700 focus:ring-primary-500 active:bg-primary-100': {},
                },

                // Secondary button variants
                '.btn-secondary': {
                    '@apply btn bg-secondary-600 text-white border border-secondary-600 hover:bg-secondary-700 hover:border-secondary-700 focus:ring-secondary-500 active:bg-secondary-800': {},
                },
                '.btn-secondary-outline': {
                    '@apply btn bg-transparent text-secondary-600 border border-secondary-600 hover:bg-secondary-50 hover:text-secondary-700 focus:ring-secondary-500 active:bg-secondary-100': {},
                },
                '.btn-secondary-ghost': {
                    '@apply btn bg-transparent text-secondary-600 border-transparent hover:bg-secondary-50 hover:text-secondary-700 focus:ring-secondary-500 active:bg-secondary-100': {},
                },

                // Success button variants
                '.btn-success': {
                    '@apply btn bg-success-600 text-white border border-success-600 hover:bg-success-700 hover:border-success-700 focus:ring-success-500 active:bg-success-800': {},
                },
                '.btn-success-outline': {
                    '@apply btn bg-transparent text-success-600 border border-success-600 hover:bg-success-50 hover:text-success-700 focus:ring-success-500 active:bg-success-100': {},
                },
                '.btn-success-ghost': {
                    '@apply btn bg-transparent text-success-600 border-transparent hover:bg-success-50 hover:text-success-700 focus:ring-success-500 active:bg-success-100': {},
                },

                // Warning button variants
                '.btn-warning': {
                    '@apply btn bg-warning-600 text-white border border-warning-600 hover:bg-warning-700 hover:border-warning-700 focus:ring-warning-500 active:bg-warning-800': {},
                },
                '.btn-warning-outline': {
                    '@apply btn bg-transparent text-warning-600 border border-warning-600 hover:bg-warning-50 hover:text-warning-700 focus:ring-warning-500 active:bg-warning-100': {},
                },
                '.btn-warning-ghost': {
                    '@apply btn bg-transparent text-warning-600 border-transparent hover:bg-warning-50 hover:text-warning-700 focus:ring-warning-500 active:bg-warning-100': {},
                },

                // Danger button variants
                '.btn-danger': {
                    '@apply btn bg-danger-600 text-white border border-danger-600 hover:bg-danger-700 hover:border-danger-700 focus:ring-danger-500 active:bg-danger-800': {},
                },
                '.btn-danger-outline': {
                    '@apply btn bg-transparent text-danger-600 border border-danger-600 hover:bg-danger-50 hover:text-danger-700 focus:ring-danger-500 active:bg-danger-100': {},
                },
                '.btn-danger-ghost': {
                    '@apply btn bg-transparent text-danger-600 border-transparent hover:bg-danger-50 hover:text-danger-700 focus:ring-danger-500 active:bg-danger-100': {},
                },

                // Info button variants
                '.btn-info': {
                    '@apply btn bg-info-600 text-white border border-info-600 hover:bg-info-700 hover:border-info-700 focus:ring-info-500 active:bg-info-800': {},
                },
                '.btn-info-outline': {
                    '@apply btn bg-transparent text-info-600 border border-info-600 hover:bg-info-50 hover:text-info-700 focus:ring-info-500 active:bg-info-100': {},
                },
                '.btn-info-ghost': {
                    '@apply btn bg-transparent text-info-600 border-transparent hover:bg-info-50 hover:text-info-700 focus:ring-info-500 active:bg-info-100': {},
                },

                // White/Light button variants
                '.btn-white': {
                    '@apply btn bg-white text-secondary-700 border border-secondary-300 hover:bg-secondary-50 hover:text-secondary-800 focus:ring-secondary-500 active:bg-secondary-100': {},
                },
                '.btn-light': {
                    '@apply btn bg-secondary-100 text-secondary-700 border border-secondary-200 hover:bg-secondary-200 hover:text-secondary-800 focus:ring-secondary-500 active:bg-secondary-300': {},
                },

                // Dark button variant
                '.btn-dark': {
                    '@apply btn bg-secondary-800 text-white border border-secondary-800 hover:bg-secondary-900 hover:border-secondary-900 focus:ring-secondary-700 active:bg-secondary-900': {},
                },

                // Button groups
                '.btn-group': {
                    '@apply inline-flex rounded-xl shadow-soft': {},
                },
                '.btn-group .btn': {
                    '@apply rounded-none shadow-none border-r-0 first:rounded-l-xl first:border-r last:rounded-r-xl last:border-r focus:z-10': {},
                },

                // Icon buttons (square buttons for icons only)
                '.btn-icon': {
                    '@apply btn aspect-square p-0': {},
                },
                '.btn-icon.btn-xs': {
                    '@apply w-6 h-6': {},
                },
                '.btn-icon.btn-sm': {
                    '@apply w-8 h-8': {},
                },
                '.btn-icon.btn-md': {
                    '@apply w-10 h-10': {},
                },
                '.btn-icon.btn-lg': {
                    '@apply w-12 h-12': {},
                },
                '.btn-icon.btn-xl': {
                    '@apply w-16 h-16': {},
                },

                // Loading state
                '.btn-loading': {
                    '@apply relative pointer-events-none': {},
                },
                '.btn-loading::before': {
                    content: '""',
                    '@apply absolute inset-0 flex items-center justify-center': {},
                },
                '.btn-loading .btn-loading-spinner': {
                    '@apply w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin': {},
                },

                // Button with badge/counter
                '.btn-with-badge': {
                    '@apply relative': {},
                },
                '.btn-badge': {
                    '@apply absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 text-xs font-medium bg-danger-500 text-white rounded-full flex items-center justify-center': {},
                },

                // Floating action button
                '.btn-fab': {
                    '@apply fixed bottom-6 right-6 btn-primary btn-icon btn-lg rounded-full shadow-lg hover:shadow-xl z-50': {},
                },
            })
        }
    ],
};
