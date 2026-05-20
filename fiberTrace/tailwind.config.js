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
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
                body: ['Outfit', 'sans-serif'],
                headline: ['Plus Jakarta Sans', 'sans-serif'],
                label: ['Outfit', 'sans-serif'],
            },
            colors: {
                primary: '#00523b',
                'on-primary': '#ffffff',
                secondary: '#2e6b54',
                'secondary-container': '#a6f4d2',
                'secondary-fixed': '#008b5e',
                tertiary: '#3f6373',
                'tertiary-fixed': '#a2cedb',
                'tertiary-fixed-dim': '#7ea1ae',
                error: '#ba1a1a',
                'error-container': '#ffdad6',
                surface: '#f8fafc',
                'surface-variant': '#dee3e5',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f0f4f8',
                'surface-container-high': '#e2e8f0',
                'on-surface': '#191c1d',
                'on-surface-variant': '#40484b',
                outline: '#70787c',
                'outline-variant': '#bfc8cc',
                'inverse-surface': '#2e3132',
                'inverse-on-surface': '#eff1f1',
            },
            spacing: {
                sm: '8px',
                md: '16px',
                lg: '24px',
                xl: '32px',
                'container-padding': '24px',
            },
            animation: {
                marquee: 'marquee 25s linear infinite',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0%)' },
                    '100%': { transform: 'translateX(-100%)' },
                }
            }
        },
    },

    safelist: [
        'text-secondary-fixed',
        'text-error-container',
        'text-surface-variant',
        'animate-bounce',
        'material-symbols-filled',
        'material-symbols-outlined'
    ],

    plugins: [forms],
};
