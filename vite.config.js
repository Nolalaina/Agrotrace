import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Alegreya', {
                    weights: [400, 500, 700, 800],
                    styles: ['normal', 'italic'],
                }),
                bunny('Alegreya Sans', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Alegreya Sans SC', {
                    weights: [500, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
