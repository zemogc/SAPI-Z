import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#ecfdf7',
                    100: '#d1faec',
                    200: '#a7f3d9',
                    300: '#6ee7c0',
                    400: '#34d3a2',
                    500: '#10b989',
                    600: '#059670',
                    700: '#04785c',
                    800: '#065f4a',
                    900: '#064e3e',
                    950: '#022c23',
                },
                ink: {
                    900: '#0b1220',
                    800: '#111a2e',
                    700: '#1b2640',
                },
            },
            boxShadow: {
                soft: '0 1px 2px rgba(16,24,40,.04), 0 4px 16px rgba(16,24,40,.06)',
            },
        },
    },

    plugins: [forms],
};
