import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Noto Sans Thai', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: { 
                discord: { 
                    green: '#23a559', 
                    red: '#ed4245', 
                    black: '#1e1f22', 
                    darkbg: '#313338',
                    main: '#313338',
                    darker: '#1e1f22',
                    'green-alt': '#23a55a',
                    'red-alt': '#f23f43'
                },
                brand: { primary: '#23a559', secondary: '#ed4245' },
                hub: {
                    blue: '#4f7cff',
                    mint: '#1ea97c',
                    ink: '#162033',
                    fog: '#eef3ff',
                },
                control: {
                    ink: '#11202d',
                    panel: '#f6f2e8',
                    accent: '#b74d25',
                    accentSoft: '#f5d1b8',
                    moss: '#264f45',
                    sand: '#dfd5c3',
                }
            }
        },
    },
    plugins: [],
};
