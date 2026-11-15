import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        cors: true, // Or define specific origins
    },
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/css/custom.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
