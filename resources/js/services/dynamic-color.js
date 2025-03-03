import Color from 'color';
import {empty} from "@splidejs/splide/src/js/utils";

document.addEventListener('alpine:init', () => {
    Alpine.store('primaryColor', {
        primaryColor: '#1091e7',

        init() {
            this.fetchPrimaryColor();
            this.listenForColorUpdates();
        },

        fetchPrimaryColor() {
            fetch('/api/primary-color')
                .then(response => response.json())
                .then(data => {
                    if (data.primaryColor) {
                        this.primaryColor = data.primaryColor;
                        this.applyPrimaryColor();
                    } else {
                        this.applyPrimaryColor();
                    }
                }).catch(data => {
                this.applyPrimaryColor();
            });
        },

        listenForColorUpdates() {
            window.addEventListener('colorUpdated', () => {
                this.fetchPrimaryColor();
            });
        },
        applyPrimaryColor() {
            const primaryShades = generatePrimaryShades(this.primaryColor);
            Object.entries(primaryShades).forEach(([name, value]) => {
                document.documentElement.style.setProperty(name, value);
            });
        }
    });

});

function generatePrimaryShades(color) {
    const shades = {};
    shades[`--primary-50`] = lighten(color, 0.45);
    shades[`--primary-100`] = lighten(color, 0.4);
    shades[`--primary-200`] = lighten(color, 0.3);
    shades[`--primary-300`] = lighten(color, 0.2);
    shades[`--primary-400`] = lighten(color, 0.1);
    shades[`--primary-500`] = darken(color, 0.1);
    shades[`--primary-600`] = darken(color, 0.2);
    shades[`--primary-700`] = darken(color, 0.3);
    shades[`--primary-800`] = darken(color, 0.4);
    shades[`--primary-900`] = darken(color, 0.5);
    shades[`--primary-950`] = darken(color, 0.55);
    return shades;
}
function lighten(color, amount) {
    return Color(color).lighten(amount).hex();
}
function darken(color, amount) {
    return Color(color).darken(amount).hex();
}

