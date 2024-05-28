import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                'ghost-white': '#F7F8FF',
                'savoy-blue': '#516AC4',
                'vista-blue': '#7991E6',
                'persian-red': '#C33A3A',
                'dark-pastel-green': '#51C451',
                'battleship-gray': '#959595',
                'silver': '#CDCDCD',
                'emerald': '#80D486',
                'light-coral': '#DC8288',
                'saffron': '#F1C749',
                'ecru': '#D6BB70',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'serif'], 
                fontawesome: ['Font Awesome 5 Free']
            },
            fontSize: {
                xs: '0.50rem',
                sm: '0.75rem', // 14px
                base: '.875rem', // 16px
                lg: '1rem', // 18px
                xl: '1.125rem', // 20px
                '2xl': '1.25rem', // 24px
                '3xl': '1.5rem', // 30px
                '4xl': '1.675rem', // 36px
                '5xl': '2.25rem', // 48px
                '6xl': '3rem', // 64px
              },
        },
    },

    plugins: [forms],
};
