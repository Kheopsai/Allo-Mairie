import defaultTheme from "tailwindcss/defaultTheme";
import colors from "tailwindcss/colors";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/wire-elements/modal/resources/views/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            maxWidth: {
                "8xl": "82rem",
            },
            colors: {
                primary: {
                    50: "#f0f8ff",
                    100: "#e0effe",
                    200: "#bbe0fc",
                    300: "#7ec8fb",
                    400: "#3aabf6",
                    500: "#1091e7",
                    600: "#0472c5",
                    700: "#045494",
                    800: "#084e84",
                    900: "#0d416d",
                    950: "#092948",
                },
                secondary: colors.slate,
                positive: colors.emerald,
                negative: colors.red,
                warning: colors.amber,
                info: colors.blue,
            },
        },
    },
    safelist: [
        {
            pattern: /max-w-(sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl)/,
            variants: ["sm", "md", "lg", "xl", "2xl"],
        },
    ],
    plugins: [],
};
