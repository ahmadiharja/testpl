import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/@antv/x6')) {
                        return 'vendor-x6';
                    }

                    if (id.includes('node_modules/@fullcalendar')) {
                        return 'vendor-calendar';
                    }

                    if (id.includes('node_modules/gridjs') || id.includes('node_modules/lucide')) {
                        return 'vendor-ui';
                    }
                },
            },
        },
    },
});
