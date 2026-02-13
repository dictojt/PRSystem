import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/user-panel.css',
                'resources/css/super-admin.css',
                'resources/css/approver.css',
                'resources/css/home.css',
                'resources/css/prs.css',
                'resources/js/app.js',
                'resources/js/user-panel.js',
                'resources/js/super-admin.js',
                'resources/js/approver.js',
                'resources/css/view-requests.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
