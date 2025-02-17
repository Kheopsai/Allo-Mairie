import defaultTheme from 'tailwindcss/defaultTheme';
import colors from "tailwindcss/colors";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode:'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
        "./vendor/wireui/wireui/src/*.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/WireUi/**/*.php",
        "./vendor/wireui/wireui/src/Components/**/*.php",
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    '50': '#f0f8ff',
                    '100': '#e0effe',
                    '200': '#bbe0fc',
                    '300': '#7ec8fb',
                    '400': '#3aabf6',
                    '500': '#1091e7',
                    '600': '#0472c5',
                    '700': '#045494',
                    '800': '#084e84',
                    '900': '#0d416d',
                    '950': '#092948',
                },
                secondary: colors.slate,
                positive: colors.emerald,
                negative: colors.red,
                warning: colors.amber,
                info: colors.blue
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require('@tailwindcss/aspect-ratio'),
        require('flowbite/plugin'),
    ],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
};
