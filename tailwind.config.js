import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef0ff',
                    100: '#e0e3ff',
                    200: '#c6caff',
                    300: '#a3a8ff',
                    400: '#8285fb',
                    500: '#6a63f1',
                    600: '#5b4fe5',
                    700: '#4c3fc9',
                    800: '#3f36a3',
                    900: '#363082',
                    950: '#211c4d',
                },
                coral: {
                    50: '#fff1f1',
                    100: '#ffe1e2',
                    200: '#ffc9cb',
                    300: '#feA1A5',
                    400: '#fb6b71',
                    500: '#f43f47',
                    600: '#e0242f',
                    700: '#bc1a24',
                    800: '#9c1922',
                    900: '#821a22',
                },
                mint: {
                    50: '#eefcf5',
                    100: '#d6f7e6',
                    200: '#b0edd0',
                    300: '#7bdcb4',
                    400: '#45c393',
                    500: '#22a877',
                    600: '#158761',
                    700: '#126c50',
                    800: '#125641',
                    900: '#104736',
                },
                ink: {
                    50: '#f6f6f8',
                    100: '#eceef2',
                    200: '#d7dae2',
                    300: '#b0b5c2',
                    400: '#868c9c',
                    500: '#666c7d',
                    600: '#4d5266',
                    700: '#383c4c',
                    800: '#232634',
                    900: '#14151d',
                    950: '#0a0a0f',
                },
                surface: '#f4f5fb',
            },
            boxShadow: {
                soft: '0 2px 8px -2px rgb(20 21 40 / 0.06), 0 1px 2px -1px rgb(20 21 40 / 0.04)',
                card: '0 4px 24px -6px rgb(20 21 40 / 0.08), 0 2px 8px -2px rgb(20 21 40 / 0.04)',
                'card-hover': '0 16px 36px -8px rgb(20 21 40 / 0.14), 0 4px 12px -2px rgb(20 21 40 / 0.06)',
            },
        },
    },

    plugins: [forms],
};
